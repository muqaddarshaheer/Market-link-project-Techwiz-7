<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\AppNotification;
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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'customers' => User::where('role', 'customer')->count(),
            'farmers' => FarmerProfile::count(),
            'pending_farmers' => FarmerProfile::where('approval_status', 'pending')->count(),
            'markets' => Market::count(),
            'products' => Product::count(),
            'orders' => Order::count(),
            'revenue' => Order::where('status', 'completed')->sum('total_amount'),
            'reviews' => Review::count(),
        ];

        $ordersByStatus = Order::selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');
        $revenueByDay = Order::where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(14))
            ->selectRaw('DATE(created_at) as day, SUM(total_amount) as total')
            ->groupBy('day')->orderBy('day')->get();
        $recentOrders = Order::with(['customer', 'farmer'])->latest()->take(8)->get();
        $pendingFarmers = FarmerProfile::with('user')->where('approval_status', 'pending')->latest()->take(5)->get();
        $growth = User::where('created_at', '>=', now()->subMonths(6))
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, role, COUNT(*) as total")
            ->groupBy('month', 'role')->orderBy('month')->get();

        return view('admin.dashboard', compact(
            'stats', 'ordersByStatus', 'revenueByDay', 'recentOrders', 'pendingFarmers', 'growth'
        ));
    }

    // —— Users ——
    public function users(Request $request)
    {
        $query = User::with('farmerProfile')->latest();

        if ($role = $request->string('role')->toString()) {
            $query->where('role', $role);
        }
        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }
        if ($q = $request->string('q')->toString()) {
            $query->where(function ($qb) use ($q) {
                $qb->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%");
            });
        }

        $users = $query->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function updateUserStatus(Request $request, User $user)
    {
        $data = $request->validate(['status' => 'required|in:active,inactive,suspended,pending']);

        if ($user->isAdmin() && $data['status'] !== 'active') {
            return back()->with('error', 'Cannot deactivate the admin account.');
        }

        $user->update(['status' => $data['status']]);

        return back()->with('success', 'User status updated.');
    }

    // —— Farmers ——
    public function farmers(Request $request)
    {
        $query = FarmerProfile::with('user')->latest();

        if ($status = $request->string('approval_status')->toString()) {
            $query->where('approval_status', $status);
        }

        $farmers = $query->paginate(15)->withQueryString();

        return view('admin.farmers.index', compact('farmers'));
    }

    public function approveFarmer(FarmerProfile $farmer)
    {
        $farmer->update(['approval_status' => 'approved', 'rejection_reason' => null]);
        $farmer->user->update(['status' => 'active']);

        AppNotification::notify(
            $farmer->user,
            'farmer_approved',
            'Account approved!',
            'Your farmer stall has been approved. You can now list products and receive orders.',
            ['farmer_id' => $farmer->id]
        );

        return back()->with('success', 'Farmer approved.');
    }

    public function rejectFarmer(Request $request, FarmerProfile $farmer)
    {
        $data = $request->validate(['rejection_reason' => 'nullable|string|max:500']);
        $farmer->update([
            'approval_status' => 'rejected',
            'rejection_reason' => $data['rejection_reason'] ?? 'Did not meet marketplace guidelines.',
        ]);

        AppNotification::notify(
            $farmer->user,
            'farmer_rejected',
            'Registration not approved',
            $farmer->rejection_reason,
            ['farmer_id' => $farmer->id]
        );

        return back()->with('success', 'Farmer rejected.');
    }

    // —— Markets ——
    public function markets()
    {
        $markets = Market::withCount(['products', 'farmers'])->latest()->paginate(15);

        return view('admin.markets.index', compact('markets'));
    }

    public function createMarket()
    {
        return view('admin.markets.create');
    }

    public function storeMarket(Request $request)
    {
        $data = $this->validateMarket($request);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('markets', 'public');
        }
        Market::create($data);

        return redirect()->route('admin.markets.index')->with('success', 'Market created.');
    }

    public function editMarket(Market $market)
    {
        return view('admin.markets.edit', compact('market'));
    }

    public function updateMarket(Request $request, Market $market)
    {
        $data = $this->validateMarket($request);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('markets', 'public');
        }
        $market->update($data);

        return redirect()->route('admin.markets.index')->with('success', 'Market updated.');
    }

    public function destroyMarket(Market $market)
    {
        $market->delete();

        return back()->with('success', 'Market deleted.');
    }

    // —— Categories ——
    public function categories()
    {
        $categories = Category::withCount('products')->orderBy('name')->paginate(20);

        return view('admin.categories.index', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:50',
        ]);
        Category::create($data);

        return back()->with('success', 'Category created.');
    }

    public function updateCategory(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,'.$category->id,
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:50',
        ]);
        $category->update($data);

        return back()->with('success', 'Category updated.');
    }

    public function destroyCategory(Category $category)
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'Cannot delete a category that has products.');
        }
        $category->delete();

        return back()->with('success', 'Category deleted.');
    }

    // —— Products ——
    public function products(Request $request)
    {
        $query = Product::with(['farmer', 'market', 'category'])->latest();
        if ($request->filled('q')) {
            $query->where('name', 'like', '%'.$request->string('q').'%');
        }
        $products = $query->paginate(20)->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    public function toggleProduct(Product $product)
    {
        $product->update(['is_available' => ! $product->is_available]);

        return back()->with('success', 'Product availability toggled.');
    }

    public function destroyProduct(Product $product)
    {
        $product->delete();

        return back()->with('success', 'Product removed.');
    }

    // —— Reviews ——
    public function reviews(Request $request)
    {
        $query = Review::with(['customer', 'farmer', 'product'])->latest();
        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }
        $reviews = $query->paginate(20)->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function moderateReview(Request $request, Review $review)
    {
        $data = $request->validate(['status' => 'required|in:pending,approved,rejected']);
        $review->update($data);

        return back()->with('success', 'Review moderated.');
    }

    // —— Reports ——
    public function reports()
    {
        $orderReport = [
            'total' => Order::count(),
            'completed' => Order::where('status', 'completed')->count(),
            'revenue' => Order::where('status', 'completed')->sum('total_amount'),
            'avg_order' => Order::where('status', 'completed')->avg('total_amount'),
        ];

        $farmerReport = FarmerProfile::withCount(['products', 'orders'])
            ->with('user')
            ->orderByDesc('orders_count')
            ->take(10)
            ->get();

        $productReport = Product::withCount('orderItems')
            ->with('farmer')
            ->orderByDesc('order_items_count')
            ->take(10)
            ->get();

        $growth = User::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total")
            ->where('created_at', '>=', now()->subYear())
            ->groupBy('month')->orderBy('month')->get();

        return view('admin.reports.index', compact('orderReport', 'farmerReport', 'productReport', 'growth'));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $type = $request->string('type')->toString() ?: 'orders';

        $filename = "marketlink-{$type}-".now()->format('Ymd-His').'.csv';

        return Response::streamDownload(function () use ($type) {
            $out = fopen('php://output', 'w');

            if ($type === 'orders') {
                fputcsv($out, ['Order Number', 'Customer', 'Farmer', 'Market', 'Status', 'Total', 'Pickup Date', 'Created']);
                Order::with(['customer', 'farmer', 'market'])->chunk(100, function ($orders) use ($out) {
                    foreach ($orders as $o) {
                        fputcsv($out, [
                            $o->order_number,
                            $o->customer->name ?? '',
                            $o->farmer->stall_name ?? '',
                            $o->market->name ?? '',
                            $o->status,
                            $o->total_amount,
                            $o->pickup_date?->format('Y-m-d'),
                            $o->created_at?->toDateTimeString(),
                        ]);
                    }
                });
            } elseif ($type === 'farmers') {
                fputcsv($out, ['Stall', 'Contact', 'Email', 'Status', 'Products', 'Orders']);
                FarmerProfile::with('user')->withCount(['products', 'orders'])->chunk(100, function ($farmers) use ($out) {
                    foreach ($farmers as $f) {
                        fputcsv($out, [
                            $f->stall_name,
                            $f->contact_person,
                            $f->user->email ?? '',
                            $f->approval_status,
                            $f->products_count,
                            $f->orders_count,
                        ]);
                    }
                });
            } elseif ($type === 'products') {
                fputcsv($out, ['Name', 'Farmer', 'Price', 'Stock', 'Available', 'Views']);
                Product::with('farmer')->chunk(100, function ($products) use ($out) {
                    foreach ($products as $p) {
                        fputcsv($out, [
                            $p->name,
                            $p->farmer->stall_name ?? '',
                            $p->price,
                            $p->stock_quantity,
                            $p->is_available ? 'yes' : 'no',
                            $p->views_count,
                        ]);
                    }
                });
            } else {
                fputcsv($out, ['Month', 'New Users']);
                User::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total")
                    ->groupBy('month')->orderBy('month')->get()
                    ->each(fn ($row) => fputcsv($out, [$row->month, $row->total]));
            }

            Report::create([
                'report_type' => $type,
                'generated_by' => auth()->id(),
                'data' => ['exported_at' => now()->toIso8601String()],
                'generated_at' => now(),
            ]);

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    // —— Announcements ——
    public function announcements()
    {
        $announcements = Announcement::with('publisher')->latest()->paginate(15);

        return view('admin.announcements.index', compact('announcements'));
    }

    public function storeAnnouncement(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'status' => 'required|in:draft,published,archived',
            'priority' => 'required|in:low,medium,high',
            'expires_at' => 'nullable|date',
        ]);

        Announcement::create([
            ...$data,
            'published_by' => auth()->id(),
            'published_at' => $data['status'] === 'published' ? now() : null,
        ]);

        return back()->with('success', 'Announcement saved.');
    }

    public function updateAnnouncement(Request $request, Announcement $announcement)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'status' => 'required|in:draft,published,archived',
            'priority' => 'required|in:low,medium,high',
            'expires_at' => 'nullable|date',
        ]);

        if ($data['status'] === 'published' && ! $announcement->published_at) {
            $data['published_at'] = now();
        }

        $announcement->update($data);

        return back()->with('success', 'Announcement updated.');
    }

    public function destroyAnnouncement(Announcement $announcement)
    {
        $announcement->delete();

        return back()->with('success', 'Announcement deleted.');
    }

    // —— FAQs ——
    public function faqs()
    {
        $faqs = ChatbotFaq::latest()->paginate(20);

        return view('admin.faqs.index', compact('faqs'));
    }

    public function storeFaq(Request $request)
    {
        $data = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string|max:5000',
            'category' => 'nullable|string|max:100',
            'keywords' => 'nullable|string|max:500',
        ]);

        $data['keywords'] = array_values(array_filter(array_map('trim', explode(',', $data['keywords'] ?? ''))));
        ChatbotFaq::create($data);

        return back()->with('success', 'FAQ created.');
    }

    public function destroyFaq(ChatbotFaq $faq)
    {
        $faq->delete();

        return back()->with('success', 'FAQ deleted.');
    }

    // —— Settings ——
    public function settings()
    {
        $settings = [
            'site_tagline' => Setting::getValue('site_tagline', 'Fresh from local farmers markets'),
            'support_email' => Setting::getValue('support_email', 'support@marketlink.com'),
            'default_cutoff_hours' => Setting::getValue('default_cutoff_hours', '24'),
            'low_stock_threshold' => Setting::getValue('low_stock_threshold', '5'),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'site_tagline' => 'required|string|max:255',
            'support_email' => 'required|email|max:100',
            'default_cutoff_hours' => 'required|integer|min:1|max:168',
            'low_stock_threshold' => 'required|integer|min:1|max:100',
        ]);

        foreach ($data as $key => $value) {
            Setting::setValue($key, $value);
        }

        return back()->with('success', 'Settings saved.');
    }

    protected function validateMarket(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:100',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'operating_days' => 'nullable|array',
            'opening_time' => 'nullable|date_format:H:i',
            'closing_time' => 'nullable|date_format:H:i',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string|max:2000',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);
    }
}
