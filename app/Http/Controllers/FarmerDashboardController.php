<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Category;
use App\Models\Market;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class FarmerDashboardController extends Controller
{
    public function index()
    {
        $profile = auth()->user()->farmerProfile;
        abort_unless($profile, 404);

        $orderStats = Order::where('farmer_id', $profile->id)
            ->selectRaw("status, COUNT(*) as total")
            ->groupBy('status')
            ->pluck('total', 'status');

        $revenue = Order::where('farmer_id', $profile->id)
            ->where('status', 'completed')
            ->sum('total_amount');

        $recentOrders = Order::with(['customer', 'market'])
            ->where('farmer_id', $profile->id)
            ->latest()
            ->take(5)
            ->get();

        $lowStock = Product::where('farmer_id', $profile->id)
            ->where('stock_quantity', '<=', 5)
            ->orderBy('stock_quantity')
            ->take(5)
            ->get();

        $salesByDay = Order::where('farmer_id', $profile->id)
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(14))
            ->selectRaw('DATE(created_at) as day, SUM(total_amount) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $topProducts = Product::where('farmer_id', $profile->id)
            ->withCount('orderItems')
            ->orderByDesc('order_items_count')
            ->take(5)
            ->get();

        return view('farmer.dashboard', compact(
            'profile', 'orderStats', 'revenue', 'recentOrders', 'lowStock', 'salesByDay', 'topProducts'
        ));
    }

    public function profile()
    {
        $profile = auth()->user()->farmerProfile()->with('markets')->firstOrFail();
        $markets = Market::active()->orderBy('name')->get();

        return view('farmer.profile.edit', compact('profile', 'markets'));
    }

    public function updateProfile(Request $request)
    {
        $profile = auth()->user()->farmerProfile;
        abort_unless($profile, 404);

        $data = $request->validate([
            'stall_name' => 'required|string|max:100',
            'contact_person' => 'required|string|max:100',
            'business_description' => 'nullable|string|max:2000',
            'operating_days' => 'nullable|array',
            'address' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'market_ids' => 'nullable|array',
            'market_ids.*' => 'exists:markets,id',
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $profile->update(collect($data)->only([
            'stall_name', 'contact_person', 'business_description', 'operating_days',
            'address', 'latitude', 'longitude', 'logo',
        ])->toArray());

        auth()->user()->update([
            'name' => $data['name'],
            'phone' => $data['phone'] ?? null,
        ]);

        if (isset($data['market_ids'])) {
            $sync = [];
            foreach ($data['market_ids'] as $marketId) {
                $sync[$marketId] = [
                    'stall_number' => $request->input("stall_number.$marketId"),
                    'operating_day' => $request->input("operating_day.$marketId"),
                    'pickup_notes' => $request->input("pickup_notes.$marketId"),
                ];
            }
            $profile->markets()->sync($sync);
        }

        return back()->with('success', 'Profile updated.');
    }

    public function products()
    {
        $profile = auth()->user()->farmerProfile;
        $products = Product::with(['category', 'market'])
            ->where('farmer_id', $profile->id)
            ->latest()
            ->paginate(12);

        return view('farmer.products.index', compact('products', 'profile'));
    }

    public function createProduct()
    {
        $profile = auth()->user()->farmerProfile;
        abort_unless($profile->isApproved(), 403);

        return view('farmer.products.create', [
            'categories' => Category::orderBy('name')->get(),
            'markets' => $profile->markets()->orderBy('name')->get() ?: Market::active()->get(),
            'profile' => $profile,
        ]);
    }

    public function storeProduct(Request $request)
    {
        $profile = auth()->user()->farmerProfile;
        abort_unless($profile->isApproved(), 403);

        $data = $this->validateProduct($request);
        $data['farmer_id'] = $profile->id;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $data['is_sold_out'] = ($data['stock_quantity'] ?? 0) <= 0;
        $data['weekly_stock_template'] = $data['stock_quantity'] ?? 0;

        Product::create($data);

        return redirect()->route('farmer.products.index')->with('success', 'Product created.');
    }

    public function editProduct(Product $product)
    {
        $this->authorizeProduct($product);
        $profile = auth()->user()->farmerProfile;

        return view('farmer.products.edit', [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(),
            'markets' => $profile->markets()->orderBy('name')->get() ?: Market::active()->get(),
            'profile' => $profile,
        ]);
    }

    public function updateProduct(Request $request, Product $product)
    {
        $this->authorizeProduct($product);
        $data = $this->validateProduct($request);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $data['is_sold_out'] = ($data['stock_quantity'] ?? 0) <= 0
            || $request->boolean('is_sold_out');
        $data['is_available'] = $request->boolean('is_available') && ! $data['is_sold_out'];

        $product->update($data);

        return redirect()->route('farmer.products.index')->with('success', 'Product updated.');
    }

    public function destroyProduct(Product $product)
    {
        $this->authorizeProduct($product);
        $product->delete();

        return back()->with('success', 'Product deleted.');
    }

    public function copyWeeklyStock()
    {
        $profile = auth()->user()->farmerProfile;
        abort_unless($profile->isApproved(), 403);

        $updated = 0;
        Product::where('farmer_id', $profile->id)
            ->whereNotNull('weekly_stock_template')
            ->each(function (Product $product) use (&$updated) {
                $product->update([
                    'stock_quantity' => $product->weekly_stock_template,
                    'is_sold_out' => false,
                    'is_available' => true,
                ]);
                $updated++;
            });

        return back()->with('success', "Weekly stock template applied to {$updated} product(s).");
    }

    public function orders(Request $request)
    {
        $profile = auth()->user()->farmerProfile;
        $query = Order::with(['customer', 'market', 'items'])
            ->where('farmer_id', $profile->id);

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        return view('farmer.orders.index', compact('orders', 'profile'));
    }

    public function showOrder(Order $order)
    {
        $this->authorizeOrder($order);
        $order->load(['customer', 'market', 'items.product']);

        return view('farmer.orders.show', compact('order'));
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $this->authorizeOrder($order);

        $data = $request->validate([
            'status' => 'required|in:accepted,declined,ready_for_pickup,completed',
            'farmer_notes' => 'nullable|string|max:1000',
        ]);

        $allowed = match ($order->status) {
            'placed' => ['accepted', 'declined'],
            'accepted' => ['ready_for_pickup', 'declined'],
            'ready_for_pickup' => ['completed'],
            default => [],
        };

        if (! in_array($data['status'], $allowed, true)) {
            return back()->with('error', 'Invalid status transition.');
        }

        DB::transaction(function () use ($order, $data) {
            if ($data['status'] === 'declined') {
                foreach ($order->items as $item) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->increment('stock_quantity', $item->quantity);
                        $product->update(['is_sold_out' => false, 'is_available' => true]);
                    }
                }
            }

            $order->update([
                'status' => $data['status'],
                'farmer_notes' => $data['farmer_notes'] ?? $order->farmer_notes,
            ]);
        });

        AppNotification::notify(
            $order->customer,
            'order_status',
            'Order '.$order->statusLabel(),
            "Your order {$order->order_number} is now: ".$order->fresh()->statusLabel().'.',
            ['order_id' => $order->id]
        );

        return back()->with('success', 'Order status updated.');
    }

    public function reviews()
    {
        $profile = auth()->user()->farmerProfile;
        $reviews = Review::with(['customer', 'product'])
            ->where('farmer_id', $profile->id)
            ->latest()
            ->paginate(15);

        return view('farmer.reviews.index', compact('reviews'));
    }

    public function replyReview(Request $request, Review $review)
    {
        abort_unless($review->farmer_id === auth()->user()->farmerProfile->id, 403);

        $data = $request->validate(['farmer_reply' => 'required|string|max:1000']);
        $review->update($data);

        return back()->with('success', 'Reply posted.');
    }

    public function insights()
    {
        $profile = auth()->user()->farmerProfile;

        $productPerformance = Product::where('farmer_id', $profile->id)
            ->withCount('orderItems')
            ->withSum('orderItems as units_sold', 'quantity')
            ->orderByDesc('units_sold')
            ->get();

        $monthly = Order::where('farmer_id', $profile->id)
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subMonths(6))
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, SUM(total_amount) as total, COUNT(*) as orders")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('farmer.insights', compact('productPerformance', 'monthly', 'profile'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        if (! Hash::check($request->current_password, auth()->user()->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        auth()->user()->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password changed.');
    }

    protected function validateProduct(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string|max:5000',
            'price' => 'required|numeric|min:0.01|max:999999',
            'unit' => 'required|in:kg,gram,dozen,bunch,litre,piece,pack',
            'stock_quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'market_id' => 'required|exists:markets,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'is_available' => 'sometimes|boolean',
            'is_sold_out' => 'sometimes|boolean',
            'is_featured' => 'sometimes|boolean',
            'weekly_stock_template' => 'nullable|integer|min:0',
        ]) + [
            'is_available' => $request->boolean('is_available', true),
            'is_featured' => $request->boolean('is_featured'),
        ];
    }

    protected function authorizeProduct(Product $product): void
    {
        abort_unless($product->farmer_id === auth()->user()->farmerProfile->id, 403);
    }

    protected function authorizeOrder(Order $order): void
    {
        abort_unless($order->farmer_id === auth()->user()->farmerProfile->id, 403);
    }
}
