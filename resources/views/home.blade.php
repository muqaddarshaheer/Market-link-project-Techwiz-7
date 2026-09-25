@extends('layouts.app')
@section('title', 'MarketLink · Local farms, ready for pickup')
@section('content')
<div class="home-page">
<section class="home-hero" aria-label="MarketLink intro">
    <div class="home-hero-media">
        <img src="{{ asset('images/produce/farm.jpg') }}" alt="" width="1600" height="900" fetchpriority="high">
    </div>
    <div class="home-hero-scrim"></div>
    <div class="home-hero-copy">
        <p class="home-hero-brand">MarketLink</p>
        <h1>Fresh from the field. Pickup at the market.</h1>
        <p class="home-hero-lead">Reserve produce in Rs, collect at the stall, and pay the farmer in person.</p>
        <div class="home-hero-actions">
            <a class="btn btn-light rounded-pill px-4" href="{{ route('markets.index') }}">Explore markets</a>
            <a class="btn btn-outline-light rounded-pill px-4" href="{{ route('products.index') }}">Browse produce</a>
        </div>
    </div>
</section>

<section class="mb-5 home-block reveal-up home-strip">
    <div class="row g-3 text-center">
        <div class="col-md-4"><div class="home-strip-item"><strong>No delivery</strong><span>You collect at the stall</span></div></div>
        <div class="col-md-4"><div class="home-strip-item"><strong>Pay in person</strong><span>Rs paid to the farmer</span></div></div>
        <div class="col-md-4"><div class="home-strip-item"><strong>Approved growers</strong><span>Stalls checked by admin</span></div></div>
    </div>
</section>

<section class="mb-5 home-block reveal-up">
    <div class="d-flex justify-content-between align-items-end mb-3 gap-3">
        <div>
            <h2 class="section-title mb-1">Featured markets</h2>
            <p class="home-sub mb-0">Nearby market days for reserved pickup.</p>
        </div>
        <a href="{{ route('markets.index') }}">See all</a>
    </div>
    <div class="row g-3 stagger-children">
        @foreach($markets as $market)
            <div class="col-md-6 col-lg-3">@include('partials.market-card', compact('market'))</div>
        @endforeach
    </div>
</section>

<section class="mb-5 home-block reveal-up">
    <div class="d-flex justify-content-between align-items-end mb-3 gap-3">
        <div>
            <h2 class="section-title mb-1">Growers this week</h2>
            <p class="home-sub mb-0">Trusted stalls packing for the weekend.</p>
        </div>
        <a href="{{ route('farmers.index') }}">See all</a>
    </div>
    <div class="row g-3 stagger-children">
        @foreach($farmers as $farmer)
            <div class="col-md-6 col-lg-3">@include('partials.farmer-card', compact('farmer'))</div>
        @endforeach
    </div>
</section>

<section class="mb-5 home-block reveal-up">
    <div class="mb-3">
        <h2 class="section-title mb-1">Featured produce</h2>
        <p class="home-sub mb-0">Seasonal picks ready for market pickup.</p>
    </div>
    <div class="row g-3 stagger-children">
        @foreach($products as $product)
            <div class="col-md-6 col-lg-3">@include('partials.product-card', compact('product'))</div>
        @endforeach
    </div>
</section>

<section class="mb-5 home-block reveal-up" id="harvest-calendar" data-products-url="{{ route('products.index') }}" aria-labelledby="harvest-title">
<script type="application/json" id="harvest-growers">@json($harvestGrowers)</script>
    <div class="harvest-compact">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
            <div>
                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                    <span class="harvest-badge pulse-soft">Harvest 2026</span>
                    <span class="harvest-status">Click a day</span>
                </div>
                <h2 class="section-title mb-0" id="harvest-title">What’s Growing?</h2>
            </div>
            <p class="small muted mb-0"><strong id="harvestCount">0</strong> events · <span id="harvestMonthLabel">January 2026</span></p>
        </div>
        <div class="harvest-shell card-ml">
            <div class="harvest-toolbar">
                <div class="d-flex flex-wrap gap-1 align-items-center">
                    <button type="button" class="btn btn-outline-ml btn-sm" id="harvestPrev" aria-label="Previous month"><i class="bi bi-chevron-left"></i></button>
                    <button type="button" class="btn btn-ml btn-sm" id="harvestToday">Today</button>
                    <button type="button" class="btn btn-outline-ml btn-sm" id="harvestNext" aria-label="Next month"><i class="bi bi-chevron-right"></i></button>
                </div>
                <div class="d-flex flex-wrap gap-1 align-items-center">
                    <input class="form-control form-control-sm harvest-search" id="harvestSearch" type="search" placeholder="Crop…" aria-label="Search crop">
                    <select class="form-select form-select-sm harvest-filter" id="harvestTypeFilter" aria-label="Filter by type">
                        <option value="">All</option>
                        <option value="Harvest">Harvest</option>
                        <option value="Arrival">Arrival</option>
                        <option value="Pickup">Pickup</option>
                        <option value="Season">Season</option>
                    </select>
                    <div class="btn-group btn-group-sm" role="group" aria-label="Calendar view">
                        <button type="button" class="btn btn-outline-ml active" id="harvestViewGrid">Grid</button>
                        <button type="button" class="btn btn-outline-ml" id="harvestViewList">List</button>
                    </div>
                </div>
            </div>
            <div class="harvest-legend" id="harvestLegend" aria-label="Event legend"></div>
            <div class="harvest-months" id="harvestMonths" role="tablist" aria-label="2026 months"></div>
            <p id="harvestMessage" class="visually-hidden"></p>
            <div id="harvestChips" class="harvest-chips"></div>
            <div class="harvest-weekdays" id="harvestWeekdays" aria-hidden="true"><span>S</span><span>M</span><span>T</span><span>W</span><span>T</span><span>F</span><span>S</span></div>
            <div class="harvest-grid" id="harvestGrid" role="grid" aria-label="2026 harvest calendar"></div>
            <div class="harvest-list" id="harvestList" hidden></div>
            <div id="harvestSelected" class="harvest-selected"></div>
        </div>
    </div>
</section>
<div id="harvestEventModalRoot"></div>

<section class="mb-5 home-block reveal-up story-band" aria-labelledby="story-title">
    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
        <span class="harvest-badge">Farmer Stories</span>
    </div>
    <div class="row g-4 align-items-center">
        <div class="col-lg-6">
            <div class="story-frame">
                <div class="story-ratio" id="storyVideo"></div>
            </div>
            <div class="d-flex gap-2 flex-wrap mt-2">
                <button class="btn btn-outline-ml btn-sm" type="button" id="storySound">Watch with Sound</button>
                <button class="btn btn-outline-ml btn-sm" type="button" id="storyPause">Pause Background Video</button>
            </div>
            <p class="small muted mt-2 mb-0" id="storyNote"></p>
        </div>
        <div class="col-lg-6">
            <h2 class="section-title mb-1" id="story-title">See How Our Farmers Grow Fresh</h2>
            <p class="home-sub">From seed to stall — the path your pickup takes.</p>
            <ol class="story-steps">
                <li><span>01</span><div><strong>Select quality seeds</strong><p>Seasonal varieties chosen for local soil.</p></div></li>
                <li><span>02</span><div><strong>Grow with care</strong><p>Responsible farming from bed to harvest.</p></div></li>
                <li><span>03</span><div><strong>Harvest at peak</strong><p>Picked when flavour is ready.</p></div></li>
                <li><span>04</span><div><strong>Bring it to market</strong><p>Packed for your pickup window.</p></div></li>
            </ol>
            <div class="d-flex gap-2 flex-wrap">
                <a class="btn btn-ml" href="{{ route('farmers.index') }}">Meet our farmers</a>
                <a class="btn btn-outline-ml" href="{{ route('markets.index') }}">Explore markets</a>
            </div>
        </div>
    </div>
</section>

<section class="mb-4 home-block reveal-up">
    <h2 class="section-title mb-1">Loved at local pickup</h2>
    <p class="home-sub">Notes from customers after collecting at the stall.</p>
    <div class="row g-3 stagger-children">
        @forelse($reviews as $review)
            <div class="col-md-4">
                <article class="home-quote">
                    @include('partials.star-rating', ['rating' => $review->rating])
                    <p class="mt-2 mb-2">{{ $review->comment }}</p>
                    <div class="small muted">{{ $review->customer->name }}</div>
                </article>
            </div>
        @empty
            <p class="muted">Reviews from completed pickups will show up here.</p>
        @endforelse
    </div>
</section>

<section class="home-cta-band mb-2 reveal-scale">
    <h2 class="section-title mb-2">Ready for your next pickup?</h2>
    <p class="mb-3">Browse markets, reserve produce, pay when you collect.</p>
    <div class="d-flex gap-2 flex-wrap">
        <a class="btn btn-ml" href="{{ route('markets.index') }}">Browse markets</a>
        <a class="btn btn-outline-ml" href="{{ route('register') }}">Create account</a>
    </div>
</section>
</div>
@endsection
@push('head')
<script>document.documentElement.classList.add('ml-motion');</script>
@endpush
@push('scripts')
<script src="{{ asset('js/home.js') }}?v={{ filemtime(public_path('js/home.js')) }}"></script>
@endpush
