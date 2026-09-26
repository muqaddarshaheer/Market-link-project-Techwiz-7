@extends('layouts.guest')
@section('title', 'Log in')
@section('auth_panel')
    <div class="auth-panel-copy">
        <p class="auth-panel-kicker">Local · Pickup · Pay at stall</p>
        <h1 class="auth-brand-title">MarketLink</h1>
        <p class="auth-panel-lead">Reserve fresh produce from verified growers, then collect and pay at the market.</p>
        <ul class="auth-steps">
            <li><span>1</span><div><strong>Sign in</strong><small>Email + password</small></div></li>
            <li><span>2</span><div><strong>Reserve</strong><small>Pick a market slot</small></div></li>
            <li><span>3</span><div><strong>Collect</strong><small>Pay the farmer in person</small></div></li>
        </ul>
    </div>
@endsection
@section('content')
<div class="login-card card-ml">
    <div class="login-card-head">
        <img class="brand-logo" src="{{ asset('images/logo.svg') }}" alt="" width="40" height="40">
        <div>
            <p class="login-kicker">Welcome back</p>
            <h1 class="login-title">Log in to MarketLink</h1>
        </div>
    </div>
    <p class="login-sub">Use your email and password. We’ll open the right panel for you.</p>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" id="loginForm" class="login-form">
        @csrf
        <label class="form-label" for="email">Email</label>
        <div class="login-field mb-3">
            <i class="bi bi-envelope" aria-hidden="true"></i>
            <input class="form-control" id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="you@email.com">
        </div>

        <label class="form-label" for="password">Password</label>
        <div class="login-field mb-3">
            <i class="bi bi-lock" aria-hidden="true"></i>
            <input class="form-control" id="password" type="password" name="password" required autocomplete="current-password" placeholder="Your password" minlength="6">
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div class="form-check mb-0"><input class="form-check-input" type="checkbox" name="remember" id="remember" value="1"><label class="form-check-label" for="remember">Keep me signed in</label></div>
            <a class="small fw-bold" href="{{ route('password.request') }}">Forgot password?</a>
        </div>
        <button class="btn btn-ml w-100 login-submit" id="loginBtn" type="submit">Log in</button>
    </form>

    <div class="login-alt">
        <a class="btn btn-outline-ml w-100" href="{{ route('register') }}">Create new account</a>
        <a class="login-guest" href="{{ route('products.index') }}">Continue browsing as guest</a>
    </div>

    <div class="login-demo">
        <span>Demo accounts</span>
        <code>admin@marketlink.com → Admin@123</code>
        <code>farmer@marketlink.com → Farmer@123</code>
        <code>customer@marketlink.com → Customer@123</code>
    </div>
</div>
<script>
(function () {
    var form = document.getElementById('loginForm');
    var button = document.getElementById('loginBtn');
    form.addEventListener('submit', function () {
        button.disabled = true;
        button.textContent = 'Signing in…';
    });
})();
</script>
@endsection
