<?php
namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Services\NotificationService;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orders,
        private NotificationService $notifications,
    ) {}

    public function create()
    {
        $cart = Cart::query()->firstOrCreate(['customer_id' => auth()->id()])
            ->load('items.product.farmer', 'items.product.market');
        $slots = $cart->items
            ->flatMap(fn ($item) => $item->product->farmer->slots())
            ->pluck('label')
            ->unique()
            ->values();

        return view('orders.place', compact('cart', 'slots'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pickup_date' => ['required', 'date', 'after_or_equal:today'],
            'pickup_slot' => ['required', 'string', 'max:100'],
            'customer_note' => ['nullable', 'string', 'max:500'],
        ]);

        $cart = Cart::query()->where('customer_id', auth()->id())->firstOrFail();
        $placed = $this->orders->placeFromCart(
            $request->user(),
            $cart,
            $data['pickup_date'],
            $data['pickup_slot'],
            $data['customer_note'] ?? null
        );

        return redirect()->route('customer.orders.show', $placed[0])
            ->with('success', count($placed).' pre-order'.(count($placed) > 1 ? 's' : '').' placed. Pay the farmer when you pick up.');
    }

    public function show(Order $order)
    {
        $this->authorizeCustomer($order);
        $order->load('items.product', 'farmer.user', 'market', 'reviews');

        return view('orders.view', compact('order'));
    }

    public function history(Request $request)
    {
        $orders = Order::query()
            ->where('customer_id', auth()->id())
            ->with(['farmer', 'market', 'items'])
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->q, fn ($q, $term) => $q->where('order_number', 'like', "%$term%"))
            ->when($request->from, fn ($q, $date) => $q->whereDate('pickup_date', '>=', $date))
            ->when($request->to, fn ($q, $date) => $q->whereDate('pickup_date', '<=', $date))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('orders.history', compact('orders'));
    }

    public function update(Request $request, Order $order)
    {
        $this->authorizeCustomer($order);
        abort_unless($order->isOpenForChange(), 403, 'This order can no longer be changed.');

        $data = $request->validate([
            'pickup_date' => ['required', 'date', 'after_or_equal:today'],
            'pickup_slot' => ['required', 'string', 'max:100'],
            'customer_note' => ['nullable', 'string', 'max:500'],
        ]);

        $cutoff = $this->orders->cutoffFor($order->farmer->cutoff_hours ?? 12, $data['pickup_date'], $data['pickup_slot']);
        if (now()->gte($cutoff)) {
            return back()->withErrors(['pickup_date' => 'That slot is already past cutoff.']);
        }

        $order->update([...$data, 'cutoff_time' => $cutoff]);

        return back()->with('success', 'Order updated.');
    }

    public function cancel(Order $order)
    {
        $this->authorizeCustomer($order);
        abort_unless($order->isOpenForChange(), 403, 'This order can no longer be cancelled.');

        $this->orders->restoreStock($order);
        $order->update(['status' => 'cancelled']);
        $this->notifications->send($order->customer, 'order_cancelled', 'Order cancelled', $order->order_number.' was cancelled.', ['order_id' => $order->id]);
        if ($order->farmer->user) {
            $this->notifications->send($order->farmer->user, 'order_cancelled', 'Order cancelled', $order->buyerName().' cancelled '.$order->order_number.'.', ['order_id' => $order->id]);
        }

        return back()->with('success', 'Order cancelled and stock restored.');
    }

    public function reorder(Order $order)
    {
        $this->authorizeCustomer($order);
        $cart = Cart::query()->firstOrCreate(['customer_id' => auth()->id()]);
        $skipped = [];
        foreach ($order->items as $item) {
            $product = $item->product;
            if (! $product || ! $product->canPurchase()) {
                $skipped[] = $item->product_name;
                continue;
            }
            $qty = min($item->quantity, $product->stock_quantity);
            $row = $cart->items()->firstOrNew(['product_id' => $product->id]);
            $row->quantity = min(($row->exists ? $row->quantity : 0) + $qty, $product->stock_quantity);
            $row->save();
        }

        $message = 'Items added back to your cart.';
        if ($skipped) {
            $message .= ' Unavailable: '.implode(', ', $skipped).'.';
        }

        return redirect()->route('cart.index')->with('success', $message);
    }

    public function invoice(Order $order)
    {
        $this->authorizeCustomer($order);
        $order->load('items', 'farmer', 'market', 'customer');

        return view('orders.invoice', compact('order'));
    }

    public function guestQuick(Request $request, Product $product)
    {
        abort_unless($product->canPurchase(), 422);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);
        abort_if($data['quantity'] > $product->stock_quantity, 422);
        $slot = collect($product->farmer->slots())->pluck('label')->first() ?: '08:00-10:00';
        $pickup = now()->addDay()->toDateString();
        $placed = $this->orders->placeGuest(
            [$product->id => $data['quantity']],
            ['name' => $data['name'], 'phone' => $data['phone'], 'address' => 'Will confirm at stall'],
            $pickup,
            $slot,
            'Quick guest order'
        );

        return redirect()->route('home')->with('success', 'Order '.$placed[0]->order_number.' placed. Pay at the stall in Rs. Pickup '.$pickup.' · '.$slot);
    }

    public function guestCreate()
    {
        $lines = array_map('intval', session('ml_guest_cart', []));
        $products = Product::query()->with('farmer')->whereIn('id', array_keys($lines))->get();
        $slots = $products->flatMap(fn ($product) => $product->farmer->slots())->pluck('label')->unique()->values();

        return view('orders.guest', compact('products', 'lines', 'slots'));
    }

    public function guestStore(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
            'pickup_date' => ['required', 'date', 'after_or_equal:today'],
            'pickup_slot' => ['required', 'string', 'max:100'],
            'customer_note' => ['nullable', 'string', 'max:500'],
        ]);
        $lines = array_map('intval', session('ml_guest_cart', []));
        $placed = $this->orders->placeGuest($lines, [
            'name' => $data['name'],
            'phone' => $data['phone'],
            'address' => $data['address'],
        ], $data['pickup_date'], $data['pickup_slot'], $data['customer_note'] ?? null);
        session()->forget('ml_guest_cart');

        return redirect()->route('home')->with('success', 'Guest pre-order '.$placed[0]->order_number.' is placed. Pay the farmer at the stall. No account was created.');
    }

    private function authorizeCustomer(Order $order): void
    {
        abort_unless($order->customer_id === auth()->id() || auth()->user()->isAdmin(), 403);
    }
}
