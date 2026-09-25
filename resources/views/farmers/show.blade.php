@extends('layouts.app')
@section('title', $farmer->stall_name)
@section('content')
@php
    $photo = \App\Support\ImageStore::picture($farmer->logo, $farmer->stall_name);
    $phone = $farmer->user->phone ?? '';
@endphp
<section class="farmer-profile">
    <div class="farmer-profile-hero card-ml">
        <img src="{{ $photo }}" alt="{{ $farmer->stall_name }}" width="1200" height="520">
        <div class="farmer-profile-hero-copy">
            <span class="farmer-dir-badge">Verified</span>
            <h1 class="section-title text-white mb-1">{{ $farmer->stall_name }}</h1>
            <p class="mb-2 text-white-50">{{ $farmer->contact_person }} · {{ implode(', ', $farmer->operating_days ?? []) }}</p>
            @include('partials.star-rating', ['rating' => $farmer->reviews->avg('rating')])
            @if($phone)
                <div class="mt-3 d-flex gap-2 flex-wrap">
                    <a class="btn btn-ml" href="tel:{{ preg_replace('/\s+/', '', $phone) }}"><i class="bi bi-telephone-fill"></i> {{ $phone }}</a>
                    <a class="btn btn-light" href="{{ route('products.index', ['farmer' => $farmer->id]) }}">Browse products</a>
                </div>
            @endif
        </div>
    </div>
    <div class="row g-4 mt-1">
        <div class="col-lg-8">
            <p class="lead">{{ $farmer->business_description }}</p>
            @auth
                @if(auth()->user()->isCustomer())
                    <form method="POST" action="{{ route('customer.favorites.toggle') }}" class="mb-3">@csrf
                        <input type="hidden" name="farmer_id" value="{{ $farmer->id }}">
                        <button class="btn btn-outline-ml btn-sm">Save farmer</button>
                    </form>
                @endif
            @endauth
            <h2 class="h5">Products</h2>
            <div class="row g-3">
                @foreach($farmer->products as $product)
                    <div class="col-md-6">@include('partials.product-card', compact('product'))</div>
                @endforeach
            </div>
            <h2 class="h5 mt-4">Reviews</h2>
            @forelse($farmer->reviews as $review)
                <div class="card-ml p-3 mb-2">
                    @include('partials.star-rating', ['rating' => $review->rating])
                    <p class="mb-1">{{ $review->comment }}</p>
                    <div class="small muted">{{ $review->customer->name }} @if($review->product) · {{ $review->product->name }} @endif</div>
                </div>
            @empty
                <p class="muted">No reviews yet.</p>
            @endforelse
        </div>
        <div class="col-lg-4">
            <div class="card-ml p-3 mb-3 farmer-side-card">
                <h2 class="h6">Contact</h2>
                <p class="mb-1">{{ $farmer->contact_person }}</p>
                @if($phone)<p class="mb-2"><a href="tel:{{ preg_replace('/\s+/', '', $phone) }}">{{ $phone }}</a></p>@endif
                <p class="small muted mb-0">{{ $farmer->address }}</p>
            </div>
            <div class="card-ml p-3 mb-3">
                <h2 class="h6">Markets</h2>
                @foreach($farmer->markets as $market)
                    <div class="mb-2"><a href="{{ route('markets.show', $market) }}">{{ $market->name }}</a><div class="small muted">Stall {{ $market->pivot->stall_number ?: '—' }}</div></div>
                @endforeach
            </div>
            @if($farmer->latitude && $farmer->longitude)
                @include('partials.map', ['id' => 'farmer-map', 'points' => [['lat' => $farmer->latitude, 'lng' => $farmer->longitude, 'title' => $farmer->stall_name, 'subtitle' => $phone ?: $farmer->address]]])
            @endif
        </div>
    </div>
</section>
@endsection
