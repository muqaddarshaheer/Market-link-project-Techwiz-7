@extends('layouts.app')
@section('title', 'Search')
@section('content')
<h1 class="section-title">Search</h1>
@if($q === '')
    <p class="muted">Type a product, stall, or market name.</p>
@else
    <p class="muted">Results for “{{ $q }}”.</p>
    <h2 class="h5">Products</h2>
    <div class="row g-3 mb-4">
        @forelse($products as $product)
            <div class="col-md-6 col-lg-3">@include('partials.product-card', compact('product'))</div>
        @empty
            <p class="muted">No products matched.</p>
        @endforelse
    </div>
    <h2 class="h5">Farmers</h2>
    @forelse($farmers as $farmer)
        <a class="d-block mb-2" href="{{ route('farmers.show', $farmer) }}">{{ $farmer->stall_name }}</a>
    @empty
        <p class="muted">No farmers matched.</p>
    @endforelse
    <h2 class="h5 mt-3">Categories</h2>
    @forelse($categories as $category)
        <a class="d-block mb-2" href="{{ route('products.index', ['category' => $category->id]) }}">{{ $category->name }}</a>
    @empty
        <p class="muted">No categories matched.</p>
    @endforelse
    <h2 class="h5 mt-3">Markets</h2>
    @forelse($markets as $market)
        <a class="d-block mb-2" href="{{ route('markets.show', $market) }}">{{ $market->name }} · {{ $market->city }}</a>
    @empty
        <p class="muted">No markets matched.</p>
    @endforelse
@endif
@endsection
