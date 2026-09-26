@extends('layouts.farmer')
@section('title', 'Stall profile')
@section('content')
<div class="panel-head mb-4">
    <div>
        <p class="panel-kicker mb-1" data-i18n="prof.kicker">Stall</p>
        <h1 class="section-title mb-0" data-i18n="prof.title">Stall profile</h1>
        <p class="home-sub mb-0" data-i18n="prof.lead">Markets, hours, and the photo shoppers see.</p>
    </div>
    <a class="btn btn-outline-ml btn-sm" href="{{ route('farmer.account') }}" data-i18n="prof.accountLink">Account &amp; password</a>
</div>

<form method="POST" action="{{ route('farmer.profile.update') }}" enctype="multipart/form-data" class="card-ml panel-card p-4">
    @csrf
    @method('PUT')
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label" for="stall_name" data-i18n="prof.stallName">Stall name</label>
            <input class="form-control" id="stall_name" name="stall_name" value="{{ old('stall_name', $farmer->stall_name) }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="contact_person" data-i18n="prof.contact">Contact person</label>
            <input class="form-control" id="contact_person" name="contact_person" value="{{ old('contact_person', $farmer->contact_person) }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="phone" data-i18n="prof.phone">Phone</label>
            <input class="form-control" id="phone" name="phone" value="{{ old('phone', auth()->user()->phone) }}" required>
        </div>
        <div class="col-md-3">
            <label class="form-label" for="lat" data-i18n="prof.lat">Latitude</label>
            <input class="form-control" id="lat" name="latitude" value="{{ old('latitude', $farmer->latitude) }}">
        </div>
        <div class="col-md-3">
            <label class="form-label" for="lng" data-i18n="prof.lng">Longitude</label>
            <input class="form-control" id="lng" name="longitude" value="{{ old('longitude', $farmer->longitude) }}">
        </div>
        <div class="col-12">
            <label class="form-label" for="address" data-i18n="prof.address">Address</label>
            <textarea class="form-control" id="address" name="address" rows="2" required>{{ old('address', $farmer->address) }}</textarea>
        </div>
        <div class="col-12">
            <label class="form-label" for="business_description" data-i18n="prof.about">About the stall</label>
            <textarea class="form-control" id="business_description" name="business_description" rows="3">{{ old('business_description', $farmer->business_description) }}</textarea>
        </div>
        <div class="col-12">
            <span class="form-label d-block" data-i18n="prof.days">Operating days</span>
            @foreach([
                'Monday' => 'prof.Mon',
                'Tuesday' => 'prof.Tue',
                'Wednesday' => 'prof.Wed',
                'Thursday' => 'prof.Thu',
                'Friday' => 'prof.Fri',
                'Saturday' => 'prof.Sat',
                'Sunday' => 'prof.Sun',
            ] as $day => $key)
                <label class="me-2"><input type="checkbox" name="operating_days[]" value="{{ $day }}" @checked(in_array($day, old('operating_days', $farmer->operating_days ?? []), true))> <span data-i18n="{{ $key }}">{{ $day }}</span></label>
            @endforeach
        </div>
        <div class="col-12">
            <label class="form-label" for="logo" data-i18n="prof.logo">Stall logo</label>
            @if($farmer->logo || $farmer->stall_name)
                <div class="account-avatar-row mb-2">
                    <img class="account-avatar" src="{{ \App\Support\ImageStore::picture($farmer->logo, $farmer->stall_name) }}" alt="" width="72" height="72">
                    <span class="small muted" data-i18n="prof.currentPhoto">Current stall photo</span>
                </div>
            @endif
            <input class="form-control" id="logo" type="file" name="logo" accept="image/jpeg,image/png,image/webp">
        </div>
        <div class="col-12">
            <h2 class="h6" data-i18n="prof.markets">Markets</h2>
            @foreach($markets as $market)
                <label class="d-block mb-2">
                    <input type="checkbox" name="market_ids[]" value="{{ $market->id }}" @checked($farmer->markets->contains($market))>
                    {{ $market->name }}
                    <input class="form-control form-control-sm d-inline-block w-auto ms-1" name="stall_number[{{ $market->id }}]" placeholder="Stall #" value="{{ $farmer->markets->firstWhere('id', $market->id)?->pivot?->stall_number }}">
                </label>
            @endforeach
        </div>
    </div>
    <button class="btn btn-ml mt-3" type="submit" data-i18n="prof.save">Save profile</button>
</form>

<p class="small muted mt-3 mb-2" data-en="Click the map to set your stall pin." data-ur="نقشے پر کلک کر کے اپنا اسٹال پن لگائیں۔">Click the map to set your stall pin.</p>
@include('partials.map', [
    'id' => 'picker',
    'points' => $farmer->latitude
        ? [['lat' => $farmer->latitude, 'lng' => $farmer->longitude, 'title' => $farmer->stall_name, 'subtitle' => 'Stall']]
        : [['lat' => 30.2672, 'lng' => -97.7431, 'title' => 'Click to place stall', 'subtitle' => '']],
])
@endsection
