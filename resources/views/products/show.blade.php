@extends('layouts.app')
@section('title', $product->name)
@section('content')
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card-ml thumb" style="height:360px">
            @if($product->image)<img src="{{ \App\Support\ImageStore::url($product->image) }}" alt="{{ $product->name }}">@else<i class="bi bi-basket"></i>@endif
        </div>
    </div>
    <div class="col-lg-6">
        <span class="badge badge-soft">{{ $product->category->name }}</span>
        <h1 class="section-title">{{ $product->name }}</h1>
        @include('partials.star-rating', ['rating' => $product->reviews->where('status','approved')->avg('rating'), 'count' => $product->reviews->count()])
        <p class="fs-4">${{ number_format($product->price, 2) }} / {{ $product->unit }}</p>
        <p>{{ $product->description }}</p>
        <p class="muted">{{ $product->stock_quantity }} in stock · {{ $product->views_count }} views</p>
        <p><a href="{{ route('farmers.show', $product->farmer) }}">{{ $product->farmer->stall_name }}</a> at <a href="{{ route('markets.show', $product->market) }}">{{ $product->market->name }}</a></p>
        @auth
            @if(auth()->user()->isCustomer() && $product->canPurchase())
                <form method="POST" action="{{ route('cart.add', $product) }}" class="d-flex gap-2 mb-2">
                    @csrf
                    <input class="form-control" style="max-width:100px" type="number" name="quantity" value="1" min="1" max="{{ $product->stock_quantity }}">
                    <button class="btn btn-ml">Add to cart</button>
                </form>
                <form method="POST" action="{{ route('customer.favorites.toggle') }}">@csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <button class="btn btn-outline-ml btn-sm">Save product</button>
                </form>
            @endif
        @else
            <a class="btn btn-ml" href="{{ route('login') }}">Log in to pre-order</a>
        @endauth
        <div class="mt-3 d-flex gap-2">
            <a class="btn btn-outline-ml btn-sm" target="_blank" href="https://twitter.com/intent/tweet?text={{ urlencode($product->name) }}&url={{ urlencode(request()->fullUrl()) }}">Share</a>
            <a class="btn btn-outline-ml btn-sm" target="_blank" href="https://wa.me/?text={{ urlencode($product->name.' '.request()->fullUrl()) }}">WhatsApp</a>
        </div>
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
    <div class="row g-3">@foreach($related as $item)<div class="col-md-3">@include('partials.product-card', ['product' => $item])</div>@endforeach</div>
@endif
@endsection
