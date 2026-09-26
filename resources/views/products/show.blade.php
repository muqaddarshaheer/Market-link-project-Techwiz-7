@extends('layouts.app')
@section('title', $product->name)
@section('content')
@php
    $available = $product->canPurchase();
    $rating = $product->reviews->where('status','approved')->avg('rating');
    $reviewCount = $product->reviews->where('status','approved')->count();
@endphp
<div class="product-stage">
    <div class="product-stage-media">
        <img src="{{ \App\Support\ImageStore::picture($product->image, $product->name) }}" alt="{{ $product->name }}" width="960" height="720">
        <div class="product-stage-tags">
            <span>{{ $product->category->name }}</span>
            <span>{{ ucfirst($product->quality ?? 'fresh') }}</span>
            <span class="{{ $available ? 'is-live' : 'is-out' }}">{{ $available ? 'In stock' : 'Unavailable' }}</span>
        </div>
    </div>
    <div class="product-stage-copy">
        <p class="story-kicker">{{ $product->farmer->stall_name }}</p>
        <h1 class="section-title">{{ $product->name }}</h1>
        @include('partials.star-rating', ['rating' => $rating, 'count' => $reviewCount])
        <p class="product-price">{{ money($product->price) }} <span>/ {{ $product->unit }}</span></p>
        <p>{{ $product->description }}</p>
        <div class="product-grid">
            <div><span>Stock</span><strong>{{ $product->stock_quantity }} {{ $product->unit }}</strong></div>
            <div><span>Farmer</span><strong><a href="{{ route('farmers.show', $product->farmer) }}">{{ $product->farmer->stall_name }}</a></strong></div>
            <div><span>Phone</span><strong>{{ $product->farmer->user->phone ?: 'Ask at the stall' }}</strong></div>
            <div><span>Market</span><strong><a href="{{ route('markets.show', $product->market) }}">{{ $product->market->name }}</a></strong></div>
        </div>
        @auth
            @if(auth()->user()->isCustomer() && $available)
                <form method="POST" action="{{ route('cart.add', $product) }}" class="product-buy" id="addForm" data-ajax-cart>
                    @csrf
                    <input class="form-control" type="number" name="quantity" value="1" min="1" max="{{ $product->stock_quantity }}">
                    <button class="btn btn-ml" id="addBtn" type="submit">Add to cart</button>
                </form>
            @endif
        @else
            @if($available)
            <form method="POST" action="{{ route('guest.cart.add', $product) }}" class="product-buy mb-2" data-ajax-cart>
                @csrf
                <input class="form-control" type="number" name="quantity" value="1" min="1" max="{{ $product->stock_quantity }}">
                <button class="btn btn-ml" type="submit">Add to cart</button>
            </form>
            <form method="POST" action="{{ route('guest.quick', $product) }}" class="guest-quick card-ml p-3" id="guestQuick" data-checkout-guard>
                @csrf
                <h2 class="h6 mb-2">Or order in one step · no account</h2>
                <div class="row g-2">
                    <div class="col-md-4"><input class="form-control" name="name" placeholder="Your name" required value="{{ old('name') }}"></div>
                    <div class="col-md-4"><input class="form-control" name="phone" placeholder="Phone" required value="{{ old('phone') }}"></div>
                    <div class="col-md-2"><input class="form-control" type="number" name="quantity" value="1" min="1" max="{{ $product->stock_quantity }}" required></div>
                    <div class="col-md-2"><button class="btn btn-outline-ml w-100" type="submit">Order</button></div>
                </div>
                <p class="small muted mb-0 mt-2">Pickup tomorrow at the stall. Pay in Rs to the farmer.</p>
            </form>
            @endif
            <a class="btn btn-outline-ml btn-sm mt-2" href="{{ route('login') }}">Or log in</a>
        @endauth
    </div>
</div>
<h2 class="h5 mt-4">Reviews</h2>
@forelse($product->reviews->where('status','approved') as $review)
    <article class="review-public-card card-ml p-3 mb-2">
        <div class="d-flex justify-content-between gap-2 flex-wrap mb-1">
            @include('partials.star-rating', ['rating' => $review->rating])
            <span class="small muted">{{ $review->created_at?->format('M j, Y') }}</span>
        </div>
        <p class="review-admin-body mb-2">{{ $review->comment }}</p>
        <div class="small muted fw-bold">{{ $review->customer->name }}</div>
        @if($review->farmer_reply)
            <div class="review-admin-reply mt-2"><strong>Farmer reply:</strong> {{ $review->farmer_reply }}</div>
        @endif
    </article>
@empty
    <p class="muted">No reviews yet.</p>
@endforelse
@if($related->isNotEmpty())
    <h2 class="h5 mt-4">More in {{ $product->category->name }}</h2>
    <div class="row g-3">@foreach($related as $item)<div class="col-md-6 col-lg-3">@include('partials.product-card', ['product' => $item])</div>@endforeach</div>
@endif
@endsection
