<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Category;
use App\Models\ChatbotFaq;
use App\Models\FarmerProfile;
use App\Models\Market;
use App\Models\Order;
use App\Models\Product;
use App\Models\Report;
use App\Models\Review;
use App\Models\Setting;
use App\Models\User;
use App\Services\NotificationService;
use App\Support\ImageStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminDashboardController extends Controller
{
    public function __construct(private NotificationService $notifications) {}

    public function index()
    {
        $revenue = Order::query()->where('status', 'completed')
            ->select(DB::raw('DATE(created_at) as day'), DB::raw('SUM(total_amount) as total'))
            ->where('created_at', '>=', now()->subDays(14))
            ->groupBy('day')->orderBy('day')->get();

        $topFarmers = FarmerProfile::query()
            ->withCount('orders')
            ->orderByDesc('orders_count')
            ->take(5)->get();

        $growth = User::query()
            ->select(DB::raw('DATE(created_at) as day'), DB::raw('COUNT(*) as total'))
            ->where('created_at', '>=', now()->subDays(14))
            ->groupBy('day')->orderBy('day')->get();

        return view('admin.dashboard', [
            'metrics' => [
                'farmers' => FarmerProfile::count(),
                'approved' => FarmerProfile::where('approval_status', 'approved')->count(),
                'pending' => FarmerProfile::where('approval_status', 'pending')->count(),
                'rejected' => FarmerProfile::where('approval_status', 'rejected')->count(),
                'customers' => User::where('role', 'customer')->count(),
                'markets' => Market::count(),
                'products' => Product::count(),
                'orders' => Order::count(),
                'pending_orders' => Order::where('status', 'placed')->count(),
                'completed' => Order::where('status', 'completed')->count(),
                'revenue' => Order::where('status', 'completed')->sum('total_amount'),
            ],
            'byStatus' => Order::query()->select('status', DB::raw('COUNT(*) as total'))->groupBy('status')->pluck('total', 'status'),
            'ordersOverTime' => Order::query()
                ->select(DB::raw('DATE(created_at) as day'), DB::raw('COUNT(*) as total'))
                ->where('created_at', '>=', now()->subDays(14))
                ->groupBy('day')->orderBy('day')->get(),
            'revenue' => $revenue,
            'topFarmers' => $topFarmers,
            'topProducts' => DB::table('order_items')
                ->select('product_name', DB::raw('SUM(quantity) as qty'))
                ->groupBy('product_name')->orderByDesc('qty')->limit(5)->get(),
            'growth' => $growth,
            'activity' => Order::query()->with(['customer', 'farmer', 'market'])->latest()->take(8)->get(),
            'recentFarmers' => FarmerProfile::query()->with('user')->latest()->take(5)->get(),
            'recentReviews' => Review::query()->with(['customer', 'product', 'farmer'])->latest()->take(5)->get(),
        ]);
    }

    public function users(Request $request)
    {
        $users = User::query()->where('role', 'customer')
            ->when($request->q, fn ($q, $t) => $q->where(fn ($w) => $w->where('name', 'like', "%$t%")->orWhere('email', 'like', "%$t%")))
            ->latest()->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function toggleUser(User $user)
    {
        abort_unless($user->role === 'customer', 403);
        $user->update(['status' => $user->status === 'active' ? 'inactive' : 'active']);

        return back()->with('success', 'Customer status updated.');
    }

    public function farmers(Request $request)
    {
        $farmers = FarmerProfile::query()->with('user')
            ->when($request->status, fn ($q, $status) => $q->where('approval_status', $status))
            ->when($request->q, fn ($q, $term) => $q->where('stall_name', 'like', "%$term%"))
            ->latest()->paginate(15)->withQueryString();

        return view('admin.farmers.index', compact('farmers'));
    }

    public function showFarmer(FarmerProfile $farmer)
    {
        $farmer->load(['user', 'markets', 'products.category', 'orders', 'reviews.customer']);

        return view('admin.farmers.show', compact('farmer'));
    }

    public function showUser(User $user)
    {
        abort_if($user->role === 'admin', 404);
        $user->load(['orders.farmer', 'orders.market', 'farmerProfile']);

        return view('admin.users.show', compact('user'));
    }

    public function setUserStatus(Request $request, User $user)
    {
        abort_unless($user->role === 'customer', 403);
        $status = $request->validate(['status' => ['required', 'in:active,inactive,suspended']])['status'];
        $user->update(['status' => $status]);

        return back()->with('success', 'Customer status updated.');
    }

    public function setUserPin(Request $request, User $user)
    {
        abort_if($user->role === 'admin', 403);
        $data = $request->validate(['pin' => ['required', 'digits:4', 'confirmed']]);
        $user->update(['password' => $data['pin']]);

        return back()->with('success', 'PIN updated. It is not shown again.');
    }

    public function setOwnPin(Request $request)
    {
        $data = $request->validate([
            'current_pin' => ['required', 'digits:4'],
            'pin' => ['required', 'digits:4', 'confirmed'],
        ]);
        abort_unless(\Illuminate\Support\Facades\Hash::check($data['current_pin'], $request->user()->password), 422);
        $request->user()->update(['password' => $data['pin']]);

        return back()->with('success', 'Your PIN was changed.');
    }

    public function setPayment(Request $request, Order $order)
    {
        $status = $request->validate(['payment_status' => ['required', 'in:unpaid,paid']])['payment_status'];
        $order->update(['payment_status' => $status]);

        return back()->with('success', 'Payment status updated. This only records in-person payment.');
    }

    public function orders(Request $request)
    {
        $orders = Order::query()->with(['customer', 'farmer', 'market'])
            ->when($request->q, fn ($q, $term) => $q->where('order_number', 'like', "%$term%"))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->market, fn ($q, $id) => $q->where('market_id', $id))
            ->when($request->farmer, fn ($q, $id) => $q->where('farmer_id', $id))
            ->when($request->from, fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
            ->when($request->to, fn ($q, $date) => $q->whereDate('created_at', '<=', $date))
            ->latest()->paginate(20)->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'markets' => Market::orderBy('name')->get(),
            'farmers' => FarmerProfile::orderBy('stall_name')->get(),
        ]);
    }

    public function showOrder(Order $order)
    {
        $order->load(['customer', 'farmer.user', 'market', 'items', 'reviews']);

        return view('admin.orders.show', compact('order'));
    }

    public function decideFarmer(Request $request, FarmerProfile $farmer)
    {
        $status = $request->validate(['approval_status' => ['required', 'in:approved,rejected,pending']])['approval_status'];
        $userStatus = match ($status) {
            'approved' => 'active',
            'rejected' => 'inactive',
            default => 'pending',
        };
        $farmer->update(['approval_status' => $status]);
        $farmer->user->update(['status' => $userStatus]);
        $this->notifications->send(
            $farmer->user,
            'farmer_'.$status,
            'Stall '.$status,
            'Your stall '.$farmer->stall_name.' is now '.$status.'.',
            ['farmer_id' => $farmer->id]
        );

        return back()->with('success', 'Farmer marked '.$status.'.');
    }

    public function updateFarmer(Request $request, FarmerProfile $farmer)
    {
        $data = $request->validate([
            'stall_name' => ['required', 'string', 'max:100'],
            'contact_person' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'business_description' => ['nullable', 'string', 'max:2000'],
            'operating_days' => ['nullable', 'array'],
            'operating_days.*' => ['in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday'],
        ]);
        $farmer->update([
            'stall_name' => $data['stall_name'],
            'contact_person' => $data['contact_person'],
            'address' => $data['address'] ?? null,
            'business_description' => $data['business_description'] ?? null,
            'operating_days' => $data['operating_days'] ?? [],
        ]);
        $farmer->user->update([
            'name' => $data['contact_person'],
            'phone' => $data['phone'],
            'address' => $data['address'] ?? $farmer->user->address,
        ]);

        return back()->with('success', 'Farmer details updated.');
    }

    public function suspendFarmer(FarmerProfile $farmer)
    {
        $farmer->user->update(['status' => $farmer->user->status === 'suspended' ? 'active' : 'suspended']);

        return back()->with('success', 'Farmer account updated.');
    }

    public function destroyFarmer(FarmerProfile $farmer)
    {
        $stall = $farmer->stall_name;
        $user = $farmer->user;
        $farmer->delete();
        $user?->delete();

        return redirect()->route('admin.farmers.index')->with('success', $stall.' was deleted.');
    }

    public function markets()
    {
        return view('admin.markets.index', ['markets' => Market::query()->latest()->paginate(12)]);
    }

    public function storeMarket(Request $request)
    {
        Market::query()->create($this->marketData($request));

        return back()->with('success', 'Market created.');
    }

    public function updateMarket(Request $request, Market $market)
    {
        $market->update($this->marketData($request, $market));

        return back()->with('success', 'Market updated.');
    }

    public function destroyMarket(Market $market)
    {
        $market->delete();

        return back()->with('success', 'Market deleted.');
    }

    public function categories()
    {
        return view('admin.categories.index', ['categories' => Category::query()->withCount('products')->orderBy('name')->get()]);
    }

    public function storeCategory(Request $request)
    {
        Category::query()->create($request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name'],
            'description' => ['nullable', 'string', 'max:500'],
            'icon' => ['nullable', 'string', 'max:50'],
        ]));

        return back()->with('success', 'Category added.');
    }

    public function updateCategory(Request $request, Category $category)
    {
        $category->update($request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name,'.$category->id],
            'description' => ['nullable', 'string', 'max:500'],
            'icon' => ['nullable', 'string', 'max:50'],
        ]));

        return back()->with('success', 'Category updated.');
    }

    public function destroyCategory(Category $category)
    {
        abort_if($category->products()->exists(), 422, 'Move products before deleting this category.');
        $category->delete();

        return back()->with('success', 'Category deleted.');
    }

    public function products(Request $request)
    {
        $products = Product::query()->with(['farmer', 'category', 'market'])
            ->when($request->q, fn ($q, $t) => $q->where('name', 'like', "%$t%"))
            ->latest()->paginate(15)->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    public function updateProduct(Request $request, Product $product)
    {
        $product->update($request->validate([
            'name' => ['required', 'string', 'max:150'],
            'price' => ['required', 'numeric', 'min:0'],
            'quality' => ['required', 'in:premium,fresh,standard'],
            'description' => ['nullable', 'string', 'max:2000'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'is_available' => ['sometimes', 'boolean'],
            'is_featured' => ['sometimes', 'boolean'],
        ]) + [
            'is_available' => $request->boolean('is_available'),
            'is_featured' => $request->boolean('is_featured'),
        ]);

        return back()->with('success', 'Product moderated.');
    }

    public function destroyProduct(Product $product)
    {
        $product->delete();

        return back()->with('success', 'Product removed.');
    }

    public function reviews()
    {
        $reviews = Review::query()->with(['customer', 'farmer', 'product'])->latest()->paginate(15);

        return view('admin.reviews.index', compact('reviews'));
    }

    public function moderateReview(Request $request, Review $review)
    {
        $review->update(['status' => $request->validate(['status' => ['required', 'in:approved,rejected,pending']])['status']]);

        return back()->with('success', 'Review updated.');
    }

    public function destroyReview(Review $review)
    {
        $review->delete();

        return back()->with('success', 'Review deleted.');
    }

    public function reports(Request $request)
    {
        $from = $request->date('from') ?? now()->subDays(30);
        $to = $request->date('to') ?? now();

        $orders = Order::query()->whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()]);
        $payload = [
            'orders' => (clone $orders)->count(),
            'revenue' => (clone $orders)->where('status', 'completed')->sum('total_amount'),
            'by_market' => Market::query()->withSum(['orders as revenue' => fn ($q) => $q->whereBetween('created_at', [$from, $to])->where('status', 'completed')], 'total_amount')->get(),
            'farmers' => FarmerProfile::query()->withCount(['orders' => fn ($q) => $q->whereBetween('created_at', [$from, $to])])->orderByDesc('orders_count')->take(8)->get(),
            'products' => Product::query()->withSum(['orderItems as sold' => fn ($q) => $q->whereBetween('created_at', [$from, $to])], 'quantity')->orderByDesc('sold')->take(8)->get(),
            'growth' => User::query()->select(DB::raw('DATE(created_at) as day'), DB::raw('COUNT(*) as total'))
                ->whereBetween('created_at', [$from, $to])->groupBy('day')->orderBy('day')->get(),
        ];

        Report::query()->create([
            'report_type' => 'platform_summary',
            'generated_by' => auth()->id(),
            'data' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
                'orders' => $payload['orders'],
                'revenue' => $payload['revenue'],
            ],
            'generated_at' => now(),
        ]);

        return view('admin.reports.index', compact('payload', 'from', 'to'));
    }

    public function exportReports(Request $request): StreamedResponse
    {
        $from = $request->date('from') ?? now()->subDays(30);
        $to = $request->date('to') ?? now();
        $rows = Order::query()->with(['customer', 'farmer', 'market'])
            ->whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()])
            ->orderBy('id')->get();

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Order', 'Date', 'Customer', 'Farmer', 'Market', 'Status', 'Total']);
            foreach ($rows as $order) {
                fputcsv($out, [
                    $order->order_number,
                    $order->created_at->toDateTimeString(),
                    $order->customer->name,
                    $order->farmer->stall_name,
                    $order->market->name,
                    $order->status,
                    $order->total_amount,
                ]);
            }
            fclose($out);
        }, 'marketlink-orders.csv', ['Content-Type' => 'text/csv']);
    }

    public function announcements()
    {
        return view('admin.announcements.index', [
            'announcements' => Announcement::query()->with('publisher')->latest()->paginate(12),
        ]);
    }

    public function storeAnnouncement(Request $request)
    {
        $data = $this->announcementData($request);
        $data['published_by'] = auth()->id();
        Announcement::query()->create($data);

        return back()->with('success', 'Announcement saved.');
    }

    public function updateAnnouncement(Request $request, Announcement $announcement)
    {
        $announcement->update($this->announcementData($request));

        return back()->with('success', 'Announcement updated.');
    }

    public function destroyAnnouncement(Announcement $announcement)
    {
        $announcement->delete();

        return back()->with('success', 'Announcement deleted.');
    }

    public function faqs()
    {
        return view('admin.faqs.index', ['faqs' => ChatbotFaq::query()->latest()->get()]);
    }

    public function storeFaq(Request $request)
    {
        $data = $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string'],
            'category' => ['nullable', 'string', 'max:100'],
            'keywords' => ['nullable', 'string', 'max:255'],
        ]);
        $data['keywords'] = array_values(array_filter(array_map('trim', explode(',', $data['keywords'] ?? ''))));
        ChatbotFaq::query()->create($data);

        return back()->with('success', 'FAQ added.');
    }

    public function destroyFaq(ChatbotFaq $faq)
    {
        $faq->delete();

        return back()->with('success', 'FAQ removed.');
    }

    public function settings()
    {
        $keys = ['platform_name', 'contact_email', 'contact_phone', 'contact_address', 'facebook', 'instagram', 'tagline'];
        $settings = [];
        foreach ($keys as $key) {
            $settings[$key] = Setting::getValue($key, '');
        }

        return view('admin.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'platform_name' => ['required', 'string', 'max:100'],
            'contact_email' => ['required', 'email'],
            'contact_phone' => ['required', 'string', 'max:30'],
            'contact_address' => ['required', 'string', 'max:255'],
            'facebook' => ['nullable', 'url'],
            'instagram' => ['nullable', 'url'],
            'tagline' => ['nullable', 'string', 'max:180'],
        ]);
        Setting::putMany($data);

        return back()->with('success', 'Settings saved.');
    }

    private function marketData(Request $request, ?Market $market = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'address' => ['required', 'string'],
            'city' => ['required', 'string', 'max:100'],
            'operating_days' => ['required', 'array', 'min:1'],
            'opening_time' => ['required', 'date_format:H:i'],
            'closing_time' => ['required', 'date_format:H:i'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'status' => ['required', 'in:active,inactive'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
        ]);
        $data['map_provider'] = 'OpenStreetMap';
        $data['image'] = ImageStore::put($request->file('image'), 'markets', $market?->image);

        return $data;
    }

    private function announcementData(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'status' => ['required', 'in:draft,published,archived'],
            'priority' => ['required', 'in:low,medium,high'],
            'published_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after:published_at'],
        ]);
        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        return $data;
    }
}
