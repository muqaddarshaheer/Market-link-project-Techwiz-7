@extends('layouts.app')
@section('title', 'Farmer profile')
@section('content')
<div class="container py-5">
    <h1 class="display-font mb-4">Stall profile</h1>
    <form method="POST" action="{{ route('farmer.profile.update') }}" enctype="multipart/form-data" class="panel">
        @csrf @method('PUT')
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Your name</label><input class="form-control" name="name" value="{{ auth()->user()->name }}" required></div>
            <div class="col-md-6"><label class="form-label">Phone</label><input class="form-control" name="phone" value="{{ auth()->user()->phone }}"></div>
            <div class="col-md-6"><label class="form-label">Stall name</label><input class="form-control" name="stall_name" value="{{ old('stall_name', $profile->stall_name) }}" required></div>
            <div class="col-md-6"><label class="form-label">Contact person</label><input class="form-control" name="contact_person" value="{{ old('contact_person', $profile->contact_person) }}" required></div>
            <div class="col-12"><label class="form-label">Description</label><textarea class="form-control" name="business_description" rows="3">{{ old('business_description', $profile->business_description) }}</textarea></div>
            <div class="col-12"><label class="form-label">Address</label><input class="form-control" name="address" value="{{ old('address', $profile->address) }}"></div>
            <div class="col-md-4"><label class="form-label">Latitude</label><input class="form-control" name="latitude" id="lat" value="{{ old('latitude', $profile->latitude) }}"></div>
            <div class="col-md-4"><label class="form-label">Longitude</label><input class="form-control" name="longitude" id="lng" value="{{ old('longitude', $profile->longitude) }}"></div>
            <div class="col-md-4"><label class="form-label">Logo</label><input type="file" name="logo" class="form-control" accept="image/*"></div>
            <div class="col-12"><div id="pickerMap" class="map-box"></div><small class="text-muted">Click the map to set coordinates.</small></div>
            <div class="col-12">
                <label class="form-label d-block">Operating days</label>
                @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)
                    <label class="me-2"><input type="checkbox" name="operating_days[]" value="{{ $day }}" @checked(in_array($day, $profile->operating_days ?? []))> {{ substr($day,0,3) }}</label>
                @endforeach
            </div>
            <div class="col-12">
                <h5>Markets</h5>
                @foreach($markets as $market)
                    <div class="border rounded p-2 mb-2">
                        <label><input type="checkbox" name="market_ids[]" value="{{ $market->id }}" @checked($profile->markets->contains($market->id))> {{ $market->name }}</label>
                        <div class="row g-2 mt-1">
                            <div class="col-md-4"><input class="form-control form-control-sm" name="stall_number[{{ $market->id }}]" placeholder="Stall #" value="{{ $profile->markets->firstWhere('id',$market->id)?->pivot?->stall_number }}"></div>
                            <div class="col-md-4"><input class="form-control form-control-sm" name="operating_day[{{ $market->id }}]" placeholder="Day" value="{{ $profile->markets->firstWhere('id',$market->id)?->pivot?->operating_day }}"></div>
                            <div class="col-md-4"><input class="form-control form-control-sm" name="pickup_notes[{{ $market->id }}]" placeholder="Pickup notes" value="{{ $profile->markets->firstWhere('id',$market->id)?->pivot?->pickup_notes }}"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <button class="btn btn-primary mt-3">Save profile</button>
    </form>
</div>
@endsection
@push('scripts')
<script>
const map = initMap('pickerMap', [{
    lat: {{ (float)($profile->latitude ?: 40.7128) }},
    lng: {{ (float)($profile->longitude ?: -74.0060) }},
    popup: 'Your stall'
}], 13);
if (map) {
    let marker;
    map.eachLayer(l => { if (l instanceof L.Marker) marker = l; });
    map.on('click', e => {
        document.getElementById('lat').value = e.latlng.lat.toFixed(8);
        document.getElementById('lng').value = e.latlng.lng.toFixed(8);
        if (marker) marker.setLatLng(e.latlng); else marker = L.marker(e.latlng).addTo(map);
    });
}
</script>
@endpush
