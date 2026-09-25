@extends('layouts.customer')
@section('title', 'Profile')
@section('content')
<div class="panel-head mb-4">
    <div>
        <p class="panel-kicker mb-1">Account</p>
        <h1 class="section-title mb-0">Your profile</h1>
    </div>
</div>
<div class="row g-4">
    <div class="col-lg-7">
        <form method="POST" action="{{ route('customer.profile.update') }}" enctype="multipart/form-data" class="card-ml panel-card p-4">
            @csrf @method('PUT')
            <h2 class="h6 mb-3">Contact details</h2>
            <label class="form-label" for="name">Name</label>
            <input class="form-control mb-2" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required>
            <label class="form-label" for="phone">Phone</label>
            <input class="form-control mb-2" id="phone" name="phone" value="{{ old('phone', auth()->user()->phone) }}" required>
            <label class="form-label" for="address">Address</label>
            <textarea class="form-control mb-2" id="address" name="address" rows="3" required>{{ old('address', auth()->user()->address) }}</textarea>
            <label class="form-label" for="avatar">Avatar</label>
            <input class="form-control mb-3" id="avatar" type="file" name="avatar" accept="image/*">
            <button class="btn btn-ml" type="submit">Save profile</button>
        </form>
    </div>
    <div class="col-lg-5">
        <form method="POST" action="{{ route('customer.password.update') }}" class="card-ml panel-card p-4">
            @csrf @method('PUT')
            <h2 class="h6 mb-3">Change PIN</h2>
            <p class="small muted mb-3">Use a 4-digit PIN — same as login.</p>
            @error('current_password')<div class="text-danger small mb-2">{{ $message }}</div>@enderror
            <label class="form-label" for="current_password">Current PIN</label>
            <input class="form-control mb-2" id="current_password" name="current_password" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" minlength="4" required autocomplete="current-password">
            <label class="form-label" for="password">New PIN</label>
            <input class="form-control mb-2" id="password" name="password" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" minlength="4" required autocomplete="new-password">
            <label class="form-label" for="password_confirmation">Confirm PIN</label>
            <input class="form-control mb-3" id="password_confirmation" name="password_confirmation" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" minlength="4" required autocomplete="new-password">
            <button class="btn btn-outline-ml" type="submit">Update PIN</button>
        </form>
    </div>
</div>
@endsection
