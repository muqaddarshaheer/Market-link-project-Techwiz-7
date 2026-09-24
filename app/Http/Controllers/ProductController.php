<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\FarmerProfile;
use App\Models\Market;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()
            ->with(['farmer', 'market', 'category'])
            ->withAvg(['reviews as rating_avg' => fn ($q) => $q->where('status', 'approved')], 'rating')
            ->whereHas('farmer', fn ($q) => $q->where('approval_status', 'approved'));

        if ($request->filled('q')) {
            $query->where('name', 'like', '%'.$request->q.'%');
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        if ($request->filled('market')) {
            $query->where('market_id', $request->market);
        }
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        if ($request->boolean('available')) {
            $query->where('is_available', true)->where('is_sold_out', false)->where('stock_quantity', '>', 0);
        }
        if ($request->filled('rating')) {
            $query->having('rating_avg', '>=', (int) $request->rating);
        }
        if ($request->filled('day')) {
            $query->whereHas('market', fn ($q) => $q->whereJsonContains('operating_days', $request->day));
        }

        match ($request->get('sort', 'latest')) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'popular' => $query->orderByDesc('views_count'),
            'rating' => $query->orderByDesc('rating_avg'),
            'name' => $query->orderBy('name'),
            default => $query->latest(),
        };

        return view('products.index', [
            'products' => $query->paginate(12)->withQueryString(),
            'categories' => Category::query()->orderBy('name')->get(),
            'markets' => Market::query()->where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function show(Product $product)
    {
        $product->load(['farmer.user', 'market', 'category', 'reviews.customer']);
        abort_unless($product->farmer?->approval_status === 'approved' || auth()->user()?->isAdmin() || auth()->id() === $product->farmer?->user_id, 404);
        $product->increment('views_count');

        $related = Product::query()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_available', true)
            ->with('farmer')
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'related'));
    }

    public function search(Request $request)
    {
        $term = trim((string) $request->q);

        return view('search.results', [
            'q' => $term,
            'products' => $term === '' ? collect() : Product::query()->with(['farmer', 'market'])->where('name', 'like', "%$term%")->whereHas('farmer', fn ($q) => $q->where('approval_status', 'approved'))->limit(12)->get(),
            'farmers' => $term === '' ? collect() : FarmerProfile::query()->where('approval_status', 'approved')->where('stall_name', 'like', "%$term%")->limit(8)->get(),
            'markets' => $term === '' ? collect() : Market::query()->where('status', 'active')->where('name', 'like', "%$term%")->limit(8)->get(),
            'categories' => $term === '' ? collect() : Category::query()->where('name', 'like', "%$term%")->limit(6)->get(),
        ]);
    }

    public function suggest(Request $request)
    {
        $term = $request->string('q')->trim();
        if ($term->length() < 2) {
            return response()->json([]);
        }

        $products = Product::query()->where('name', 'like', '%'.$term.'%')
            ->whereHas('farmer', fn ($q) => $q->where('approval_status', 'approved'))
            ->limit(6)->get(['id', 'name']);
        $farmers = FarmerProfile::query()->where('approval_status', 'approved')
            ->where('stall_name', 'like', '%'.$term.'%')->limit(4)->get(['id', 'stall_name']);
        $markets = Market::query()->where('status', 'active')
            ->where('name', 'like', '%'.$term.'%')->limit(4)->get(['id', 'name']);

        return response()->json([
            'products' => $products->map(fn ($p) => ['label' => $p->name, 'url' => route('products.show', $p)]),
            'farmers' => $farmers->map(fn ($f) => ['label' => $f->stall_name, 'url' => route('farmers.show', $f)]),
            'markets' => $markets->map(fn ($m) => ['label' => $m->name, 'url' => route('markets.show', $m)]),
        ]);
    }
}
