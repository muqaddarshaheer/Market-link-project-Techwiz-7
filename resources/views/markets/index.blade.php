@extends('layouts.app')
@section('title', 'Markets')
@section('content')
<div class="container py-5">
    <h1 class="display-font mb-2">Farmers markets</h1>
    <p class="section-sub">Filter by city or operating day, then explore on the map.</p>

    <form class="row g-2 mb-4" method="GET">
        <div class="col-md-3"><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Search markets"></div>
        <div class="col-md-3">
            <select name="city" class="form-select">
                <option value="">All cities</option>
                @foreach($cities as $city)
                    <option value="{{ $city }}" @selected(request('city')==$city)>{{ $city }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="day" class="form-select">
                <option value="">Any day</option>
                @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)
                    <option value="{{ $day }}" @selected(request('day')==$day)>{{ $day }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3"><button class="btn btn-primary w-100">Filter</button></div>
    </form>

    <div id="marketsMap" class="map-box mb-4"></div>

    <div class="row g-4">
        @forelse($markets as $market)
            <div class="col-md-6 col-lg-4">
                <div class="market-card">
                    <img src="{{ $market->imageUrl() }}" alt="{{ $market->name }}">
                    <div class="p-3">
                        <h5>{{ $market->name }}</h5>
                        <p class="small text-muted mb-1">{{ $market->address }}, {{ $market->city }}</p>
                        <p class="small mb-2">{{ implode(', ', $market->operating_days ?? []) }}</p>
                        <a href="{{ route('markets.show', $market) }}" class="btn btn-sm btn-primary">Details</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">No markets match your filters.</div>
        @endforelse
    </div>
    <div class="mt-4">{{ $markets->links() }}</div>
</div>
@endsection
@push('scripts')
<script>initMap('marketsMap', @json($mapMarkets));</script>
@endpush
