<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Market;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Services\NotificationService;
use App\Services\OrderService;
use App\Services\WeatherService;
use App\Support\ImageStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FarmerDashboardController extends Controller
{
    public function __construct(
        private NotificationService $notifications,
        private OrderService $orders,
        private WeatherService $weather,
    ) {}

    private function profile()
    {
        $profile = auth()->user()->farmerProfile;
        abort_unless($profile, 403);

        return $profile;
    }

    public function index()
    {
        $farmer = $this->profile()->loadMissing('markets');
        $orders = $farmer->orders();

        return view('farmer.dashboard', [
            'farmer' => $farmer,
            'weather' => $this->weather->forFarmer($farmer),
            'stats' => [
                'orders' => (clone $orders)->count(),
                'pending' => (clone $orders)->where('status', 'placed')->count(),
                'revenue' => (clone $orders)->where('status', 'completed')->sum('total_amount'),
                'products' => $farmer->products()->count(),
            ],
            'recent' => $farmer->orders()->with('customer')->latest()->take(6)->get(),
            'lowStock' => $farmer->products()->where('stock_quantity', '<=', 5)->where('is_available', true)->get(),
            'best' => $farmer->products()->withSum('orderItems as sold', 'quantity')->orderByDesc('sold')->take(5)->get(),
        ]);
    }

    public function speakWeather(Request $request)
    {
        $this->profile();

        $data = $request->validate([
            'text' => ['required', 'string', 'max:280'],
            'lang' => ['nullable', 'in:ur,en,hi'],
        ]);

        $lang = $data['lang'] ?? 'ur';
        $speech = app(\App\Services\WeatherSpeechService::class);
        $normalizeLang = $lang === 'en' ? 'en' : 'ur';

        // Expand symbols/abbreviations so TTS never reads raw "C", "%", or "km/h".
        $text = $speech->normalizeForTts(trim($data['text']), $normalizeLang);

        if ($normalizeLang === 'ur') {
            // English place names / labels can flip Google TTS mid-sentence.
            $cleaned = trim(preg_replace('/\b[A-Za-z][A-Za-z0-9\'\-]{2,}\b/u', '', $text) ?: $text);
            $cleaned = preg_replace('/\s+/u', ' ', $cleaned) ?: $text;
            if (mb_strlen($cleaned) >= 12) {
                $text = $cleaned;
            }
        }

        $text = trim(preg_replace('/\s+/u', ' ', $text) ?: '');
        $text = mb_substr($text, 0, 180);
        if ($text === '') {
            return response('Voice unavailable', 422);
        }

        // Prefer real Urdu voice. Hindi fallback often returns tiny/garbled audio for Nastaliq.
        $voiceOrder = match ($lang) {
            'en' => ['en'],
            'hi' => ['hi'],
            default => ['ur', 'hi'],
        };

        try {
            $body = null;
            foreach ($voiceOrder as $voiceLang) {
                $body = $this->fetchTtsAudio($text, $voiceLang);
                if ($body !== null) {
                    break;
                }
            }

            if ($body === null) {
                return response('Voice unavailable', 502);
            }

            return response($body, 200, [
                'Content-Type' => 'audio/mpeg',
                'Cache-Control' => 'private, max-age=300',
            ]);
        } catch (\Throwable $e) {
            return response('Voice unavailable', 502);
        }
    }

    private function fetchTtsAudio(string $text, string $tl): ?string
    {
        $http = \Illuminate\Support\Facades\Http::timeout(12)
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
                'Accept' => '*/*',
                'Accept-Language' => 'ur-PK,ur;q=0.9,hi-IN;q=0.8,en;q=0.7',
                'Referer' => 'https://translate.google.com/',
            ]);

        if (app()->environment('local')) {
            $http = $http->withoutVerifying();
        }

        // Short sentences still produce several KB; reject tiny broken clips.
        $minBytes = max(3500, (int) (mb_strlen($text) * 80));

        foreach (['tw-ob', 'gtx'] as $client) {
            $response = $http->get('https://translate.google.com/translate_tts', [
                'ie' => 'UTF-8',
                'client' => $client,
                'tl' => $tl,
                'q' => $text,
                'ttsspeed' => $tl === 'ur' ? '0.88' : '0.92',
            ]);

            if ($response->successful() && strlen($response->body()) >= $minBytes) {
                return $response->body();
            }
        }

        return null;
    }

    public function products()
    {
        $farmer = $this->profile();

        return view('farmer.products.index', [
            'farmer' => $farmer,
            'products' => $farmer->products()->with(['category', 'market'])->withCount('favorites')->latest()->paginate(12),
            'categories' => Category::query()->orderBy('name')->get(),
            'markets' => $farmer->markets,
        ]);
    }

    public function storeProduct(Request $request)
    {
        $farmer = $this->profile();
        abort_unless($farmer->isApproved(), 403, 'Your stall must be approved before listing products.');

        $data = $this->validateProduct($request);
        $data['farmer_id'] = $farmer->id;
        $data['image'] = ImageStore::put($request->file('image'), 'products');
        $data['is_sold_out'] = ($data['stock_quantity'] ?? 0) <= 0;
        Product::query()->create($data);

        return back()->with('success', 'Product listed.');
    }

    public function updateProduct(Request $request, Product $product)
    {
        $farmer = $this->profile();
        abort_unless($product->farmer_id === $farmer->id, 403);
        $before = $product->stock_quantity;
        $data = $this->validateProduct($request);
        if ($request->hasFile('image')) {
            $data['image'] = ImageStore::put($request->file('image'), 'products', $product->image);
        }
        $data['is_sold_out'] = $request->boolean('is_sold_out') || ($data['stock_quantity'] ?? 0) <= 0;
        $data['is_available'] = $request->boolean('is_available');
        $product->update($data);

        if ($before <= 0 && $product->stock_quantity > 0) {
            $this->notifyRestock($product);
        }

        return back()->with('success', 'Product updated.');
    }

    public function destroyProduct(Product $product)
    {
        abort_unless($product->farmer_id === $this->profile()->id, 403);
        $product->delete();

        return back()->with('success', 'Product removed.');
    }

    public function saveTemplate()
    {
        $farmer = $this->profile();
        $template = $farmer->products()->pluck('stock_quantity', 'id');
        $farmer->update(['weekly_stock_template' => $template]);

        return back()->with('success', 'This week’s stock was saved as your template.');
    }

    public function applyTemplate()
    {
        $farmer = $this->profile();
        $template = $farmer->weekly_stock_template ?? [];
        foreach ($template as $id => $qty) {
            $product = $farmer->products()->whereKey($id)->first();
            if (! $product) {
                continue;
            }
            $wasEmpty = $product->stock_quantity <= 0;
            $product->update([
                'stock_quantity' => (int) $qty,
                'is_sold_out' => (int) $qty <= 0,
            ]);
            if ($wasEmpty && (int) $qty > 0) {
                $this->notifyRestock($product);
            }
        }

        return back()->with('success', 'Weekly stock template applied.');
    }

    public function ordersIndex(Request $request)
    {
        $farmer = $this->profile();
        $orders = $farmer->orders()->with(['customer', 'items', 'market'])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest()->paginate(12)->withQueryString();

        return view('farmer.orders.index', compact('orders', 'farmer'));
    }

    public function accept(Request $request, Order $order)
    {
        $this->own($order);
        abort_unless($order->status === 'placed', 422);
        $order->update([
            'status' => 'accepted',
            'farmer_notes' => $request->validate(['farmer_notes' => ['nullable', 'string', 'max:500']])['farmer_notes'] ?? $order->farmer_notes,
        ]);
        $this->notifications->send($order->customer, 'order_accepted', 'Order accepted', $order->order_number.' was accepted. See you at pickup.', ['order_id' => $order->id]);

        return back()->with('success', 'Order accepted.');
    }

    public function decline(Request $request, Order $order)
    {
        $this->own($order);
        abort_unless($order->status === 'placed', 422);
        $note = $request->validate(['farmer_notes' => ['nullable', 'string', 'max:500']])['farmer_notes'] ?? null;
        $this->orders->restoreStock($order);
        $order->update(['status' => 'declined', 'farmer_notes' => $note]);
        $this->notifications->send($order->customer, 'order_declined', 'Order declined', $order->order_number.' was declined.'.($note ? ' '.$note : ''), ['order_id' => $order->id]);

        return back()->with('success', 'Order declined and stock restored.');
    }

    public function ready(Order $order)
    {
        $this->own($order);
        abort_unless($order->status === 'accepted', 422);
        $order->update(['status' => 'ready_for_pickup']);
        $this->notifications->send($order->customer, 'order_ready', 'Ready for pickup', $order->order_number.' is ready at '.$order->market->name.'.', ['order_id' => $order->id]);

        return back()->with('success', 'Marked ready for pickup.');
    }

    public function complete(Order $order)
    {
        $this->own($order);
        abort_unless(in_array($order->status, ['accepted', 'ready_for_pickup'], true), 422);
        $order->update(['status' => 'completed']);

        return back()->with('success', 'Order completed.');
    }

    public function slots()
    {
        return view('farmer.slots.index', ['farmer' => $this->profile()]);
    }

    public function updateSlots(Request $request)
    {
        $data = $request->validate([
            'slots' => ['required', 'array', 'min:1'],
            'slots.*' => ['required', 'string', 'max:100'],
            'cutoff_hours' => ['required', 'integer', 'min:1', 'max:72'],
        ]);
        $this->profile()->update([
            'pickup_slots' => collect($data['slots'])->map(fn ($label) => ['label' => $label])->values(),
            'cutoff_hours' => $data['cutoff_hours'],
        ]);

        return back()->with('success', 'Pickup slots saved.');
    }

    public function insights(Request $request)
    {
        $farmer = $this->profile();
        $days = match ($request->range) {
            'today' => 0,
            '30' => 30,
            default => 7,
        };
        $start = $request->filled('from') ? $request->date('from') : ($days === 0 ? now()->startOfDay() : now()->subDays($days));
        $byDay = $farmer->orders()
            ->select(DB::raw('DATE(created_at) as day'), DB::raw('COUNT(*) as total'), DB::raw('SUM(CASE WHEN status = "completed" THEN total_amount ELSE 0 END) as revenue'))
            ->where('created_at', '>=', $start)
            ->when($request->to, fn ($q, $date) => $q->whereDate('created_at', '<=', $date))
            ->groupBy('day')->orderBy('day')->get();

        $top = $farmer->products()->withSum('orderItems as sold', 'quantity')->orderByDesc('sold')->take(5)->get();

        return view('farmer.insights', compact('farmer', 'byDay', 'top'));
    }

    public function reviews()
    {
        $reviews = $this->profile()->reviews()->with(['customer', 'product'])->latest()->paginate(10);

        return view('farmer.reviews', compact('reviews'));
    }

    public function reply(Request $request, Review $review)
    {
        abort_unless($review->farmer_id === $this->profile()->id, 403);
        $data = $request->validate(['farmer_reply' => ['required', 'string', 'max:1000']]);
        $review->update($data);

        return back()->with('success', 'Reply posted.');
    }

    public function profileEdit()
    {
        $farmer = $this->profile()->load('markets');

        return view('farmer.profile', [
            'farmer' => $farmer,
            'markets' => Market::query()->where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function account()
    {
        $this->profile();

        return view('farmer.account');
    }

    public function profileUpdate(Request $request)
    {
        $farmer = $this->profile();
        $data = $request->validate([
            'stall_name' => ['required', 'string', 'max:100'],
            'contact_person' => ['required', 'string', 'max:100'],
            'business_description' => ['nullable', 'string', 'max:2000'],
            'address' => ['required', 'string', 'max:500'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'operating_days' => ['required', 'array', 'min:1'],
            'phone' => ['required', 'string', 'max:20'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'market_ids' => ['nullable', 'array'],
            'market_ids.*' => ['integer', 'exists:markets,id'],
            'stall_number' => ['nullable', 'array'],
        ]);

        $farmer->update([
            'stall_name' => $data['stall_name'],
            'contact_person' => $data['contact_person'],
            'business_description' => $data['business_description'] ?? null,
            'address' => $data['address'],
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'operating_days' => $data['operating_days'],
            'logo' => ImageStore::put($request->file('logo'), 'logos', $farmer->logo),
        ]);
        $request->user()->update(['name' => $data['contact_person'], 'phone' => $data['phone'], 'address' => $data['address']]);

        $sync = [];
        foreach ($data['market_ids'] ?? [] as $id) {
            $sync[$id] = ['stall_number' => $data['stall_number'][$id] ?? null];
        }
        $farmer->markets()->sync($sync);

        return back()->with('success', 'Stall profile saved.');
    }

    private function validateProduct(Request $request): array
    {
        $farmer = $this->profile();

        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'category_id' => ['required', 'exists:categories,id'],
            'market_id' => ['required', 'in:'.$farmer->markets()->pluck('markets.id')->implode(',')],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0.01', 'max:99999'],
            'unit' => ['required', 'in:kg,gram,dozen,bunch,litre,piece,pack'],
            'quality' => ['required', 'in:premium,fresh,standard'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'is_available' => ['sometimes', 'boolean'],
            'is_sold_out' => ['sometimes', 'boolean'],
        ]) + [
            'is_available' => $request->boolean('is_available', true),
            'is_sold_out' => $request->boolean('is_sold_out'),
        ];
    }

    private function own(Order $order): void
    {
        abort_unless($order->farmer_id === $this->profile()->id, 403);
    }

    private function notifyRestock(Product $product): void
    {
        $watchers = $product->favorites()->with('customer')->get();
        foreach ($watchers as $fav) {
            if ($fav->customer) {
                $this->notifications->send(
                    $fav->customer,
                    'restock',
                    'Back in stock',
                    $product->name.' is available again.',
                    ['product_id' => $product->id]
                );
            }
        }
    }
}
