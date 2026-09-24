@extends('layouts.admin')
@section('title', 'Markets')
@section('content')
<h1 class="section-title">Markets</h1>
<form method="POST" action="{{ route('admin.markets.store') }}" enctype="multipart/form-data" class="card-ml p-3 mb-4">
    @csrf
    <div class="row g-2">
        <div class="col-md-4"><input class="form-control" name="name" placeholder="Name" required></div>
        <div class="col-md-4"><input class="form-control" name="city" placeholder="City" required></div>
        <div class="col-md-4"><input class="form-control" name="address" placeholder="Address" required></div>
        <div class="col-md-3"><input class="form-control" type="time" name="opening_time" required></div>
        <div class="col-md-3"><input class="form-control" type="time" name="closing_time" required></div>
        <div class="col-md-3"><input class="form-control" id="marketLat" name="latitude" placeholder="Latitude" required></div>
        <div class="col-md-3"><input class="form-control" id="marketLng" name="longitude" placeholder="Longitude" required></div>
        <div class="col-12"><div id="marketPicker" class="map-box"></div><div class="small muted">Click the map to set latitude and longitude.</div></div>
        <div class="col-md-6">@foreach(['Saturday','Sunday','Wednesday'] as $day)<label class="me-2"><input type="checkbox" name="operating_days[]" value="{{ $day }}" checked> {{ $day }}</label>@endforeach</div>
        <div class="col-md-3"><select class="form-select" name="status"><option>active</option><option>inactive</option></select></div>
        <div class="col-md-3"><input class="form-control" type="file" name="image"></div>
    </div>
    <button class="btn btn-ml mt-2">Add market</button>
</form>
@foreach($markets as $market)
<form method="POST" action="{{ route('admin.markets.update', $market) }}" class="card-ml p-3 mb-2" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="row g-2">
        <div class="col-md-3"><input class="form-control" name="name" value="{{ $market->name }}"></div>
        <div class="col-md-2"><input class="form-control" name="city" value="{{ $market->city }}"></div>
        <div class="col-md-3"><input class="form-control" name="address" value="{{ $market->address }}"></div>
        <div class="col-md-2"><input class="form-control" type="time" name="opening_time" value="{{ substr($market->opening_time,0,5) }}"></div>
        <div class="col-md-2"><input class="form-control" type="time" name="closing_time" value="{{ substr($market->closing_time,0,5) }}"></div>
        <div class="col-md-2"><input class="form-control" name="latitude" value="{{ $market->latitude }}"></div>
        <div class="col-md-2"><input class="form-control" name="longitude" value="{{ $market->longitude }}"></div>
        <div class="col-md-4">@foreach(['Saturday','Sunday','Wednesday','Friday'] as $day)<label class="me-2 small"><input type="checkbox" name="operating_days[]" value="{{ $day }}" @checked(in_array($day, $market->operating_days ?? []))> {{ $day }}</label>@endforeach</div>
        <div class="col-md-2"><select class="form-select" name="status"><option @selected($market->status==='active')>active</option><option @selected($market->status==='inactive')>inactive</option></select></div>
        <div class="col-md-2"><button class="btn btn-outline-ml">Save</button></div>
    </div>
</form>
<form method="POST" action="{{ route('admin.markets.destroy', $market) }}" onsubmit="return confirm('Delete market?')">@csrf @method('DELETE')<button class="btn btn-link text-danger">Delete {{ $market->name }}</button></form>
@endforeach
{{ $markets->links() }}
@endsection
@push('scripts')
<script>
const picker = L.map('marketPicker').setView([30.27, -97.74], 11);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution: '&copy; OpenStreetMap'}).addTo(picker);
let marker;
picker.on('click', (event) => {
    document.getElementById('marketLat').value = event.latlng.lat.toFixed(6);
    document.getElementById('marketLng').value = event.latlng.lng.toFixed(6);
    if (marker) marker.setLatLng(event.latlng); else marker = L.marker(event.latlng).addTo(picker);
});
</script>
@endpush
