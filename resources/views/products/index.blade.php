@extends('layouts.app')
@section('title', 'Products')
@section('content')
@php
    $activeRating = (int) request('rating', 0);
@endphp
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
            <div class="filter-shell shop-filters" id="productFilters">
                <span class="filter-shell-glow" aria-hidden="true"></span>
                <form class="filter-panel filter-panel-ui" method="GET" action="{{ route('products.index') }}" data-live-filter>
                    <div class="filter-panel-top">
                        <h2 class="filter-panel-title"><i class="bi bi-leaf-fill" aria-hidden="true"></i> Filters</h2>
                        <a class="filter-clear" href="{{ route('products.index') }}" data-filter-clear>Clear</a>
                    </div>

                    <label class="filter-label" for="filterQ">Search</label>
                    <div class="filter-search">
                        <i class="bi bi-search" aria-hidden="true"></i>
                        <input id="filterQ" class="form-control" type="search" name="q" value="{{ request('q') }}" placeholder="Search…" autocomplete="off" data-filter-live>
                    </div>

                    <label class="filter-label" for="filterCategory">Category</label>
                    <select class="form-select" id="filterCategory" name="category" data-filter-live>
                        <option value="">All</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>

                    <label class="filter-label" for="filterMarket">Market</label>
                    <select class="form-select" id="filterMarket" name="market" data-filter-live>
                        <option value="">All</option>
                        @foreach($markets as $market)
                            <option value="{{ $market->id }}" @selected(request('market') == $market->id)>{{ $market->name }}</option>
                        @endforeach
                    </select>

                    <div class="filter-price-row">
                        <input class="form-control" name="min_price" placeholder="Min Rs" value="{{ request('min_price') }}" inputmode="decimal" data-filter-live aria-label="Minimum price">
                        <input class="form-control" name="max_price" placeholder="Max Rs" value="{{ request('max_price') }}" inputmode="decimal" data-filter-live aria-label="Maximum price">
                    </div>

                    <label class="filter-label" for="filterDay">Day</label>
                    <select class="form-select" id="filterDay" name="day" data-filter-live>
                        <option value="">Any</option>
                        @foreach(['Saturday','Sunday','Wednesday','Friday'] as $day)
                            <option value="{{ $day }}" @selected(request('day') === $day)>{{ $day }}</option>
                        @endforeach
                    </select>

                    <div class="filter-rate-box" data-filter-rate>
                        <span class="filter-label">Your Rating</span>
                        <div class="filter-rate-stars" role="radiogroup" aria-label="Filter by minimum rating">
                            @for($i = 1; $i <= 5; $i++)
                                <button
                                    type="button"
                                    class="rate-star{{ $activeRating >= $i ? ' is-on' : '' }}"
                                    data-rate-value="{{ $i }}"
                                    aria-label="{{ $i }} star{{ $i > 1 ? 's' : '' }} and up"
                                    aria-checked="{{ $activeRating === $i ? 'true' : 'false' }}"
                                    role="radio"
                                >★</button>
                            @endfor
                        </div>
                        <p class="filter-rate-hint">Tap a star to rate us</p>
                        <div class="filter-rate-thanks{{ $activeRating > 0 ? ' is-show' : '' }}" data-rate-thanks @if($activeRating < 1) hidden @endif>
                            <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                            <span data-rate-thanks-text>Thank you for rating us {{ $activeRating }} out of 5!</span>
                        </div>
                        <input type="hidden" name="rating" value="{{ $activeRating > 0 ? $activeRating : '' }}" data-filter-live data-rate-input>
                    </div>

                    <label class="filter-label" for="filterSort">Sort</label>
                    <select class="form-select" id="filterSort" name="sort" data-filter-live>
                        @foreach(['latest'=>'Latest','price_asc'=>'Price low-high','price_desc'=>'Price high-low','popular'=>'Most popular','rating'=>'Highest rated','name'=>'Alphabetical'] as $val => $label)
                            <option value="{{ $val }}" @selected(request('sort', 'latest') === $val)>{{ $label }}</option>
                        @endforeach
                    </select>

                    <div class="form-check filter-stock">
                        <input class="form-check-input" type="checkbox" name="available" value="1" id="filterAvailable" @checked(request()->boolean('available')) data-filter-live>
                        <label class="form-check-label" for="filterAvailable">In stock only</label>
                    </div>

                    <button class="btn btn-ml filter-apply" type="submit">Apply</button>

                    <div class="filter-chips" id="filterChips">
                        @foreach(request()->except('page') as $key => $value)
                            @if($value !== null && $value !== '')
                                <span class="filter-chip">{{ $key }}: {{ is_array($value) ? implode(',', $value) : $value }}</span>
                            @endif
                        @endforeach
                    </div>
                </form>
            </div>
        </div>
        <div class="col-lg-9">
            <div id="productResults" class="shop-results" aria-live="polite">
                @include('products._grid')
            </div>
        </div>
    </div>
</div>
@endsection
