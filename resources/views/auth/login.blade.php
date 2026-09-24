@extends('layouts.app')
@section('title', 'Login')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="panel">
                <h1 class="h3 display-font mb-3">Welcome back</h1>
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Login</button>
                </form>
                <p class="mt-3 mb-0 small">No account? <a href="{{ route('register') }}">Register</a></p>
                <hr>
                <p class="small text-muted mb-0">Demo: admin@marketlink.com / Admin@123</p>
            </div>
        </div>
    </div>
</div>
@endsection
