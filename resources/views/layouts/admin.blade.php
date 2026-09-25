<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') · {{ $siteName ?? 'MarketLink' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet">
    <link href="{{ asset('css/marketlink.css') }}" rel="stylesheet">
    <script>document.documentElement.setAttribute('data-theme', localStorage.getItem('ml-theme') || 'light');</script>
</head>
<body class="admin-body desk-admin">
<div class="admin-shell">
    <aside class="admin-side" id="adminSide">
        <a class="admin-brand" href="{{ route('admin.dashboard') }}"><img class="brand-logo" src="{{ asset('images/logo.svg') }}" alt="" width="36" height="36"> {{ $siteName ?? 'MarketLink' }}</a>
        <nav class="admin-nav">
            @php
                $groups = [
                    'Overview' => [
                        ['admin.dashboard', 'bi-speedometer2', 'Dashboard'],
                        ['admin.reports.index', 'bi-bar-chart', 'Reports'],
                    ],
                    'People' => [
                        ['admin.users.index', 'bi-people', 'Customers'],
                        ['admin.farmers.index', 'bi-shop', 'Farmers'],
                    ],
                    'Catalog' => [
                        ['admin.markets.index', 'bi-geo-alt', 'Markets'],
                        ['admin.categories.index', 'bi-tags', 'Categories'],
                        ['admin.products.index', 'bi-basket', 'Products'],
                    ],
                    'Sales' => [
                        ['admin.orders.index', 'bi-receipt', 'Orders'],
                        ['admin.reviews.index', 'bi-star', 'Reviews'],
                    ],
                    'System' => [
                        ['admin.announcements.index', 'bi-megaphone', 'Announcements'],
                        ['admin.chatbot-faqs.index', 'bi-chat-dots', 'Chatbot FAQs'],
                        ['admin.settings', 'bi-gear', 'Settings'],
                    ],
                ];
            @endphp
            @foreach($groups as $group => $links)
                <div class="admin-group">{{ $group }}</div>
                @foreach($links as [$name, $icon, $label])
                    <a class="{{ request()->routeIs($name) || request()->routeIs(str_replace('.index', '.*', $name)) ? 'active' : '' }}" href="{{ route($name) }}">
                        <i class="bi {{ $icon }}"></i> {{ $label }}
                    </a>
                @endforeach
            @endforeach
        </nav>
        <form method="POST" action="{{ route('logout') }}" class="p-3">@csrf
            <button class="btn btn-outline-light w-100"><i class="bi bi-box-arrow-right"></i> Log out</button>
        </form>
    </aside>
    <div class="admin-main">
        <div class="admin-top">
            <button class="btn btn-outline-ml btn-sm d-lg-none" type="button" onclick="document.getElementById('adminSide').classList.toggle('open')" aria-label="Open admin menu"><i class="bi bi-list"></i></button>
            <div>
                <div class="admin-crumb">Admin</div>
                <strong>@yield('title', 'Dashboard')</strong>
            </div>
            <div class="ms-auto d-flex align-items-center gap-2">
                <button class="btn btn-outline-ml btn-sm" type="button" onclick="mlTheme()" aria-label="Toggle dark mode"><i class="bi bi-moon-stars"></i></button>
                <a class="btn btn-outline-ml btn-sm" href="{{ route('home') }}">View site</a>
                <span class="admin-user">{{ auth()->user()->name }}</span>
            </div>
        </div>
        <div class="p-3 p-md-4">
            @include('partials.flashes')
            @yield('content')
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
window.mlTheme = function () {
    const next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', next);
    localStorage.setItem('ml-theme', next);
};
</script>
@stack('scripts')
</body>
</html>
