@extends('layouts.farmer')
@section('title', 'Stall profile')
@section('content')
<h1 class="section-title">Stall profile</h1>
<form method="POST" action="{{ route('farmer.profile.update') }}" enctype="multipart/form-data" class="card-ml p-4">
    @csrf @method('PUT')
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label">Stall name</label><input class="form-control" name="stall_name" value="{{ $farmer->stall_name }}" required></div>
        <div class="col-md-6"><label class="form-label">Contact person</label><input class="form-control" name="contact_person" value="{{ $farmer->contact_person }}" required></div>
        <div class="col-md-6"><label class="form-label">Phone</label><input class="form-control" name="phone" value="{{ auth()->user()->phone }}" required></div>
        <div class="col-md-3"><label class="form-label">Latitude</label><input class="form-control" name="latitude" id="lat" value="{{ $farmer->latitude }}"></div>
        <div class="col-md-3"><label class="form-label">Longitude</label><input class="form-control" name="longitude" id="lng" value="{{ $farmer->longitude }}"></div>
        <div class="col-12"><label class="form-label">Address</label><textarea class="form-control" name="address" required>{{ $farmer->address }}</textarea></div>
        <div class="col-12"><label class="form-label">About the stall</label><textarea class="form-control" name="business_description">{{ $farmer->business_description }}</textarea></div>
        <div class="col-12">@foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)<label class="me-2"><input type="checkbox" name="operating_days[]" value="{{ $day }}" @checked(in_array($day, $farmer->operating_days ?? []))> {{ $day }}</label>@endforeach</div>
        <div class="col-12"><label class="form-label">Logo</label><input class="form-control" type="file" name="logo" accept="image/*"></div>
        <div class="col-12"><h2 class="h6">Markets</h2>
            @foreach($markets as $market)
                <label class="d-block"><input type="checkbox" name="market_ids[]" value="{{ $market->id }}" @checked($farmer->markets->contains($market))> {{ $market->name }}
                    <input class="form-control form-control-sm d-inline-block w-auto" name="stall_number[{{ $market->id }}]" placeholder="Stall #" value="{{ $farmer->markets->firstWhere('id', $market->id)?->pivot?->stall_number }}">
                </label>
            @endforeach
        </div>
    </div>
    <button class="btn btn-ml mt-3">Save profile</button>
</form>
<p class="small muted mt-2">Click the map to set your stall pin.</p>
@include('partials.map', ['id'=>'picker', 'points'=> $farmer->latitude ? [['lat'=>$farmer->latitude,'lng'=>$farmer->longitude,'title'=>$farmer->stall_name,'subtitle'=>'Stall']] : [['lat'=>30.2672,'lng'=>-97.7431,'title'=>'Click to place stall','subtitle'=>'']]])
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const map = window.L && document.getElementById('picker') && L.map && null;
    setTimeout(() => {
        const el = document.querySelector('#picker');
        if (!el || !el._leaflet_id) return;
        const instance = Object.values(el).find(v => v && v._container);
    }, 400);
});
</script>
@endpush
