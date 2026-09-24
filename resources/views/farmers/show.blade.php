@extends('layouts.app')
@section('title', $farmer->stall_name)
@section('content')
<div class="container py-5">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="d-flex gap-3 align-items-center mb-3">
                <img src="{{ $farmer->logoUrl() }}" class="rounded-circle" style="width:96px;height:96px;object-fit:cover" alt="">
                <div>
                    <h1 class="display-font mb-1">{{ $farmer->stall_name }}</h1>
                    <p class="mb-0 text-muted">{{ $farmer->contact_person }} · {{ number_format($avgRating, 1) }} ★</p>
                </div>
            </div>
            <p>{{ $farmer->business_description }}</p>
            <p><strong>Address:</strong> {{ $farmer->address }}</p>
            <p><strong>Days:</strong> {{ implode(', ', $farmer->operating_days ?? []) }}</p>
            @auth
                @if(auth()->user()->isCustomer())
                    <form method="POST" action="{{ route('customer.favorites.toggle') }}" class="d-inline">
                        @csrf
                        <input type="hidden" name="farmer_id" value="{{ $farmer->id }}">
                        <button class="btn btn-outline-primary"><i class="bi bi-heart"></i> Favorite farmer</button>
                    </form>
                @endif
            @endauth
        </div>
        <div class="col-lg-4">
            <div id="farmerMap" class="map-box"></div>
            <h6 class="mt-3">Markets</h6>
            <ul>
                @foreach($farmer->markets as $m)
                    <li><a href="{{ route('markets.show', $m) }}">{{ $m->name }}</a> (stall {{ $m->pivot->stall_number ?? '—' }})</li>
                @endforeach
            </ul>
        </div>
    </div>

    <h3 class="mt-5">Products</h3>
    <div class="row g-4">
        @foreach($farmer->products as $product)
            <div class="col-md-3">@include('partials.product-card', ['product' => $product])</div>
        @endforeach
    </div>

    <h3 class="mt-5">Reviews</h3>
    @forelse($reviews as $review)
        <div class="panel mb-3">
            <div class="star-rating">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5-$review->rating) }}</div>
            <p class="mb-1">{{ $review->comment }}</p>
            <small class="text-muted">{{ $review->customer->name }} · {{ $review->created_at->diffForHumans() }}</small>
            @if($review->farmer_reply)
                <div class="mt-2 p-2 rounded" style="background:var(--ml-cream)"><strong>Farmer reply:</strong> {{ $review->farmer_reply }}</div>
            @endif
        </div>
    @empty
        <p class="text-muted">No reviews yet.</p>
    @endforelse
    {{ $reviews->links() }}
</div>
@endsection
@push('scripts')
@if($farmer->latitude && $farmer->longitude)
<script>initMap('farmerMap', [{ lat: {{ (float)$farmer->latitude }}, lng: {{ (float)$farmer->longitude }}, popup: @json($farmer->stall_name) }], 14);</script>
@endif
@endpush
