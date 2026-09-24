<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Market;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with(['farmer.user', 'market', 'category', 'reviews'])
            ->withAvg(['reviews as avg_rating' => fn ($q) => $q->where('status', 'approved')], 'rating')
            ->whereHas('farmer', fn ($q) => $q->where('approval_status', 'approved'));

        if ($search = $request->string('q')->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }

        if ($request->filled('market_id')) {
            $query->where('market_id', $request->integer('market_id'));
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->input('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->input('max_price'));
        }

        if ($request->boolean('available')) {
            $query->available();
        }

        if ($request->filled('rating')) {
            $minRating = (float) $request->input('rating');
            $query->having('avg_rating', '>=', $minRating);
        }

        $sort = $request->string('sort')->toString() ?: 'latest';
        match ($sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'popular' => $query->orderByDesc('views_count'),
            'rated' => $query->orderByDesc('avg_rating'),
            'alpha' => $query->orderBy('name'),
            default => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::orderBy('name')->get();
        $markets = Market::active()->orderBy('name')->get();

        $activeFilters = collect($request->only([
            'q', 'category_id', 'market_id', 'min_price', 'max_price', 'available', 'rating', 'sort',
        ]))->filter(fn ($v) => $v !== null && $v !== '');

        return view('products.index', compact('products', 'categories', 'markets', 'activeFilters', 'sort'));
    }

    public function show(Product $product): View
    {
        $product->load(['farmer.user', 'market', 'category']);
        $product->incrementViews();

        $reviews = $product->reviews()->approved()->with('customer')->latest()->paginate(5);
        $related = Product::available()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['farmer', 'market'])
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'reviews', 'related'));
    }

    public function autocomplete(Request $request)
    {
        $q = $request->string('q')->toString();

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $results = Product::whereHas('farmer', fn ($f) => $f->where('approval_status', 'approved'))
            ->where('name', 'like', "%{$q}%")
            ->available()
            ->orderBy('name')
            ->take(8)
            ->get(['id', 'name', 'price', 'unit'])
            ->map(fn (Product $p) => [
                'id' => $p->id,
                'name' => $p->name,
                'price' => number_format((float) $p->price, 2),
                'unit' => $p->unit,
                'url' => route('products.show', $p),
            ]);

        return response()->json($results);
    }
}
