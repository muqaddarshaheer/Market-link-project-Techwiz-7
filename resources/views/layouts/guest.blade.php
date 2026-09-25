<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Sign in') · {{ $siteName ?? 'MarketLink' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,560;9..144,700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/marketlink.css') }}?v={{ @filemtime(public_path('css/marketlink.css')) }}" rel="stylesheet">
    <script>document.documentElement.setAttribute('data-theme', localStorage.getItem('ml-theme') || 'light');</script>
</head>
<body class="auth-body {{ request()->routeIs('register*') ? 'auth-register' : 'auth-login' }}">
<div class="auth-shell">
    <aside class="auth-panel">
        <div class="auth-panel-media" aria-hidden="true"></div>
        <div class="auth-panel-shade" aria-hidden="true"></div>
        <div class="auth-panel-content">
            <a href="{{ route('home') }}" class="auth-panel-home">
                <img class="brand-logo" src="{{ asset('images/logo.svg') }}" alt="" width="44" height="44">
                <span>Back to home</span>
            </a>
            @yield('auth_panel')
        </div>
    </aside>
    <main class="auth-form">
        @include('partials.flashes')
        @yield('content')
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
