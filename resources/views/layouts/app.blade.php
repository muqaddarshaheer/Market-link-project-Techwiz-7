<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1f6b45">
    <meta name="description" content="MarketLink connects farmers-market growers with customers for local pickup pre-orders.">
    <title>@yield('title', $siteName ?? 'MarketLink')</title>
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,700&family=Manrope:wght@500;700&display=swap">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,700&family=Manrope:wght@500;700&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,700&family=Manrope:wght@500;700&display=swap" rel="stylesheet"></noscript>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"></noscript>
    <link href="{{ asset('css/marketlink.css') }}?v={{ @filemtime(public_path('css/marketlink.css')) }}" rel="stylesheet">
    <script>
        (function () {
            document.documentElement.setAttribute('data-theme', localStorage.getItem('ml-theme') || 'light');
        })();
    </script>
    <style>[x-cloak]{display:none!important}</style>
    @stack('head')
</head>
<body class="app-shell">
@include('partials.navbar')
@if(($liveAnnouncements ?? collect())->isNotEmpty())
    <div class="container mt-3">
        @foreach($liveAnnouncements as $announcement)
            <div class="alert alert-dismissible fade show {{ $announcement->priority === 'high' ? 'alert-warning' : 'alert-success' }} py-2 mb-2 ml-alert" role="alert">
                <strong>{{ $announcement->title }}.</strong> {{ $announcement->message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endforeach
    </div>
@endif
<main class="py-4 app-main">
    <div class="container">
        @include('partials.flashes')
        @auth
            @if(auth()->user()->isCustomer())
                @include('partials.customer-nav')
            @elseif(auth()->user()->isFarmer())
                @include('partials.farmer-nav')
            @endif
        @endauth
        @yield('content')
    </div>
</main>
@include('partials.footer')
@include('partials.mobile-dock')
@include('partials.chatbot')
<div class="toast-stack" id="toastStack" aria-live="polite"></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
<script>
    window.mlTheme = function () {
        const next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', next);
        localStorage.setItem('ml-theme', next);
    };
</script>
<script src="{{ asset('js/shop.js') }}?v={{ @filemtime(public_path('js/shop.js')) }}" defer></script>
@stack('scripts')
</body>
</html>
