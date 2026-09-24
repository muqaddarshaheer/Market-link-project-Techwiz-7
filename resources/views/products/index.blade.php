@extends('layouts.app')
@section('title', 'Products')
@section('content')
<div class="row g-4">
    <div class="col-lg-3">
        <form class="filter-panel p-3" method="GET">
            <div class="d-flex justify-content-between"><h2 class="h6">Filters</h2><a href="{{ route('products.index') }}">Clear</a></div>
            <label class="form-label">Search</label>
            <input class="form-control mb-2" name="q" value="{{ request('q') }}">
            <label class="form-label">Category</label>
            <select class="form-select mb-2" name="category"><option value="">All</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(request('category')==$category->id)>{{ $category->name }}</option>@endforeach</select>
            <label class="form-label">Market</label>
            <select class="form-select mb-2" name="market"><option value="">All</option>@foreach($markets as $market)<option value="{{ $market->id }}" @selected(request('market')==$market->id)>{{ $market->name }}</option>@endforeach</select>
            <div class="row g-2">
                <div class="col"><input class="form-control" name="min_price" placeholder="Min $" value="{{ request('min_price') }}"></div>
                <div class="col"><input class="form-control" name="max_price" placeholder="Max $" value="{{ request('max_price') }}"></div>
            </div>
            <label class="form-label mt-2">Day</label>
            <select class="form-select mb-2" name="day"><option value="">Any</option>@foreach(['Saturday','Sunday','Wednesday','Friday'] as $day)<option @selected(request('day')===$day)>{{ $day }}</option>@endforeach</select>
            <label class="form-label">Rating</label>
            <select class="form-select mb-2" name="rating"><option value="">Any</option>@foreach([4,3,2] as $r)<option value="{{ $r }}" @selected(request('rating')==$r)>{{ $r }}+ stars</option>@endforeach</select>
            <div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="available" value="1" @checked(request()->boolean('available'))><label class="form-check-label">In stock only</label></div>
            <label class="form-label">Sort</label>
            <select class="form-select mb-3" name="sort">
                @foreach(['latest'=>'Latest','price_asc'=>'Price low-high','price_desc'=>'Price high-low','popular'=>'Most popular','rating'=>'Highest rated','name'=>'Alphabetical'] as $val=>$label)
                    <option value="{{ $val }}" @selected(request('sort','latest')===$val)>{{ $label }}</option>
                @endforeach
            </select>
            <button class="btn btn-ml w-100">Apply</button>
            <div class="mt-2 small">
                @foreach(request()->except('page') as $key => $value)
                    @if($value)<span class="badge badge-soft me-1">{{ $key }}: {{ is_array($value) ? '' : $value }}</span>@endif
                @endforeach
            </div>
        </form>
    </div>
    <div class="col-lg-9">
        <h1 class="section-title">Products</h1>
        <div class="row g-3">
            @forelse($products as $product)
                <div class="col-md-6 col-xl-4">@include('partials.product-card', compact('product'))</div>
            @empty
                <div class="empty-state"><i class="bi bi-basket"></i><p>No products match those filters.</p></div>
            @endforelse
        </div>
        <div class="mt-3">{{ $products->links() }}</div>
    </div>
</div>
@endsection
