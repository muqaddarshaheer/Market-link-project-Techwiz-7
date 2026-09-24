@extends('layouts.app')
@section('title', 'Profile')
@section('content')
<h1 class="section-title">Profile</h1>
<div class="row g-4">
    <div class="col-lg-7">
        <form method="POST" action="{{ route('customer.profile.update') }}" enctype="multipart/form-data" class="card-ml p-4">
            @csrf @method('PUT')
            <label class="form-label">Name</label><input class="form-control mb-2" name="name" value="{{ old('name', auth()->user()->name) }}" required>
            <label class="form-label">Phone</label><input class="form-control mb-2" name="phone" value="{{ old('phone', auth()->user()->phone) }}" required>
            <label class="form-label">Address</label><textarea class="form-control mb-2" name="address" required>{{ old('address', auth()->user()->address) }}</textarea>
            <label class="form-label">Avatar</label><input class="form-control mb-3" type="file" name="avatar" accept="image/*">
            <button class="btn btn-ml">Save profile</button>
        </form>
    </div>
    <div class="col-lg-5">
        <form method="POST" action="{{ route('customer.password.update') }}" class="card-ml p-4">
            @csrf @method('PUT')
            <h2 class="h6">Change password</h2>
            <input class="form-control mb-2" type="password" name="current_password" placeholder="Current password" required>
            <input class="form-control mb-2" type="password" name="password" placeholder="New password" required>
            <input class="form-control mb-3" type="password" name="password_confirmation" placeholder="Confirm" required>
            <button class="btn btn-outline-ml">Update password</button>
        </form>
    </div>
</div>
@endsection
