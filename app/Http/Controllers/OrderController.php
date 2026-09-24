<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function checkout()
    {
        $cart = Cart::with(['items.product.farmer', 'items.product.market'])
            ->where('customer_id', auth()->id())
            ->first();

        if (! $cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Group by farmer — one order per farmer
        $groups = $cart->items->groupBy(fn ($item) => $item->product->farmer_id);

        return view('orders.checkout', compact('cart', 'groups'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pickup_date' => 'required|date|after_or_equal:today',
            'pickup_slot' => 'required|string|max:100',
            'customer_note' => 'nullable|string|max:1000',
        ]);

        $cart = Cart::with(['items.product.farmer', 'items.product.market'])
            ->where('customer_id', auth()->id())
            ->firstOrFail();

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Validate stock before committing
        foreach ($cart->items as $item) {
            $product = $item->product->fresh();
            if (! $product->isInStock() || $item->quantity > $product->stock_quantity) {
                return back()->with('error', "Insufficient stock for {$product->name}.");
            }
            if (! $product->farmer->isApproved()) {
                return back()->with('error', "Farmer for {$product->name} is not approved.");
            }
        }

        $orders = DB::transaction(function () use ($cart, $data) {
            $created = [];
            $groups = $cart->items->groupBy(fn ($item) => $item->product->farmer_id);

            foreach ($groups as $farmerId => $items) {
                $first = $items->first()->product;
                $total = $items->sum(fn ($i) => $i->quantity * $i->product->price);

                // Cutoff: day before pickup at 18:00 or 2 hours before if same day
                $pickupDate = \Carbon\Carbon::parse($data['pickup_date']);
                $cutoff = $pickupDate->copy()->subDay()->setTime(18, 0);
                if ($pickupDate->isToday()) {
                    $cutoff = now()->addHours(2);
                }

                $order = Order::create([
                    'order_number' => Order::generateOrderNumber(),
                    'customer_id' => auth()->id(),
                    'farmer_id' => $farmerId,
                    'market_id' => $first->market_id,
                    'pickup_date' => $data['pickup_date'],
                    'pickup_slot' => $data['pickup_slot'],
                    'status' => 'placed',
                    'total_amount' => $total,
                    'customer_note' => $data['customer_note'] ?? null,
                    'cutoff_time' => $cutoff,
                ]);

                foreach ($items as $item) {
                    $product = $item->product;
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'unit_price' => $product->price,
                        'quantity' => $item->quantity,
                        'subtotal' => $item->quantity * $product->price,
                    ]);

                    $product->decrement('stock_quantity', $item->quantity);
                    $product->syncStockFlags();
                }

                AppNotification::notify(
                    $first->farmer->user,
                    'order_placed',
                    'New pre-order received',
                    "Order {$order->order_number} was placed for pickup on {$order->pickup_date->format('M d, Y')}.",
                    ['order_id' => $order->id]
                );

                $created[] = $order;
            }

            $cart->items()->delete();

            return $created;
        });

        $first = $orders[0];

        return redirect()
            ->route('customer.orders.show', $first)
            ->with('success', count($orders) > 1
                ? count($orders).' pre-orders placed successfully (one per farmer).'
                : 'Pre-order placed successfully!');
    }

    public function show(Order $order)
    {
        $this->authorizeView($order);
        $order->load(['items.product', 'farmer.user', 'market', 'customer', 'reviews']);

        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        abort_unless($order->customer_id === auth()->id() && $order->canModify(), 403);
        $order->load(['items.product', 'farmer', 'market']);

        return view('orders.edit', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        abort_unless($order->customer_id === auth()->id() && $order->canModify(), 403);

        $data = $request->validate([
            'pickup_date' => 'required|date|after_or_equal:today',
            'pickup_slot' => 'required|string|max:100',
            'customer_note' => 'nullable|string|max:1000',
        ]);

        $order->update($data);

        AppNotification::notify(
            $order->farmer->user,
            'order_updated',
            'Order updated',
            "Customer updated order {$order->order_number}.",
            ['order_id' => $order->id]
        );

        return redirect()->route('customer.orders.show', $order)->with('success', 'Order updated.');
    }

    public function cancel(Order $order)
    {
        abort_unless($order->customer_id === auth()->id() && $order->canCancel(), 403);

        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->increment('stock_quantity', $item->quantity);
                    $product->update(['is_sold_out' => false, 'is_available' => true]);
                }
            }
            $order->update(['status' => 'cancelled']);
        });

        AppNotification::notify(
            $order->farmer->user,
            'order_cancelled',
            'Order cancelled',
            "Order {$order->order_number} was cancelled by the customer.",
            ['order_id' => $order->id]
        );

        return redirect()->route('customer.orders.show', $order)->with('success', 'Order cancelled and stock restored.');
    }

    public function reorder(Order $order)
    {
        abort_unless($order->customer_id === auth()->id(), 403);

        $cart = Cart::firstOrCreate(['customer_id' => auth()->id()]);
        $added = 0;

        foreach ($order->items as $item) {
            $product = Product::find($item->product_id);
            if (! $product || ! $product->isInStock()) {
                continue;
            }
            $qty = min($item->quantity, $product->stock_quantity);
            $existing = $cart->items()->where('product_id', $product->id)->first();
            if ($existing) {
                $existing->update(['quantity' => min($existing->quantity + $qty, $product->stock_quantity)]);
            } else {
                $cart->items()->create(['product_id' => $product->id, 'quantity' => $qty]);
            }
            $added++;
        }

        return redirect()->route('cart.index')->with(
            $added ? 'success' : 'error',
            $added ? "{$added} item(s) added to cart from previous order." : 'No items could be re-added (out of stock).'
        );
    }

    public function invoice(Order $order)
    {
        $this->authorizeView($order);
        $order->load(['items', 'farmer.user', 'market', 'customer']);

        return view('orders.invoice', compact('order'));
    }

    protected function authorizeView(Order $order): void
    {
        $user = auth()->user();
        $allowed = $order->customer_id === $user->id
            || ($user->isFarmer() && $user->farmerProfile?->id === $order->farmer_id)
            || $user->isAdmin();

        abort_unless($allowed, 403);
    }
}
