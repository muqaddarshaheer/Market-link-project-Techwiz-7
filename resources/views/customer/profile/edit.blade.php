@extends('layouts.app')
@section('title', 'Profile')
@section('content')
<div class="container py-5">
    <h1 class="display-font mb-4">Your profile</h1>
    <div class="row g-4">
        <div class="col-lg-6">
            <form method="POST" action="{{ route('customer.profile.update') }}" enctype="multipart/form-data" class="panel">
                @csrf @method('PUT')
                <div class="mb-3 text-center">
                    <img src="{{ $user->avatarUrl() }}" class="rounded-circle mb-2" style="width:96px;height:96px;object-fit:cover" alt="">
                    <input type="file" name="avatar" class="form-control" accept="image/*">
                </div>
                <div class="mb-3"><label class="form-label">Name</label><input class="form-control" name="name" value="{{ old('name', $user->name) }}" required></div>
                <div class="mb-3"><label class="form-label">Email</label><input class="form-control" value="{{ $user->email }}" disabled></div>
                <div class="mb-3"><label class="form-label">Phone</label><input class="form-control" name="phone" value="{{ old('phone', $user->phone) }}"></div>
                <div class="mb-3"><label class="form-label">Address</label><textarea class="form-control" name="address" rows="2">{{ old('address', $user->address) }}</textarea></div>
                <button class="btn btn-primary">Save profile</button>
            </form>
        </div>
        <div class="col-lg-6">
            <form method="POST" action="{{ route('customer.password.update') }}" class="panel">
                @csrf @method('PUT')
                <h5>Change password</h5>
                <div class="mb-3"><label class="form-label">Current</label><input type="password" name="current_password" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">New</label><input type="password" name="password" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Confirm</label><input type="password" name="password_confirmation" class="form-control" required></div>
                <button class="btn btn-outline-primary">Update password</button>
            </form>
        </div>
    </div>
</div>
@endsection
