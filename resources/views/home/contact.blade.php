@extends('layouts.app')
@section('title', 'Contact')
@section('content')
<div class="container py-5">
    <div class="row g-4">
        <div class="col-lg-6">
            <h1 class="display-font mb-3">Contact us</h1>
            <p class="text-muted">Questions about pickup, farmer onboarding, or marketplace policy? Send a message.</p>
            <form method="POST" action="{{ route('contact.submit') }}" class="panel">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Message</label>
                    <textarea name="message" rows="5" class="form-control" required>{{ old('message') }}</textarea>
                </div>
                <button class="btn btn-primary" type="submit">Send message</button>
            </form>
        </div>
        <div class="col-lg-6">
            <div id="contactMap" class="map-box mb-3"></div>
            <p class="mb-0"><strong>Support:</strong> support@marketlink.com</p>
            <p class="text-muted">HQ marker is illustrative — markets are listed with real OSM coordinates.</p>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>initMap('contactMap', [{ lat: 40.7128, lng: -74.006, popup: '<strong>MarketLink</strong>' }]);</script>
@endpush
