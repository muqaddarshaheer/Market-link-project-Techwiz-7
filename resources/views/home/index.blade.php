@extends('layouts.app')

@section('title', 'Home')

@section('content')
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <p class="text-uppercase small fw-semibold mb-2" style="letter-spacing:.12em;opacity:.85">MarketLink</p>
            <h1>Fresh from farmers markets near you</h1>
            <p>Browse local stalls, pre-order seasonal produce, and pick up in person — no delivery fees, no online payments.</p>
            <div class="d-flex flex-wrap gap-2 mt-4">
                <a href="{{ route('products.index') }}" class="btn btn-light btn-lg">Shop produce</a>
                <a href="{{ route('markets.index') }}" class="btn btn-outline-light btn-lg">Find markets</a>
            </div>
        </div>
    </div>
</section>

@if($announcements->isNotEmpty())
<section class="container py-4">
    @foreach($announcements as $a)
        <div class="alert alert-success border-0 panel mb-2 py-3">
            <strong>{{ $a->title }}</strong> — {{ Str::limit($a->message, 120) }}
        </div>
    @endforeach
</section>
@endif

<section class="container py-5 animate-in">
    <h2 class="section-title">Shop by category</h2>
    <p class="section-sub">Seasonal staples from approved local farmers.</p>
    <div class="row g-3">
        @foreach($categories as $cat)
            <div class="col-6 col-md-3 col-lg-2">
                <a href="{{ route('products.index', ['category_id' => $cat->id]) }}" class="text-decoration-none">
                    <div class="stat-tile text-center">
                        <i class="bi {{ $cat->icon ?: 'bi-basket' }} fs-3 text-success"></i>
                        <div class="fw-semibold mt-2">{{ $cat->name }}</div>
                        <small class="text-muted">{{ $cat->products_count }} items</small>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</section>

<section class="container pb-5">
    <div class="d-flex justify-content-between align-items-end mb-3">
        <div>
            <h2 class="section-title mb-1">Featured produce</h2>
            <p class="section-sub mb-0">Hand-picked picks ready for pre-order.</p>
        </div>
        <a href="{{ route('products.index') }}" class="btn btn-outline-primary">View all</a>
    </div>
    <div class="row g-4">
        @forelse($featuredProducts as $product)
            <div class="col-md-6 col-lg-3">
                @include('partials.product-card', ['product' => $product])
            </div>
        @empty
            <div class="col-12 empty-state">No products yet — check back soon.</div>
        @endforelse
    </div>
</section>

<section class="container pb-5">
    <h2 class="section-title">Nearby markets</h2>
    <p class="section-sub">Explore stalls on the map and plan your pickup.</p>
    <div class="row g-4">
        @foreach($markets as $market)
            <div class="col-md-4">
                <div class="market-card">
                    <img src="{{ $market->imageUrl() }}" alt="{{ $market->name }}">
                    <div class="p-3">
                        <h5 class="mb-1">{{ $market->name }}</h5>
                        <p class="text-muted small mb-2">{{ $market->city }} · {{ $market->products_count }} products</p>
                        <a href="{{ route('markets.show', $market) }}" class="btn btn-sm btn-primary">View market</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>

<section class="container pb-5">
    <h2 class="section-title">Meet the farmers</h2>
    <p class="section-sub">Approved growers ready for your pre-orders.</p>
    <div class="row g-4">
        @foreach($farmers as $farmer)
            <div class="col-md-4">
                <div class="farmer-card p-3 d-flex gap-3 align-items-center">
                    <img src="{{ $farmer->logoUrl() }}" alt="" class="rounded-circle" style="width:72px;height:72px;object-fit:cover">
                    <div>
                        <h5 class="mb-0">{{ $farmer->stall_name }}</h5>
                        <small class="text-muted">{{ $farmer->products_count }} products</small>
                        <div><a href="{{ route('farmers.show', $farmer) }}">Visit stall</a></div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>
@endsection
