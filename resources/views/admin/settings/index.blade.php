@extends('layouts.app')
@section('title', 'Settings')
@section('content')
<div class="container py-4">
    <h1 class="h3 display-font mb-3">System settings</h1>
    <form method="POST" action="{{ route('admin.settings.update') }}" class="panel col-lg-6">
        @csrf @method('PUT')
        <div class="mb-3"><label class="form-label">Site tagline</label><input class="form-control" name="site_tagline" value="{{ $settings['site_tagline'] }}" required></div>
        <div class="mb-3"><label class="form-label">Support email</label><input type="email" class="form-control" name="support_email" value="{{ $settings['support_email'] }}" required></div>
        <div class="mb-3"><label class="form-label">Default cutoff hours</label><input type="number" class="form-control" name="default_cutoff_hours" value="{{ $settings['default_cutoff_hours'] }}" required></div>
        <div class="mb-3"><label class="form-label">Low stock threshold</label><input type="number" class="form-control" name="low_stock_threshold" value="{{ $settings['low_stock_threshold'] }}" required></div>
        <button class="btn btn-primary">Save settings</button>
    </form>
</div>
@endsection
