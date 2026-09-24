@extends('layouts.app')
@section('title', 'MarketLink · Local farms, ready for pickup')
@section('content')
<section class="hero p-4 p-md-5 mb-5">
    <div class="row align-items-center">
        <div class="col-lg-7">
            <p class="hero-kicker">Farmers markets, without the guesswork</p>
            <h1 class="display-5">Meet the growers. Reserve your basket. Pick it up fresh.</h1>
            <p class="lead">MarketLink connects nearby markets and farmers with customers who want to pre-order produce and pay in person.</p>
            <div class="d-flex gap-2 flex-wrap">
                <a class="btn btn-light rounded-pill" href="{{ route('products.index') }}">Browse products</a>
                <a class="btn btn-outline-light rounded-pill" href="{{ route('markets.index') }}">Find a market</a>
            </div>
        </div>
        <div class="col-lg-5 mt-4 mt-lg-0">
            <div class="how-card p-4">
                <h2 class="h5">How it works</h2>
                <ol class="mb-0">
                    <li>Find a market or farmer near you.</li>
                    <li>Add produce to your cart.</li>
                    <li>Choose a pickup window before cutoff.</li>
                    <li>Pay the farmer when you collect your order.</li>
                </ol>
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
@endsection
