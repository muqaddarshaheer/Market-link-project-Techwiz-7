@extends('layouts.guest')
@section('title', 'Log in')
@section('auth_panel')
    <div class="auth-panel-copy">
        <p class="auth-panel-kicker">Local · Pickup · Pay at stall</p>
        <h1 class="auth-brand-title">MarketLink</h1>
        <p class="auth-panel-lead">Reserve fresh produce from verified growers, then collect and pay at the market.</p>
        <ul class="auth-steps">
            <li><span>1</span><div><strong>Sign in</strong><small>Email + 4-digit PIN</small></div></li>
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
    <p class="login-sub">Use your email and PIN. We’ll open the right panel for you.</p>

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

        <label class="form-label" for="pin">4-digit PIN</label>
        <div class="login-field login-field-pin mb-2">
            <i class="bi bi-shield-lock" aria-hidden="true"></i>
            <input class="form-control pin-input" id="pin" name="pin" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" minlength="4" required autocomplete="current-password" placeholder="••••" aria-describedby="pinHelp">
        </div>
        <div class="pin-dots" id="pinDots" aria-hidden="true">
            <span></span><span></span><span></span><span></span>
        </div>
        <p class="small muted mb-3" id="pinHelp">Four numbers only — same PIN you set at signup.</p>

        <div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="remember" id="remember" value="1"><label class="form-check-label" for="remember">Keep me signed in</label></div>
        <button class="btn btn-ml w-100 login-submit" id="loginBtn" type="submit">Log in</button>
    </form>

    <div class="login-alt">
        <a class="btn btn-outline-ml w-100" href="{{ route('register') }}">Create new account</a>
        <a class="login-guest" href="{{ route('products.index') }}">Continue browsing as guest</a>
    </div>

    <div class="login-demo">
        <span>Demo PIN</span>
        <code>admin@marketlink.com → 0000</code>
        <code>farmer@marketlink.com → 1111</code>
        <code>customer@marketlink.com → 2222</code>
    </div>
</div>
<script>
(function () {
    const form = document.getElementById('loginForm');
    const email = document.getElementById('email');
    const pin = document.getElementById('pin');
    const button = document.getElementById('loginBtn');
    const dots = Array.from(document.querySelectorAll('#pinDots span'));
    let submitting = false;

    function paintDots() {
        const len = pin.value.length;
        dots.forEach(function (dot, i) { dot.classList.toggle('is-on', i < len); });
    }

    function emailOk() {
        return email.checkValidity() && email.value.trim().length > 3;
    }

    function tryAutoLogin() {
        if (submitting) return;
        if (! emailOk() || pin.value.length !== 4) return;
        submitting = true;
        button.disabled = true;
        button.textContent = 'Signing in…';
        form.requestSubmit ? form.requestSubmit() : form.submit();
    }

    pin.addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 4);
        paintDots();
        tryAutoLogin();
    });

    email.addEventListener('change', tryAutoLogin);
    email.addEventListener('blur', tryAutoLogin);

    form.addEventListener('submit', function () {
        submitting = true;
        button.disabled = true;
        button.textContent = 'Signing in…';
    });

    paintDots();
})();
</script>
@endsection
