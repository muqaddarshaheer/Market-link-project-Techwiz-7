@extends('layouts.guest')
@section('title', 'Log in')
@section('content')
<div class="login-card card-ml p-4 p-md-5">
    <p class="login-kicker">MarketLink</p>
    <h1 class="h3 mb-1">Welcome back</h1>
    <p class="muted mb-4">Sign in to pre-order, manage a stall, or open the admin desk.</p>
    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    <form method="POST" action="{{ route('login') }}" id="loginForm">
        @csrf
        <label class="form-label" for="email">Email</label>
        <div class="login-field mb-3">
            <i class="bi bi-envelope"></i>
            <input class="form-control @error('email') is-invalid @enderror" id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="you@marketlink.com">
        </div>
        @error('email')<div class="text-danger small mb-2">{{ $message }}</div>@enderror
        <label class="form-label" for="password">Password</label>
        <div class="login-field mb-3">
            <i class="bi bi-lock"></i>
            <input class="form-control" id="password" type="password" name="password" required autocomplete="current-password" placeholder="Password">
            <button class="login-eye" type="button" aria-label="Show password" onclick="const input=document.getElementById('password'); const show=input.type==='password'; input.type=show?'text':'password'; this.setAttribute('aria-label', show?'Hide password':'Show password'); this.innerHTML=show?'<i class=\'bi bi-eye-slash\'></i>':'<i class=\'bi bi-eye\'></i>';"><i class="bi bi-eye"></i></button>
        </div>
        <div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="remember" id="remember"><label for="remember">Remember me</label></div>
        <button class="btn btn-ml w-100" id="loginBtn" type="submit">Log in</button>
        <a class="d-inline-block mt-3" href="{{ route('password.request') }}">Forgot password</a>
    </form>
    <p class="mt-3 mb-0">New here? <a href="{{ route('register') }}">Create an account</a></p>
    <p class="small muted mt-3 mb-0">Demo: admin@marketlink.com / Admin@123 · farmer@marketlink.com / Farmer@123 · customer@marketlink.com / Customer@123</p>
</div>
<script>
document.getElementById('loginForm').addEventListener('submit', function () {
    const button = document.getElementById('loginBtn');
    button.disabled = true;
    button.textContent = 'Signing in…';
});
</script>
@endsection
