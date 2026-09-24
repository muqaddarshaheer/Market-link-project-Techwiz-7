<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Sign in') · {{ $siteName ?? 'MarketLink' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,560;9..144,700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/marketlink.css') }}" rel="stylesheet">
    <script>document.documentElement.setAttribute('data-theme', localStorage.getItem('ml-theme') || 'light');</script>
</head>
<body class="auth-body">
<div class="auth-shell">
    <aside class="auth-panel">
        <a href="{{ route('home') }}" class="d-inline-flex align-items-center gap-2 text-white text-decoration-none mb-4">
            <img class="brand-logo" src="{{ asset('images/logo.svg') }}" alt="" width="44" height="44">
            <strong class="fs-4">{{ $siteName ?? 'MarketLink' }}</strong>
        </a>
        <h1 class="display-6">Fresh food, short miles.</h1>
        <p>Reserve a basket from a local stall and pay the farmer when you pick it up.</p>
        <ol>
            <li>Browse markets and growers.</li>
            <li>Pre-order before the cutoff.</li>
            <li>Collect and pay at the stall.</li>
        </ol>
    </aside>
    <main class="auth-form">
        @include('partials.flashes')
        @yield('content')
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
