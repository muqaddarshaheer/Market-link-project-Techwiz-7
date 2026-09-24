<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Sign in') · MarketLink</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/marketlink.css') }}" rel="stylesheet">
    <script>
        document.documentElement.setAttribute('data-theme', localStorage.getItem('ml-theme') || 'light');
    </script>
</head>
<body>
<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <a href="{{ route('home') }}" class="d-inline-flex align-items-center gap-2 text-decoration-none mb-3">
                <span class="brand-mark">M</span><strong>MarketLink</strong>
            </a>
            @include('partials.flashes')
            @yield('content')
        </div>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
