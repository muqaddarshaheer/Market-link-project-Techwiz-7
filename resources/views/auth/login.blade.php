@extends('layouts.guest')
@section('title', 'Log in')
@section('content')
<div class="login-card card-ml p-4 p-md-5">
    <p class="login-kicker">MarketLink</p>
    <h1 class="h3 mb-1">Welcome back</h1>
    <p class="muted mb-4">Enter your email and 4-digit PIN. We open the right dashboard for your account.</p>
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
        <label class="form-label" for="pin">4-digit PIN</label>
        <div class="login-field mb-3">
            <i class="bi bi-lock"></i>
            <input class="form-control" id="pin" name="pin" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" minlength="4" required autocomplete="current-password" placeholder="0000">
        </div>
        @error('pin')<div class="text-danger small mb-2">{{ $message }}</div>@enderror
        <div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="remember" id="remember"><label for="remember">Remember me</label></div>
        <button class="btn btn-ml w-100" id="loginBtn" type="submit">Log in</button>
        <a class="d-inline-block mt-3" href="{{ route('password.request') }}">Forgot PIN</a>
    </form>
    <p class="mt-3 mb-0">New here? <a href="{{ route('register') }}">Create an account</a></p>
    <p class="mb-0"><a href="{{ route('products.index') }}">Continue browsing without an account</a></p>
    <p class="small muted mt-3 mb-0">Try admin@marketlink.com / 0000 · farmer@marketlink.com / 1111 · customer@marketlink.com / 2222</p>
</div>
<script>
document.getElementById('loginForm').addEventListener('submit', function () {
    const button = document.getElementById('loginBtn');
    button.disabled = true;
    button.textContent = 'Signing in…';
});
</script>
@endsection
