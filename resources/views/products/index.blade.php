@extends('layouts.app')
@section('title', 'Products')
@section('content')
<div class="shop-browse ml-motion" id="shopBrowse">
    <div class="shop-browse-head d-flex flex-wrap justify-content-between align-items-end gap-2 mb-3">
        <div>
            <h1 class="section-title mb-1">Products</h1>
            <p class="home-sub mb-0">Fast browse · live filters · add without leaving the page</p>
        </div>
        <button class="btn btn-outline-ml btn-sm d-lg-none" type="button" id="shopFilterToggle" aria-controls="productFilters" aria-expanded="false">
            <i class="bi bi-sliders"></i> Filters
        </button>
    </div>
    <div class="row g-4">
        <div class="col-lg-3">
            <form class="filter-panel p-3 shop-filters" id="productFilters" method="GET" action="{{ route('products.index') }}" data-live-filter>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h2 class="h6 mb-0">Filters</h2>
                    <a href="{{ route('products.index') }}" data-filter-clear>Clear</a>
                </div>
                <label class="form-label">Search</label>
                <input class="form-control mb-2" name="q" value="{{ request('q') }}" placeholder="Tomato, corn…" autocomplete="off" data-filter-live>
                <label class="form-label">Category</label>
                <select class="form-select mb-2" name="category" data-filter-live><option value="">All</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(request('category')==$category->id)>{{ $category->name }}</option>@endforeach</select>
                <label class="form-label">Market</label>
                <select class="form-select mb-2" name="market" data-filter-live><option value="">All</option>@foreach($markets as $market)<option value="{{ $market->id }}" @selected(request('market')==$market->id)>{{ $market->name }}</option>@endforeach</select>
                <div class="row g-2">
                    <div class="col"><input class="form-control" name="min_price" placeholder="Min Rs" value="{{ request('min_price') }}" data-filter-live></div>
                    <div class="col"><input class="form-control" name="max_price" placeholder="Max Rs" value="{{ request('max_price') }}" data-filter-live></div>
                </div>
                <label class="form-label mt-2">Day</label>
                <select class="form-select mb-2" name="day" data-filter-live><option value="">Any</option>@foreach(['Saturday','Sunday','Wednesday','Friday'] as $day)<option @selected(request('day')===$day)>{{ $day }}</option>@endforeach</select>
                <label class="form-label">Rating</label>
                <select class="form-select mb-2" name="rating" data-filter-live><option value="">Any</option>@foreach([4,3,2] as $r)<option value="{{ $r }}" @selected(request('rating')==$r)>{{ $r }}+ stars</option>@endforeach</select>
                <label class="form-label">Quality</label>
                <select class="form-select mb-2" name="quality" data-filter-live><option value="">Any</option>@foreach(['premium','fresh','standard'] as $quality)<option value="{{ $quality }}" @selected(request('quality')===$quality)>{{ ucfirst($quality) }}</option>@endforeach</select>
                <label class="form-label">Farmer</label>
                <select class="form-select mb-2" name="farmer" data-filter-live><option value="">Any</option>@foreach($farmers as $farmer)<option value="{{ $farmer->id }}" @selected(request('farmer')==$farmer->id)>{{ $farmer->stall_name }}</option>@endforeach</select>
                <div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="available" value="1" @checked(request()->boolean('available')) data-filter-live><label class="form-check-label">In stock only</label></div>
                <label class="form-label">Sort</label>
                <select class="form-select mb-3" name="sort" data-filter-live>
                    @foreach(['latest'=>'Latest','price_asc'=>'Price low-high','price_desc'=>'Price high-low','popular'=>'Most popular','rating'=>'Highest rated','name'=>'Alphabetical'] as $val=>$label)
                        <option value="{{ $val }}" @selected(request('sort','latest')===$val)>{{ $label }}</option>
                    @endforeach
                </select>
                <button class="btn btn-ml w-100 d-lg-none" type="submit">Apply</button>
                <div class="mt-2 small filter-chips" id="filterChips">
                    @foreach(request()->except('page') as $key => $value)
                        @if($value)<span class="badge badge-soft me-1">{{ $key }}: {{ is_array($value) ? '' : $value }}</span>@endif
                    @endforeach
                </div>
            </form>
        </div>
        <div class="col-lg-9">
            <div id="productResults" class="shop-results" aria-live="polite">
                @include('products._grid')
            </div>
        </div>
    </div>
</div>
@endsection
