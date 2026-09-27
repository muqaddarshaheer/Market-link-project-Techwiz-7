<!DOCTYPE html>
<html lang="en" data-theme="light" data-farmer-lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · {{ $siteName ?? 'MarketLink' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@600;800&family=Noto+Nastaliq+Urdu:wght@500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" media="print" onload="this.media='all'">
    <link href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet" media="print" onload="this.media='all'">
    <link href="{{ asset('css/marketlink.css') }}?v={{ @filemtime(public_path('css/marketlink.css')) }}" rel="stylesheet">
    <link href="{{ asset('css/farmer-panel.css') }}?v={{ @filemtime(public_path('css/farmer-panel.css')) }}" rel="stylesheet">
    <script>
        document.documentElement.setAttribute('data-theme', localStorage.getItem('ml-theme') || 'light');
        (function () {
            var lang = localStorage.getItem('farmer-lang') || 'en';
            document.documentElement.setAttribute('data-farmer-lang', lang);
            document.documentElement.setAttribute('lang', lang === 'ur' ? 'ur' : 'en');
            document.documentElement.setAttribute('dir', lang === 'ur' ? 'rtl' : 'ltr');
        })();
    </script>
    <style>[x-cloak]{display:none!important} .chat-panel[hidden]{display:none!important}</style>
</head>
<body class="admin-body desk-farmer" data-csrf-url="{{ route('csrf.token') }}">
<div class="panel-scrim" id="panelScrim" onclick="document.getElementById('adminSide').classList.remove('open');this.classList.remove('is-on');this.hidden=true" hidden></div>
<div class="admin-shell">
    <aside class="admin-side" id="adminSide">
        <a class="admin-brand" href="{{ route('home') }}" title="Open website"><img class="brand-logo" src="{{ asset('images/logo.svg') }}" alt="" width="36" height="36"> {{ $siteName ?? 'MarketLink' }}</a>
        <nav class="admin-nav">
            @php
                $groups = [
                    ['key' => 'nav.overview', 'links' => [
                        ['farmer.dashboard', 'bi-speedometer2', 'nav.dashboard'],
                        ['farmer.lands.index', 'bi-geo-alt', 'nav.lands'],
                        ['farmer.crop-calculator', 'bi-calculator', 'nav.calculator'],
                        ['farmer.smart-crop-guide', 'bi-flower1', 'nav.smartCrop'],
                        ['produce-guide', 'bi-lightbulb', 'nav.produceGuide'],
                    ]],
                    ['key' => 'nav.stall', 'links' => [
                        ['farmer.products.index', 'bi-basket', 'nav.products'],
                        ['farmer.slots.index', 'bi-clock', 'nav.slots'],
                        ['farmer.profile', 'bi-shop', 'nav.profile'],
                        ['farmer.account', 'bi-person', 'nav.account'],
                    ]],
                    ['key' => 'nav.sales', 'links' => [
                        ['farmer.orders.index', 'bi-receipt', 'nav.orders'],
                        ['farmer.sales', 'bi-graph-up', 'nav.salesPage'],
                        ['farmer.expenses.index', 'bi-wallet2', 'nav.expenses'],
                        ['farmer.reviews', 'bi-star', 'nav.reviews'],
                    ]],
                    ['key' => 'nav.accountGroup', 'links' => [
                        ['farmer.notifications', 'bi-bell', 'nav.notifications'],
                    ]],
                ];
            @endphp
            @foreach($groups as $group)
                <div class="admin-group" data-i18n="{{ $group['key'] }}">{{ $group['key'] === 'nav.overview' ? 'Overview' : ($group['key'] === 'nav.stall' ? 'Stall' : ($group['key'] === 'nav.sales' ? 'Sales' : 'Account')) }}</div>
                @foreach($group['links'] as [$name, $icon, $i18n])
                    @continue(! \Illuminate\Support\Facades\Route::has($name))
                    <a class="{{ request()->routeIs($name) || request()->routeIs($name.'.*') || request()->routeIs(str_replace('.index', '.*', $name)) || ($name === 'farmer.sales' && request()->routeIs('farmer.insights')) ? 'active' : '' }}" href="{{ route($name) }}">
                        <i class="bi {{ $icon }}"></i> <span data-i18n="{{ $i18n }}">{{ match($i18n) {
                            'nav.dashboard' => 'Dashboard',
                            'nav.insights' => 'Sales',
                            'nav.salesPage' => 'Sales',
                            'nav.expenses' => 'Expenses',
                            'nav.lands' => 'My Land',
                            'nav.calculator' => 'Crop Calculator',
                            'nav.smartCrop' => 'Smart Crop Guide',
                            'nav.cropHealth' => 'Crop Health',
                            'nav.produceGuide' => 'HarvestWise',
                            'nav.products' => 'Products',
                            'nav.slots' => 'Pickup slots',
                            'nav.profile' => 'Stall profile',
                            'nav.account' => 'Account',
                            'nav.orders' => 'Orders',
                            'nav.reviews' => 'Reviews',
                            'nav.notifications' => 'Notifications',
                            default => $i18n,
                        } }}</span>
                        @if($name === 'farmer.notifications' && ($unreadNotifications ?? 0) > 0)
                            <span class="ms-auto">{{ $unreadNotifications }}</span>
                        @endif
                    </a>
                @endforeach
            @endforeach
        </nav>
        <form method="POST" action="{{ route('logout') }}" class="p-3">@csrf
            <button class="btn btn-outline-light w-100"><i class="bi bi-box-arrow-right"></i> <span data-i18n="nav.logout">Log out</span></button>
        </form>
    </aside>
    <div class="admin-main">
        <div class="admin-top">
            <button class="btn btn-outline-ml btn-sm d-lg-none panel-menu-btn" type="button" onclick="document.getElementById('adminSide').classList.toggle('open');var s=document.getElementById('panelScrim');if(s){s.classList.toggle('is-on');s.hidden=!s.classList.contains('is-on')}" aria-label="Open menu"><i class="bi bi-list"></i></button>
            <div>
                <div class="admin-crumb" data-i18n="top.panel">Farmer panel</div>
                <strong>@yield('title', 'Dashboard')</strong>
            </div>
            <div class="ms-auto d-flex align-items-center gap-2 flex-wrap justify-content-end">
                <button type="button" class="btn btn-ml btn-sm farmer-lang-btn" id="farmerLangBtn" aria-pressed="false" title="اردو میں دیکھیں">
                    <i class="bi bi-translate"></i> <span>اردو</span>
                </button>
                <span class="admin-role-pill d-none d-md-inline" data-i18n="top.role">Farmer</span>
                <button class="btn btn-outline-ml btn-sm" type="button" onclick="mlTheme()" aria-label="Toggle dark mode"><i class="bi bi-moon-stars"></i></button>
                <a class="btn btn-outline-ml btn-sm" href="{{ route('home') }}"><span data-i18n="top.site">View site</span></a>
                <span class="admin-user">{{ auth()->user()->name }}</span>
            </div>
        </div>
        <div class="p-3 p-md-4 farmer-content">
            @include('partials.flashes')
            @yield('content')
        </div>
    </div>
</div>
@include('partials.chatbot', [
    'chatFarmer' => true,
    'chatSpeakUrl' => route('chatbot.speak'),
])
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
<script src="{{ asset('js/csrf-keepalive.js') }}?v={{ @filemtime(public_path('js/csrf-keepalive.js')) }}"></script>
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="{{ asset('js/farmer-i18n.js') }}?v={{ @filemtime(public_path('js/farmer-i18n.js')) }}"></script>
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
