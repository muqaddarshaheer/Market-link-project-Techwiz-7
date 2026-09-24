@extends('layouts.app')
@section('title', 'MarketLink · Local farms, ready for pickup')
@section('content')
<section class="hero p-4 p-md-5 mb-5">
    <div class="row align-items-center">
        <div class="col-lg-7">
            <p class="hero-kicker">Farmers markets, without the guesswork</p>
            <h1 class="display-5">Fresh Local Products. Simple Market Pickup.</h1>
            <p class="lead">Find a market, reserve produce from approved farmers, and pay in person when you pick it up. There is no delivery and no online payment.</p>
            <form class="d-flex gap-2 mb-3" action="{{ route('search') }}" method="GET">
                <input class="form-control" name="q" placeholder="Search products, farmers or markets...">
                <button class="btn btn-light">Search</button>
            </form>
            <div class="d-flex gap-2 flex-wrap">
                <a class="btn btn-light rounded-pill" href="{{ route('markets.index') }}">Explore Markets</a>
                <a class="btn btn-outline-light rounded-pill" href="{{ route('products.index') }}">Browse Products</a>
                <a class="btn btn-outline-light rounded-pill" href="{{ route('register') }}">Become a Farmer</a>
            </div>
        </div>
        <div class="col-lg-5 mt-4 mt-lg-0">
            <div class="how-card p-4">
                <h2 class="h5">How it works</h2>
                <ol class="mb-2">
                    <li>Discover a market or stall.</li>
                    <li>Browse what is in stock this week.</li>
                    <li>Pre-order a pickup window before cutoff.</li>
                    <li>Pickup and pay the farmer at the stall.</li>
                </ol>
                <p class="mb-0"><strong>Payment is made in person at the market during pickup.</strong></p>
            </div>
        </div>
    </div>
</section>
<section class="mb-5">
    <div class="d-flex justify-content-between align-items-end mb-3">
        <h2 class="section-title mb-0">Featured markets</h2>
        <a href="{{ route('markets.index') }}">See all</a>
    </div>
    <div class="row g-3">
        @foreach($markets as $market)
            <div class="col-md-6 col-lg-3">@include('partials.market-card', compact('market'))</div>
        @endforeach
    </div>
</section>
<section class="mb-5">
    <div class="d-flex justify-content-between align-items-end mb-3">
        <h2 class="section-title mb-0">Growers this week</h2>
        <a href="{{ route('farmers.index') }}">See all</a>
    </div>
    <div class="row g-3">
        @foreach($farmers as $farmer)
            <div class="col-md-6 col-lg-3">@include('partials.farmer-card', compact('farmer'))</div>
        @endforeach
    </div>
</section>
<section class="mb-5">
    <h2 class="section-title mb-3">Featured produce</h2>
    <div class="row g-3">
        @foreach($products as $product)
            <div class="col-md-6 col-lg-3">@include('partials.product-card', compact('product'))</div>
        @endforeach
    </div>
</section>
<section class="mb-4">
    <h2 class="section-title mb-3">From the pickup line</h2>
    <div class="row g-3">
        @forelse($reviews as $review)
            <div class="col-md-4">
                <div class="card-ml p-3 h-100">
                    @include('partials.star-rating', ['rating' => $review->rating])
                    <p class="mt-2">{{ $review->comment }}</p>
                    <div class="small muted">{{ $review->customer->name }}</div>
                </div>
            </div>
        @empty
            <p class="muted">Reviews from completed pickups will show up here.</p>
        @endforelse
    </div>
</section>
<section class="card-ml p-4 p-md-5 mb-2">
    <h2 class="section-title">Ready for Saturday?</h2>
    <p>Discover a market, reserve a basket, and pay the grower when you pick it up. There is no online checkout and no delivery.</p>
    <div class="d-flex gap-2 flex-wrap">
        <a class="btn btn-ml" href="{{ route('markets.index') }}">Browse markets</a>
        <a class="btn btn-outline-ml" href="{{ route('register') }}">Become a farmer</a>
        <a class="btn btn-outline-ml" href="{{ route('register') }}">Register as a customer</a>
    </div>
</section>
@endsection
