@extends('layouts.guest')
@section('content')
<form method="POST" action="{{ route('password.update') }}" class="card-ml p-4">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <h1 class="h4">Choose a new password</h1>
    <input class="form-control mb-2" type="email" name="email" value="{{ $email }}" required>
    <input class="form-control mb-2" type="password" name="password" required>
    <input class="form-control mb-3" type="password" name="password_confirmation" required>
    <button class="btn btn-ml">Update password</button>
</form>
@endsection
