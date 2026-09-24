@extends('layouts.guest')
@section('title', 'Log in')
@section('content')
<div class="card-ml p-4">
    <h1 class="h3">Welcome back</h1>
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <label class="form-label">Email</label><input class="form-control mb-2" type="email" name="email" value="{{ old('email') }}" required>
        <label class="form-label">Password</label><input class="form-control mb-2" type="password" name="password" required>
        <div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="remember" id="remember"><label for="remember">Remember me</label></div>
        <button class="btn btn-ml">Log in</button>
        <a class="ms-2" href="{{ route('password.request') }}">Forgot password</a>
    </form>
    <p class="mt-3 mb-0">New here? <a href="{{ route('register') }}">Create an account</a></p>
    <p class="small muted mt-3 mb-0">Demo: admin@marketlink.com / Admin@123 · farmer@marketlink.com / Farmer@123 · customer@marketlink.com / Customer@123</p>
</div>
@endsection
