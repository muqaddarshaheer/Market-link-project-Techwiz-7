@extends('layouts.app')
@section('title', 'MarketLink · Local farms, ready for pickup')

@section('fullbleed')
@php
    $heroPicks = collect($products ?? [])->take(3);
@endphp
<section class="ml-hero ml-hero-fresh" aria-label="MarketLink intro">
    <div class="ml-hero-atmosphere" aria-hidden="true"></div>
    <div class="ml-hero-fresh-inner">
        <div class="ml-hero-copy">
            <p class="ml-hero-brand" aria-label="MarketLink">
                <span class="ml-hero-brand-track" aria-hidden="true">
                    <span class="ml-hero-brand-word" style="--w:0">
                        <span class="ml-hero-brand-letter" style="--i:0">M</span><span class="ml-hero-brand-letter" style="--i:1">a</span><span class="ml-hero-brand-letter" style="--i:2">r</span><span class="ml-hero-brand-letter" style="--i:3">k</span><span class="ml-hero-brand-letter" style="--i:4">e</span><span class="ml-hero-brand-letter" style="--i:5">t</span>
                    </span>
                    <span class="ml-hero-brand-word ml-hero-brand-word-link" style="--w:1">
                        <span class="ml-hero-brand-letter" style="--i:0">L</span><span class="ml-hero-brand-letter" style="--i:1">i</span><span class="ml-hero-brand-letter" style="--i:2">n</span><span class="ml-hero-brand-letter" style="--i:3">k</span>
                    </span>
                </span>
            </p>
            <h1 class="ml-hero-title">Fresh from local stalls.<em>Ready for pickup.</em></h1>
            <p class="ml-hero-lead">Shop fruits and vegetables from approved growers — reserve online, collect at the market, and pay the farmer in person.</p>
            <div class="ml-hero-cta">
                <a class="btn btn-ml ml-hero-btn" href="{{ route('products.index') }}">Shop Fresh Now</a>
                <a class="ml-hero-link" href="{{ route('markets.index') }}">Browse markets <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
            </div>
        </div>

        <div class="ml-hero-stage" aria-hidden="false">
            <div class="ml-hero-phone" aria-label="MarketLink on mobile">
                <div class="ml-hero-phone-bezel">
                    <div class="ml-hero-phone-notch" aria-hidden="true"></div>
                    <div class="ml-hero-phone-screen">
                        <div class="ml-hero-phone-top">
                            <strong>MarketLink</strong>
                            <span>Fresh picks · Pickup</span>
                        </div>
                        <div class="ml-hero-phone-search" aria-hidden="true"><i class="bi bi-search"></i> Search produce…</div>
                        <p class="ml-hero-phone-label">Fresh picks</p>
                        @forelse($heroPicks as $pick)
                            <a class="ml-hero-phone-item" href="{{ route('products.show', $pick) }}">
                                <img src="{{ \App\Support\ImageStore::picture($pick->image, $pick->name) }}" alt="">
                                <span>
                                    <strong>{{ $pick->name }}</strong>
                                    <small>{{ money($pick->price) }}/{{ $pick->unit }}</small>
                                </span>
                            </a>
                        @empty
                            <a class="ml-hero-phone-item" href="{{ route('products.index') }}">
                                <img src="{{ asset('images/produce/cherry-tomatoes.jpg') }}" alt="">
                                <span><strong>Cherry tomatoes</strong><small>See shop</small></span>
                            </a>
                            <a class="ml-hero-phone-item" href="{{ route('products.index') }}">
                                <img src="{{ asset('images/produce/peaches.jpg') }}" alt="">
                                <span><strong>Peaches</strong><small>See shop</small></span>
                            </a>
                            <a class="ml-hero-phone-item" href="{{ route('products.index') }}">
                                <img src="{{ asset('images/produce/honey.jpg') }}" alt="">
                                <span><strong>Local honey</strong><small>See shop</small></span>
                            </a>
                        @endforelse
                    </div>
                </div>
            </div>

            <img class="ml-hero-veg ml-hero-veg-a" src="{{ asset('images/produce/salad.jpg') }}" alt="" width="200" height="200" fetchpriority="high">
            <img class="ml-hero-veg ml-hero-veg-b" src="{{ asset('images/produce/cherry-tomatoes.jpg') }}" alt="" width="150" height="150" fetchpriority="high">
            <img class="ml-hero-veg ml-hero-veg-c" src="{{ asset('images/produce/sweet-corn.jpg') }}" alt="" width="130" height="130" fetchpriority="high">
            <img class="ml-hero-veg ml-hero-veg-d" src="{{ asset('images/produce/peaches.jpg') }}" alt="" width="120" height="120" fetchpriority="high">
            <img class="ml-hero-veg ml-hero-veg-e" src="{{ asset('images/produce/blueberries.jpg') }}" alt="" width="110" height="110">
            <img class="ml-hero-veg ml-hero-veg-f" src="{{ asset('images/produce/basil.jpg') }}" alt="" width="100" height="100">
        </div>
    </div>
</section>
@endsection

@section('content')
<div class="home-page">
<section class="mb-5 home-block reveal-up home-strip">
    <div class="row g-3">
        <div class="col-md-4">
            <div class="home-strip-item">
                <i class="bi bi-shop" aria-hidden="true"></i>
                <div><strong>No delivery</strong><span>You collect at the stall</span></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="home-strip-item">
                <i class="bi bi-cash-coin" aria-hidden="true"></i>
                <div><strong>Pay in person</strong><span>Rs paid to the farmer</span></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="home-strip-item">
                <i class="bi bi-patch-check" aria-hidden="true"></i>
                <div><strong>Approved growers</strong><span>Stalls checked by admin</span></div>
            </div>
        </div>
    </div>
</section>

<section class="mb-5 home-block reveal-up">
    <div class="section-head">
        <div>
            <h2 class="section-title mb-1">Featured markets</h2>
            <p class="home-sub mb-0">Nearby market days for reserved pickup.</p>
        </div>
        <a class="section-more" href="{{ route('markets.index') }}">See all</a>
    </div>
    <div class="row g-3 stagger-children">
        @foreach($markets as $market)
            <div class="col-md-6 col-lg-3">@include('partials.market-card', compact('market'))</div>
        @endforeach
    </div>
</section>

<section class="mb-5 home-block reveal-up">
    <div class="section-head">
        <div>
            <h2 class="section-title mb-1">Growers this week</h2>
            <p class="home-sub mb-0">Trusted stalls packing for the weekend.</p>
        </div>
        <a class="section-more" href="{{ route('farmers.index') }}">See all</a>
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
    <div class="hc">
        <div class="hc-head">
            <div>
                <span class="harvest-badge">Harvest 2026</span>
                <h2 class="section-title mt-2 mb-1" id="harvest-title">What’s Growing?</h2>
                <p class="home-sub mb-0" id="harvestMessage">Season guide for stall pickup — no delivery.</p>
            </div>
            <div class="hc-stat">
                <strong id="harvestCount">0</strong>
                <span id="harvestMonthLabel">January 2026</span>
            </div>
        </div>

        <div class="hc-card">
            <div class="hc-toolbar">
                <div class="hc-toolbar-left">
                    <button type="button" class="btn btn-outline-ml btn-sm" id="harvestPrev" aria-label="Previous month"><i class="bi bi-chevron-left"></i></button>
                    <button type="button" class="btn btn-ml btn-sm" id="harvestToday">Today</button>
                    <button type="button" class="btn btn-outline-ml btn-sm" id="harvestNext" aria-label="Next month"><i class="bi bi-chevron-right"></i></button>
                    <button type="button" class="btn btn-outline-ml btn-sm" id="harvestNextEvent">Next event</button>
                    <span class="hc-hint" id="harvestStatus">Pick a highlighted day</span>
                </div>
                <div class="hc-toolbar-right">
                    <input class="form-control form-control-sm" id="harvestSearch" type="search" placeholder="Search crop…" aria-label="Search crop">
                    <select class="form-select form-select-sm" id="harvestTypeFilter" aria-label="Filter by type">
                        <option value="">All types</option>
                        <option value="Harvest">Harvest</option>
                        <option value="Arrival">Arrival</option>
                        <option value="Pickup">Pickup</option>
                        <option value="Season">Season</option>
                        <option value="fav">★ Saved</option>
                    </select>
                    <div class="btn-group btn-group-sm" role="group" aria-label="Calendar view">
                        <button type="button" class="btn btn-outline-ml active" id="harvestViewGrid">Grid</button>
                        <button type="button" class="btn btn-outline-ml" id="harvestViewList">List</button>
                    </div>
                </div>
            </div>

            <div class="hc-months" id="harvestMonths" role="tablist" aria-label="2026 months"></div>
            <div class="hc-progress" aria-hidden="true"><span id="harvestSeasonFill"></span></div>
            <div class="hc-legend" id="harvestLegend" aria-label="Event legend"></div>

            <div class="hc-weekdays" id="harvestWeekdays" aria-hidden="true"><span>Sun</span><span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span></div>
            <div class="hc-grid" id="harvestGrid" role="grid" aria-label="2026 harvest calendar"></div>
            <div class="hc-list" id="harvestList" hidden></div>

            <div class="hc-footer">
                <div id="harvestChips" class="hc-chips"></div>
                <div class="hc-upcoming-wrap">
                    <p class="hc-label">Coming up</p>
                    <div id="harvestUpcoming" class="hc-upcoming"></div>
                </div>
            </div>

            <div id="harvestSelected" class="hc-selected"></div>

            {{-- Keep JS hooks without cluttering the UI --}}
            <div class="visually-hidden" aria-hidden="true">
                <div id="harvestWeekStrip"></div>
                <div id="harvestFavorites"></div>
                <button type="button" id="harvestClearFavs"></button>
                <button type="button" id="harvestCopyMonth"></button>
                <button type="button" id="harvestPrintMonth"></button>
            </div>
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
    @if($reviews->isNotEmpty())
        <div class="review-marquee" aria-label="Customer reviews">
            <div class="review-marquee-track">
                @foreach([0, 1] as $loopCopy)
                    @foreach($reviews as $review)
                        <article class="home-quote review-marquee-card">
                            @include('partials.star-rating', ['rating' => $review->rating])
                            <p class="mt-2 mb-2">{{ $review->comment }}</p>
                            <div class="small muted">{{ $review->customer->name }}</div>
                        </article>
                    @endforeach
                @endforeach
            </div>
        </div>
    @else
        <p class="muted">Reviews from completed pickups will show up here.</p>
    @endif
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
