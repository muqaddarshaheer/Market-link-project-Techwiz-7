<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="MarketLink connects farmers-market growers with customers for local pickup pre-orders.">
    <title>@yield('title', $siteName ?? 'MarketLink')</title>
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,560;9..144,700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet">
    <link href="{{ asset('css/marketlink.css') }}" rel="stylesheet">
    <script>
        (function () {
            const saved = localStorage.getItem('ml-theme') || 'light';
            document.documentElement.setAttribute('data-theme', saved);
        })();
    </script>
    @stack('head')
</head>
<body>
@include('partials.navbar')
@if(($liveAnnouncements ?? collect())->isNotEmpty())
    <div class="container mt-3">
        @foreach($liveAnnouncements as $announcement)
            <div class="alert {{ $announcement->priority === 'high' ? 'alert-warning' : 'alert-success' }} py-2 mb-2">
                <strong>{{ $announcement->title }}.</strong> {{ $announcement->message }}
            </div>
        @endforeach
    </div>
@endif
<main class="py-4">
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
@include('partials.chatbot')
<div class="toast-stack" id="toastStack"></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    window.mlTheme = function () {
        const next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', next);
        localStorage.setItem('ml-theme', next);
    };
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('{{ asset('sw.js') }}').catch(() => {});
    }
</script>
@stack('scripts')
</body>
</html>
