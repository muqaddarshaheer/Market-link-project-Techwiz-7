@extends('layouts.app')
@section('title', $farmer->stall_name)
@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="d-flex gap-3 align-items-center mb-3">
            <img class="avatar" style="width:72px;height:72px" src="{{ \App\Support\ImageStore::url($farmer->logo) }}" alt="">
            <div>
                <h1 class="section-title mb-0">{{ $farmer->stall_name }}</h1>
                <div class="muted">{{ $farmer->contact_person }} · {{ implode(', ', $farmer->operating_days ?? []) }}</div>
                @include('partials.star-rating', ['rating' => $farmer->reviews->avg('rating')])
            </div>
        </div>
        <p>{{ $farmer->business_description }}</p>
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
                @if($review->farmer_reply)<div class="timeline-note mt-2"><strong>Farmer:</strong> {{ $review->farmer_reply }}</div>@endif
                @auth
                    @if(auth()->user()->isCustomer())
                        <form method="POST" action="{{ route('customer.reviews.helpful', $review) }}">@csrf<button class="btn btn-link btn-sm">Helpful ({{ $review->helpful_count }})</button></form>
                    @endif
                @endauth
            </div>
        @empty
            <p class="muted">No reviews yet.</p>
        @endforelse
    </div>
    <div class="col-lg-4">
        <div class="card-ml p-3 mb-3">
            <h2 class="h6">Markets</h2>
            @foreach($farmer->markets as $market)
                <div class="mb-2"><a href="{{ route('markets.show', $market) }}">{{ $market->name }}</a><div class="small muted">Stall {{ $market->pivot->stall_number ?: '—' }}</div></div>
            @endforeach
        </div>
        @if($farmer->latitude && $farmer->longitude)
            @include('partials.map', ['id' => 'farmer-map', 'points' => [['lat' => $farmer->latitude, 'lng' => $farmer->longitude, 'title' => $farmer->stall_name, 'subtitle' => $farmer->address]]])
        @endif
    </div>
</div>
@endsection
