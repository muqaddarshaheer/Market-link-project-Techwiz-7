<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') · {{ $siteName ?? 'MarketLink' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@600;800&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" media="print" onload="this.media='all'">
    <link href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet" media="print" onload="this.media='all'">
    <link href="{{ asset('css/marketlink.css') }}?v={{ @filemtime(public_path('css/marketlink.css')) }}" rel="stylesheet">
    <script>document.documentElement.setAttribute('data-theme', localStorage.getItem('ml-theme') || 'light');</script>
</head>
<body class="admin-body desk-farmer">
<div class="admin-shell">
    <aside class="admin-side" id="adminSide">
        <a class="admin-brand" href="{{ route('farmer.dashboard') }}"><img class="brand-logo" src="{{ asset('images/logo.svg') }}" alt="" width="36" height="36"> {{ $siteName ?? 'MarketLink' }}</a>
        <nav class="admin-nav">
            @php
                $groups = [
                    'Overview' => [
                        ['farmer.dashboard', 'bi-speedometer2', 'Dashboard'],
                        ['farmer.insights', 'bi-bar-chart', 'Insights'],
                    ],
                    'Stall' => [
                        ['farmer.products.index', 'bi-basket', 'Products'],
                        ['farmer.slots.index', 'bi-clock', 'Pickup slots'],
                        ['farmer.profile', 'bi-shop', 'Stall profile'],
                    ],
                    'Sales' => [
                        ['farmer.orders.index', 'bi-receipt', 'Orders'],
                        ['farmer.reviews', 'bi-star', 'Reviews'],
                    ],
                    'Account' => [
                        ['farmer.notifications', 'bi-bell', 'Notifications'],
                    ],
                ];
            @endphp
            @foreach($groups as $group => $links)
                <div class="admin-group">{{ $group }}</div>
                @foreach($links as [$name, $icon, $label])
                    <a class="{{ request()->routeIs($name) || request()->routeIs(str_replace('.index', '.*', $name)) ? 'active' : '' }}" href="{{ route($name) }}">
                        <i class="bi {{ $icon }}"></i> {{ $label }}
                        @if($name === 'farmer.notifications' && ($unreadNotifications ?? 0) > 0)
                            <span class="ms-auto">{{ $unreadNotifications }}</span>
                        @endif
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
            <button class="btn btn-outline-ml btn-sm d-lg-none" type="button" onclick="document.getElementById('adminSide').classList.toggle('open')" aria-label="Open menu"><i class="bi bi-list"></i></button>
            <div>
                <div class="admin-crumb">Farmer panel</div>
                <strong>@yield('title', 'Dashboard')</strong>
            </div>
            <div class="ms-auto d-flex align-items-center gap-2">
                <span class="admin-role-pill d-none d-md-inline">Farmer</span>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
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
