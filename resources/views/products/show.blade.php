@extends('layouts.app')
@section('title', $product->name)
@section('content')
@php
    $available = $product->canPurchase();
    $rating = $product->reviews->where('status','approved')->avg('rating');
    $reviewCount = $product->reviews->where('status','approved')->count();
@endphp
<div class="product-view">
    <div class="product-photo card-ml">
        <img src="{{ \App\Support\ImageStore::picture($product->image, $product->name) }}" alt="{{ $product->name }}" width="960" height="720">
    </div>
    <div>
        <span class="badge badge-soft">{{ $product->category->name }}</span>
        <span class="badge badge-soft">{{ ucfirst($product->quality ?? 'fresh') }}</span>
        @if($available)
            <span class="badge text-bg-success">Available</span>
        @else
            <span class="badge text-bg-secondary">Unavailable</span>
        @endif
        <h1 class="section-title">{{ $product->name }}</h1>
        @include('partials.star-rating', ['rating' => $rating, 'count' => $reviewCount])
        <p class="product-price">${{ number_format($product->price, 2) }} <span>/ {{ $product->unit }}</span></p>
        <p>{{ $product->description }}</p>
        <div class="product-grid">
            <div><span>Stock</span><strong>{{ $product->stock_quantity }} {{ $product->unit }}</strong></div>
            <div><span>Farmer</span><strong><a href="{{ route('farmers.show', $product->farmer) }}">{{ $product->farmer->stall_name }}</a></strong></div>
            <div><span>Phone</span><strong>{{ $product->farmer->user->phone ?: 'Ask at the stall' }}</strong></div>
            <div><span>Quality</span><strong>{{ ucfirst($product->quality ?? 'fresh') }}</strong></div>
            <div><span>Market</span><strong><a href="{{ route('markets.show', $product->market) }}">{{ $product->market->name }}</a></strong></div>
            <div><span>Pay</span><strong>At the stall, in person</strong></div>
        </div>
        @auth
            @if(auth()->user()->isCustomer() && $available)
                <form method="POST" action="{{ route('cart.add', $product) }}" class="d-flex gap-2 mb-2" id="addForm">
                    @csrf
                    <input class="form-control" style="max-width:100px" type="number" name="quantity" value="1" min="1" max="{{ $product->stock_quantity }}">
                    <button class="btn btn-ml" id="addBtn" type="submit">Add to cart</button>
                </form>
                <form method="POST" action="{{ route('customer.favorites.toggle') }}">@csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <button class="btn btn-outline-ml btn-sm">Save product</button>
                </form>
            @elseif(! auth()->user()->isCustomer())
                <p class="muted mb-0">Customers can pre-order this from their account.</p>
            @endif
        @else
            <form method="POST" action="{{ route('guest.cart.add', $product) }}" class="d-flex gap-2 mb-2">
                @csrf
                <input class="form-control" style="max-width:100px" type="number" name="quantity" value="1" min="1" max="{{ max(1, $product->stock_quantity) }}">
                <button class="btn btn-ml" type="submit" @disabled(! $available)>Add as guest</button>
            </form>
            <a href="{{ route('login') }}">Or log in</a>
        @endauth
    </div>
</div>
<h2 class="h5 mt-4">Reviews</h2>
@forelse($product->reviews->where('status','approved') as $review)
    <div class="card-ml p-3 mb-2">
        @include('partials.star-rating', ['rating' => $review->rating])
        <p class="mb-1">{{ $review->comment }}</p>
        <div class="small muted">{{ $review->customer->name }}</div>
    </div>
@empty
    <p class="muted">No reviews yet.</p>
@endforelse
@if($related->isNotEmpty())
    <h2 class="h5 mt-4">More in {{ $product->category->name }}</h2>
    <div class="row g-3">@foreach($related as $item)<div class="col-md-6 col-lg-3">@include('partials.product-card', ['product' => $item])</div>@endforeach</div>
@endif
@auth
@if(auth()->user()->isCustomer() && $available)
<script>
document.getElementById('addForm').addEventListener('submit', function () {
    const button = document.getElementById('addBtn');
    button.disabled = true;
    button.textContent = 'Adding…';
});
</script>
@endif
@endauth
@endsection
