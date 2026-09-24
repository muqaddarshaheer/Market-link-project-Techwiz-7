@extends('layouts.app')
@section('title', 'Products')
@section('content')
<div class="container py-5">
    <h1 class="display-font">Browse produce</h1>
    <p class="section-sub">Search, filter, and sort seasonal items from approved farmers.</p>

    <div class="row g-4">
        <aside class="col-lg-3">
            <form method="GET" class="panel">
                <h5 class="mb-3">Filters</h5>
                <div class="mb-3">
                    <label class="form-label">Search</label>
                    <input class="form-control" name="q" value="{{ request('q') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="">All</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}" @selected(request('category_id')==$c->id)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Market</label>
                    <select name="market_id" class="form-select">
                        <option value="">All</option>
                        @foreach($markets as $m)
                            <option value="{{ $m->id }}" @selected(request('market_id')==$m->id)>{{ $m->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col"><input class="form-control" name="min_price" placeholder="Min $" value="{{ request('min_price') }}"></div>
                    <div class="col"><input class="form-control" name="max_price" placeholder="Max $" value="{{ request('max_price') }}"></div>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="available" value="1" id="avail" @checked(request('available'))>
                    <label class="form-check-label" for="avail">In stock only</label>
                </div>
                <div class="mb-3">
                    <label class="form-label">Min rating</label>
                    <select name="rating" class="form-select">
                        <option value="">Any</option>
                        @foreach([4,3,2,1] as $r)
                            <option value="{{ $r }}" @selected(request('rating')==$r)>{{ $r }}+ stars</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Sort</label>
                    <select name="sort" class="form-select">
                        <option value="latest" @selected($sort==='latest')>Latest</option>
                        <option value="price_asc" @selected($sort==='price_asc')>Price ↑</option>
                        <option value="price_desc" @selected($sort==='price_desc')>Price ↓</option>
                        <option value="popular" @selected($sort==='popular')>Popular</option>
                        <option value="rated" @selected($sort==='rated')>Top rated</option>
                        <option value="alpha" @selected($sort==='alpha')>A–Z</option>
                    </select>
                </div>
                <button class="btn btn-primary w-100">Apply</button>
                <a href="{{ route('products.index') }}" class="btn btn-link w-100">Clear all</a>
            </form>
        </aside>
        <div class="col-lg-9">
            @if($activeFilters->isNotEmpty())
                <div class="mb-3">
                    @foreach($activeFilters as $key => $val)
                        <span class="filter-chip">{{ $key }}: {{ is_bool($val) ? 'yes' : $val }}</span>
                    @endforeach
                </div>
            @endif
            <div class="row g-4">
                @forelse($products as $product)
                    <div class="col-md-6 col-xl-4">@include('partials.product-card', ['product' => $product])</div>
                @empty
                    <div class="empty-state">No products match your filters.</div>
                @endforelse
            </div>
            <div class="mt-4">{{ $products->links() }}</div>
        </div>
    </div>
</div>
@endsection
