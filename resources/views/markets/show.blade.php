@extends('layouts.app')
@section('title', $market->name)
@section('content')
<div class="container py-5">
    <div class="row g-4">
        <div class="col-lg-7">
            <img src="{{ $market->imageUrl() }}" class="w-100 rounded mb-3" style="max-height:320px;object-fit:cover" alt="">
            <h1 class="display-font">{{ $market->name }}</h1>
            <p class="text-muted">{{ $market->address }}, {{ $market->city }}</p>
            <p>{{ $market->description }}</p>
            <p><strong>Hours:</strong>
                {{ $market->opening_time ? \Carbon\Carbon::parse($market->opening_time)->format('g:i A') : '—' }}
                –
                {{ $market->closing_time ? \Carbon\Carbon::parse($market->closing_time)->format('g:i A') : '—' }}
            </p>
            <p><strong>Days:</strong> {{ implode(', ', $market->operating_days ?? []) }}</p>
            @if($market->directionsUrl())
                <a class="btn btn-outline-primary" target="_blank" href="{{ $market->directionsUrl() }}">Directions (OpenStreetMap)</a>
            @endif
            @auth
                @if(auth()->user()->isCustomer())
                    <form class="d-inline" method="POST" action="{{ route('customer.favorites.toggle') }}">
                        @csrf
                        <input type="hidden" name="market_id" value="{{ $market->id }}">
                        <button class="btn btn-outline-secondary"><i class="bi bi-heart"></i> Favorite</button>
                    </form>
                @endif
            @endauth
        </div>
        <div class="col-lg-5">
            <div id="marketMap" class="map-box"></div>
        </div>
    </div>

    <h3 class="mt-5 mb-3">Farmers at this market</h3>
    <div class="row g-3">
        @forelse($market->farmers as $farmer)
            <div class="col-md-4">
                <div class="panel h-100">
                    <h5>{{ $farmer->stall_name }}</h5>
                    <p class="small text-muted">Stall {{ $farmer->pivot->stall_number ?? '—' }}</p>
                    <a href="{{ route('farmers.show', $farmer) }}">View profile</a>
                </div>
            </div>
        @empty
            <p class="text-muted">No approved farmers linked yet.</p>
        @endforelse
    </div>

    <h3 class="mt-5 mb-3">Products</h3>
    <div class="row g-4">
        @foreach($market->products as $product)
            <div class="col-md-3">@include('partials.product-card', ['product' => $product])</div>
        @endforeach
    </div>
</div>
@endsection
@push('scripts')
<script>
initMap('marketMap', [{
    lat: {{ (float)$market->latitude }},
    lng: {{ (float)$market->longitude }},
    popup: @json($market->name)
}], 15);
</script>
@endpush
