@extends('layouts.app')
@section('title', 'MarketLink · Local farms, ready for pickup')
@section('content')
<div class="home-page">
<section class="hero p-4 p-md-5 mb-5">
    <div class="row align-items-center">
        <div class="col-lg-7">
            <p class="hero-kicker">Farmers markets, without the guesswork</p>
            <h1 class="display-5">Fresh Local Products. Simple Market Pickup.</h1>
            <p class="lead">Find a market, reserve produce from approved farmers, and pay in person when you pick it up. There is no delivery and no online payment.</p>
            <form class="d-flex gap-2 mb-3" action="{{ route('search') }}" method="GET">
                <label class="visually-hidden" for="home-search">Search products, farmers or markets</label>
                <input id="home-search" class="form-control" name="q" placeholder="Search products, farmers or markets...">
                <button class="btn btn-light" type="submit">Search</button>
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
<section class="mb-5 home-block reveal-up">
    <div class="d-flex justify-content-between align-items-end mb-3 gap-3">
        <div>
            <h2 class="section-title mb-1">Featured markets</h2>
            <p class="home-sub mb-0">Find nearby market days and reserve produce directly from trusted growers.</p>
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
            <p class="home-sub mb-0">Meet the farmers preparing this week’s seasonal harvest.</p>
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
        <p class="home-sub mb-0">Seasonal favourites available for upcoming market pickup.</p>
    </div>
    <div class="row g-3 stagger-children">
        @foreach($products as $product)
            <div class="col-md-6 col-lg-3">@include('partials.product-card', compact('product'))</div>
        @endforeach
    </div>
</section>

<section class="mb-5 home-block reveal-up" id="harvest-calendar" data-products-url="{{ route('products.index') }}" aria-labelledby="harvest-title">
<script type="application/json" id="harvest-growers">@json($harvestGrowers)</script>
    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
        <span class="harvest-badge pulse-soft">Seasonal Harvest Guide</span>
        <span class="harvest-status">Updated for 2026</span>
    </div>
    <h2 class="section-title mb-1" id="harvest-title">What’s Growing in 2026?</h2>
    <p class="home-sub">Explore the seasonal harvest calendar — jump months, filter crop types, search crops, and open day details.</p>
    <div class="harvest-shell card-ml">
        <div class="harvest-toolbar mb-3">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <button type="button" class="btn btn-outline-ml btn-sm" id="harvestPrev" aria-label="Previous month"><i class="bi bi-chevron-left"></i></button>
                <button type="button" class="btn btn-ml btn-sm" id="harvestToday">Today</button>
                <button type="button" class="btn btn-outline-ml btn-sm" id="harvestNext" aria-label="Next month"><i class="bi bi-chevron-right"></i></button>
            </div>
            <div class="d-flex flex-wrap gap-2 align-items-center flex-grow-1 justify-content-end">
                <input class="form-control form-control-sm harvest-search" id="harvestSearch" type="search" placeholder="Search crop…" aria-label="Search crop">
                <select class="form-select form-select-sm harvest-filter" id="harvestTypeFilter" aria-label="Filter by type">
                    <option value="">All types</option>
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
        <div class="harvest-legend mb-2" id="harvestLegend" aria-label="Event legend"></div>
        <div class="harvest-months" id="harvestMonths" role="tablist" aria-label="2026 months"></div>
        <div class="d-flex justify-content-between align-items-end gap-2 mb-2">
            <p class="harvest-kicker mb-0" id="harvestMonthLabel">January 2026</p>
            <p class="small muted mb-0"><strong id="harvestCount">0</strong> events · click a marked day</p>
        </div>
        <p id="harvestMessage" class="visually-hidden"></p>
        <div id="harvestChips" class="harvest-chips mb-2"></div>
        <div id="harvestSelected" class="harvest-selected mb-2"></div>
        <div class="harvest-weekdays" id="harvestWeekdays" aria-hidden="true"><span>Sun</span><span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span></div>
        <div class="harvest-grid" id="harvestGrid" role="grid" aria-label="2026 harvest calendar"></div>
        <div class="harvest-list" id="harvestList" hidden></div>
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
            <p class="home-sub">From seed to market stall, meet the growers who care for every crop and bring seasonal produce to your community.</p>
            <ol class="story-steps">
                <li><span>01</span><div><strong>Select Quality Seeds</strong><p>Farmers choose suitable seasonal seeds.</p></div></li>
                <li><span>02</span><div><strong>Grow With Care</strong><p>Crops are nurtured with responsible farming practices.</p></div></li>
                <li><span>03</span><div><strong>Harvest at Peak Freshness</strong><p>Produce is collected at the right time.</p></div></li>
                <li><span>04</span><div><strong>Bring It to Your Market</strong><p>Farmers prepare fresh produce for local pickup.</p></div></li>
            </ol>
            <ul class="hero-trust story-trust">
                <li>Local Farmers</li>
                <li>Seasonal Produce</li>
                <li>Pickup Freshness</li>
            </ul>
            <div class="d-flex gap-2 flex-wrap">
                <a class="btn btn-ml" href="{{ route('farmers.index') }}">Meet Our Farmers</a>
                <a class="btn btn-outline-ml" href="{{ route('markets.index') }}">Explore Markets</a>
            </div>
        </div>
    </div>
</section>

<section class="mb-4 home-block reveal-up">
    <h2 class="section-title mb-1">Loved at Local Pickup</h2>
    <p class="home-sub">Real feedback from customers after collecting fresh produce from their local market.</p>
    <div class="row g-3 stagger-children">
        @forelse($reviews as $review)
            <div class="col-md-4">
                <article class="card-ml p-3 h-100">
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
<section class="card-ml home-cta p-4 p-md-5 mb-2 reveal-scale">
    <span class="cta-orb" aria-hidden="true"></span>
    <h2 class="section-title">Your Next Fresh Pickup Starts Here.</h2>
    <p>Browse nearby markets, reserve produce from local growers, and pay when you collect your order.</p>
    <div class="d-flex gap-2 flex-wrap">
        <a class="btn btn-ml" href="{{ route('markets.index') }}">Browse markets</a>
        <a class="btn btn-outline-ml" href="{{ route('register') }}">Become a farmer</a>
        <a class="btn btn-outline-ml" href="{{ route('register') }}">Register as a customer</a>
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
