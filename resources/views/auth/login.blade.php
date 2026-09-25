@extends('layouts.guest')
@section('title', 'Log in')
@section('content')
<div class="login-card card-ml p-4 p-md-5">
    <p class="login-kicker">MarketLink</p>
    <h1 class="h3 mb-1">Welcome back</h1>
    <p class="muted mb-4">Sign in with email and your 4-digit PIN. We open the right dashboard for you.</p>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    <form method="POST" action="{{ route('login') }}" id="loginForm">
        @csrf
        <label class="form-label" for="email">Email</label>
        <div class="login-field mb-3">
            <i class="bi bi-envelope"></i>
            <input class="form-control" id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="you@marketlink.com">
        </div>
        <label class="form-label" for="pin">4-digit PIN</label>
        <div class="login-field mb-3">
            <i class="bi bi-lock"></i>
            <input class="form-control" id="pin" name="pin" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" minlength="4" required autocomplete="current-password" placeholder="0000">
        </div>
        <div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="remember" id="remember" value="1"><label class="form-check-label" for="remember">Remember me</label></div>
        <button class="btn btn-ml w-100" id="loginBtn" type="submit">Log in</button>
    </form>
    <a class="btn btn-outline-ml w-100 mt-3" href="{{ route('register') }}">Create new account</a>
    <a class="btn btn-link w-100 mt-2" href="{{ route('products.index') }}">Continue as guest</a>
    <p class="small muted mt-3 mb-0">Demo: admin@marketlink.com / 0000 · farmer@marketlink.com / 1111 · customer@marketlink.com / 2222</p>
</div>
<script>
(function () {
    const pin = document.getElementById('pin');
    pin.addEventListener('input', function () { this.value = this.value.replace(/\D/g, '').slice(0, 4); });
    document.getElementById('loginForm').addEventListener('submit', function () {
        const button = document.getElementById('loginBtn');
        button.disabled = true;
        button.textContent = 'Signing in…';
    });
})();
</script>
@endsection
