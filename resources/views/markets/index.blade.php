@extends('layouts.app')
@section('title', 'Markets')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="section-title mb-0">Markets</h1>
</div>
<form class="row g-2 mb-3" method="GET">
    <div class="col-md-4"><select class="form-select" name="city"><option value="">All cities</option>@foreach($cities as $city)<option value="{{ $city }}" @selected(request('city')===$city)>{{ $city }}</option>@endforeach</select></div>
    <div class="col-md-4"><select class="form-select" name="day"><option value="">Any day</option>@foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)<option @selected(request('day')===$day)>{{ $day }}</option>@endforeach</select></div>
    <div class="col-md-4"><button class="btn btn-ml">Filter</button> <a href="{{ route('markets.index') }}">Clear</a></div>
</form>
<div class="mb-4">
    @include('partials.map', ['id' => 'markets-map', 'points' => $markets->map(fn ($m) => ['lat' => $m->latitude, 'lng' => $m->longitude, 'title' => $m->name, 'subtitle' => $m->city])->values()])
</div>
<div class="row g-3">
    @forelse($markets as $market)
        <div class="col-md-6 col-lg-4">@include('partials.market-card', compact('market'))</div>
    @empty
        <div class="empty-state"><i class="bi bi-geo"></i><p>No markets match those filters.</p></div>
    @endforelse
</div>
<div class="mt-3">{{ $markets->links() }}</div>
@endsection
