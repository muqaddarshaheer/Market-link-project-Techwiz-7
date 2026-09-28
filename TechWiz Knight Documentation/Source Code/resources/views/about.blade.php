@extends('layouts.app')
@section('title', 'About MarketLink')
@section('content')
<div class="story-page">
    <section class="story-hero mb-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <p class="story-kicker">About MarketLink</p>
                <h1 class="section-title">Fresh From Local Growers. Closer to You.</h1>
                <p>MarketLink helps you find nearby markets, see what approved farmers are bringing this week, reserve a pickup window, and pay at the stall.</p>
                <div class="d-flex gap-2 flex-wrap">
                    <a class="btn btn-ml" href="{{ route('markets.index') }}">Explore Markets</a>
                    <a class="btn btn-outline-ml" href="{{ route('farmers.index') }}">Meet Our Farmers</a>
                </div>
            </div>
            <div class="col-lg-6">
                <img class="story-photo" src="{{ asset('images/produce/farm.jpg') }}" alt="A local farm field that supplies MarketLink stalls" width="960" height="640" decoding="async">
            </div>
        </div>
    </section>

    <section class="mb-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-5">
                <img class="story-photo" src="{{ asset('images/produce/field.jpg') }}" alt="Rows of crops growing for a weekend market" width="800" height="560" loading="lazy" decoding="async">
            </div>
            <div class="col-lg-7">
                <h2 class="section-title">Why MarketLink Exists</h2>
                <p>Customers get a clear picture of seasonal produce. Farmers get pre-orders before market day. Payment stays in person at the stall.</p>
                <ul class="story-points">
                    <li>Discover growers, markets, and what is in season</li>
                    <li>Reserve before the cutoff</li>
                    <li>Pick up at the market and pay the farmer directly</li>
                </ul>
            </div>
        </div>
    </section>

    <section class="mb-5">
        <h2 class="section-title">How MarketLink works</h2>
        <div class="row g-3">
            @foreach([
                ['01', 'Discover', 'Explore local farmers, markets and seasonal produce.'],
                ['02', 'Choose', 'Find the products you want from participating growers.'],
                ['03', 'Reserve', 'Reserve products for your selected local market pickup.'],
                ['04', 'Pick Up', 'Visit the market, collect your produce and pay the farmer directly.'],
            ] as [$n, $title, $text])
                <div class="col-6 col-lg-3">
                    <article class="card-ml story-step h-100 p-3">
                        <span>{{ $n }}</span>
                        <h3 class="h5">{{ $title }}</h3>
                        <p class="mb-0">{{ $text }}</p>
                    </article>
                </div>
            @endforeach
        </div>
    </section>

    <section class="mb-5">
        <div class="d-flex justify-content-between align-items-end gap-3 mb-3">
            <div>
                <h2 class="section-title mb-1">Built Around Local Growers</h2>
                <p class="mb-0">Meet the people packing this week’s stalls.</p>
            </div>
            <a href="{{ route('farmers.index') }}">Meet Our Farmers →</a>
        </div>
        <div class="row g-3">
            @forelse($farmers as $farmer)
                <div class="col-md-4">@include('partials.farmer-card', compact('farmer'))</div>
            @empty
                <p class="muted">Approved growers will appear here.</p>
            @endforelse
        </div>
    </section>

    <section class="mb-5">
        <h2 class="section-title">Freshness Has a Season.</h2>
        <p>What you see depends on what growers list for pickup.</p>
        <div class="row g-3">
            @foreach($products as $product)
                <div class="col-md-6 col-lg-3">@include('partials.product-card', compact('product'))</div>
            @endforeach
        </div>
    </section>

    <section class="card-ml story-cta p-4 p-md-5">
        <h2 class="section-title">Your Next Fresh Pickup Starts Here.</h2>
        <p>Browse markets, reserve produce, and pay when you collect.</p>
        <a class="btn btn-ml" href="{{ route('markets.index') }}">Browse markets</a>
    </section>
</div>
@endsection
