@extends('layouts.app')
@section('title', $product->name)
@section('content')
<div class="container py-5">
    <div class="row g-4">
        <div class="col-md-5">
            <img src="{{ $product->imageUrl() }}" class="w-100 rounded" style="max-height:420px;object-fit:cover" alt="{{ $product->name }}">
        </div>
        <div class="col-md-7">
            <h1 class="display-font">{{ $product->name }}</h1>
            <p class="text-muted">{{ $product->category->name }} · {{ $product->market->name }}</p>
            <p class="fs-3 fw-bold text-success">${{ number_format($product->price, 2) }} <span class="fs-6 text-muted">/ {{ $product->unit }}</span></p>
            <p>{{ $product->description }}</p>
            <p>
                @if($product->isInStock())
                    <span class="badge bg-success">In stock ({{ $product->stock_quantity }})</span>
                @else
                    <span class="badge bg-secondary">Sold out</span>
                @endif
                <span class="ms-2 small text-muted">{{ $product->views_count }} views</span>
            </p>
            <p>Sold by <a href="{{ route('farmers.show', $product->farmer) }}">{{ $product->farmer->stall_name }}</a>
                · {{ number_format($product->averageRating(), 1) }} ★</p>

            @auth
                @if(auth()->user()->isCustomer())
                    @if($product->isInStock())
                        <form method="POST" action="{{ route('cart.store') }}" class="d-flex gap-2 align-items-end mb-3">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <div>
                                <label class="form-label">Qty</label>
                                <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock_quantity }}" class="form-control" style="width:100px">
                            </div>
                            <button class="btn btn-primary">Add to cart</button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('customer.favorites.toggle') }}">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <button class="btn btn-outline-secondary"><i class="bi bi-heart"></i> Favorite</button>
                    </form>
                @endif
            @else
                <a href="{{ route('login') }}" class="btn btn-primary">Login to pre-order</a>
            @endauth

            <div class="mt-3">
                <button class="btn btn-sm btn-outline-secondary" onclick="navigator.share ? navigator.share({title:@json($product->name),url:location.href}) : navigator.clipboard.writeText(location.href)">
                    <i class="bi bi-share"></i> Share
                </button>
            </div>
        </div>
    </div>

    <h3 class="mt-5">Reviews</h3>
    @forelse($reviews as $review)
        <div class="panel mb-2">
            <div class="star-rating">{{ str_repeat('★', $review->rating) }}</div>
            <p class="mb-1">{{ $review->comment }}</p>
            <small class="text-muted">{{ $review->customer->name }}</small>
            @auth
                <form class="d-inline ms-2" method="POST" action="{{ route('reviews.helpful', $review) }}">@csrf<button class="btn btn-link btn-sm p-0">Helpful ({{ $review->helpful_count }})</button></form>
            @endauth
        </div>
    @empty
        <p class="text-muted">No reviews yet.</p>
    @endforelse
    {{ $reviews->links() }}

    <h3 class="mt-5">Related</h3>
    <div class="row g-4">
        @foreach($related as $item)
            <div class="col-md-3">@include('partials.product-card', ['product' => $item])</div>
        @endforeach
    </div>
</div>
@endsection
