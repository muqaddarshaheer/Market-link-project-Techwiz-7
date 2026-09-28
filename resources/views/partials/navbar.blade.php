<nav class="navbar navbar-expand-xl navbar-ml fixed-top">
    <div class="container-fluid px-3 px-xl-4 nav-inner">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
            <img class="brand-logo" src="{{ asset('images/logo.svg') }}" alt="" width="34" height="34">
            <span class="fw-bold brand-text">{{ $siteName ?? 'MarketLink' }}</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain" aria-controls="navMain" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav me-xl-auto align-items-xl-center nav-links">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}" title="Home"><i class="bi bi-house-door"></i><span>Home</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('markets.*') ? 'active' : '' }}" href="{{ route('markets.index') }}" title="Markets"><i class="bi bi-geo-alt"></i><span>Markets</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('farmers.*') ? 'active' : '' }}" href="{{ route('farmers.index') }}" title="Farmers"><i class="bi bi-people"></i><span>Farmers</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}" title="Products"><i class="bi bi-basket2"></i><span>Products</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('produce-guide') ? 'active' : '' }}" href="{{ route('produce-guide') }}" title="HarvestWise"><i class="bi bi-flower1"></i><span>HarvestWise</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}" title="About"><i class="bi bi-info-circle"></i><span>About</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}" title="Contact"><i class="bi bi-envelope"></i><span>Contact</span></a>
                </li>
            </ul>

            <form class="nav-search ms-xl-2 me-xl-2 my-2 my-xl-0" action="{{ route('search') }}" method="GET" x-data="searchBox()" @click.outside="open = false" role="search">
                <label class="visually-hidden" for="navSearchInput">Search</label>
                <i class="bi bi-search" aria-hidden="true"></i>
                <input
                    id="navSearchInput"
                    class="form-control"
                    type="search"
                    name="q"
                    placeholder="Search produce or stalls"
                    autocomplete="off"
                    enterkeyhint="search"
                    x-model="q"
                    @focus="if (q.length > 1) open = true"
                    @input.debounce.200ms="lookup()"
                    @keydown.escape.prevent="open = false"
                    @keydown.enter="open = false"
                    aria-label="Search produce, stalls, and markets"
                    aria-autocomplete="list"
                    aria-expanded="false"
                    :aria-expanded="open ? 'true' : 'false'"
                >
                <div class="search-pop" x-show="open" x-cloak x-transition.opacity.duration.120ms>
                    <template x-if="loading">
                        <div class="search-pop-empty">Searching…</div>
                    </template>
                    <template x-if="!loading && items.length === 0 && q.length > 1">
                        <div class="search-pop-empty">No quick matches — press Enter for full results</div>
                    </template>
                    <template x-for="item in items" :key="item.url">
                        <a class="search-pop-item" :href="item.url">
                            <span class="search-pop-type" x-text="item.type"></span>
                            <span x-text="item.label"></span>
                        </a>
                    </template>
                    <a class="search-pop-all" x-show="q.length > 1" :href="'{{ route('search') }}?q=' + encodeURIComponent(q)">
                        See all results for “<span x-text="q"></span>”
                    </a>
                </div>
            </form>

            <div class="nav-actions">
                <button class="btn btn-outline-ml btn-sm nav-icon-btn" type="button" data-theme-toggle onclick="mlTheme()" aria-label="Toggle dark mode" aria-pressed="false"><i class="bi bi-moon-stars"></i></button>
                @auth
                    <div class="dropdown" x-data="notifyBell()" x-init="start()">
                        <button class="btn btn-outline-ml btn-sm position-relative" data-bs-toggle="dropdown" aria-label="Notifications">
                            <i class="bi bi-bell"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" x-show="unread > 0" x-text="unread" x-cloak></span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end p-2" style="width: 320px;">
                            <template x-for="item in items" :key="item.id">
                                <div class="px-2 py-2 border-bottom">
                                    <strong x-text="item.title"></strong>
                                    <div class="small muted" x-text="item.message"></div>
                                </div>
                            </template>
                            <a class="dropdown-item text-center" href="{{ auth()->user()->isFarmer() ? route('farmer.notifications') : (auth()->user()->isCustomer() ? route('customer.notifications') : route('admin.dashboard')) }}">View all</a>
                        </div>
                    </div>
                    @if(auth()->user()->isCustomer())
                        <a class="btn btn-outline-ml btn-sm position-relative" href="{{ route('cart.index') }}" aria-label="Cart" data-cart-link>
                            <i class="bi bi-bag"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger {{ ($cartCount ?? 0) < 1 ? 'd-none' : '' }}" data-cart-badge>{{ $cartCount ?? 0 }}</span>
                        </a>
                    @endif
                    <div class="dropdown">
                        <button class="btn btn-ml btn-sm dropdown-toggle d-inline-flex align-items-center gap-2" data-bs-toggle="dropdown">
                            <img class="nav-avatar" src="{{ \App\Support\ImageStore::url(auth()->user()->avatar, 'images/placeholder.svg') }}" alt="" width="22" height="22">
                            <span>{{ auth()->user()->name }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @if(auth()->user()->isCustomer())
                                <li><a class="dropdown-item" href="{{ route('customer.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                                <li><a class="dropdown-item" href="{{ route('customer.orders.index') }}"><i class="bi bi-receipt"></i> Orders</a></li>
                                <li><a class="dropdown-item" href="{{ route('customer.favorites') }}"><i class="bi bi-heart"></i> Favorites</a></li>
                                <li><a class="dropdown-item" href="{{ route('customer.profile') }}"><i class="bi bi-person"></i> Profile</a></li>
                            @elseif(auth()->user()->isFarmer())
                                <li><a class="dropdown-item" href="{{ route('farmer.dashboard') }}"><i class="bi bi-speedometer2"></i> Farmer dashboard</a></li>
                                <li><a class="dropdown-item" href="{{ route('farmer.products.index') }}"><i class="bi bi-basket2"></i> Products</a></li>
                                <li><a class="dropdown-item" href="{{ route('farmer.orders.index') }}"><i class="bi bi-receipt"></i> Orders</a></li>
                                <li><a class="dropdown-item" href="{{ route('farmer.profile') }}"><i class="bi bi-shop"></i> Stall profile</a></li>
                                <li><a class="dropdown-item" href="{{ route('farmer.account') }}"><i class="bi bi-person-gear"></i> Account</a></li>
                                <li><a class="dropdown-item" href="{{ route('farmer.insights') }}"><i class="bi bi-graph-up"></i> Insights</a></li>
                            @else
                                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="bi bi-shield-lock"></i> Admin dashboard</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.settings') }}"><i class="bi bi-gear"></i> Account &amp; settings</a></li>
                            @endif
                            <li>
                                <form method="POST" action="{{ route('logout') }}">@csrf
                                    <button class="dropdown-item"><i class="bi bi-box-arrow-right"></i> Log out</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a class="btn btn-outline-ml btn-sm position-relative" href="{{ route('guest.cart') }}" aria-label="Cart" data-cart-link>
                        <i class="bi bi-bag"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger {{ ($cartCount ?? 0) < 1 ? 'd-none' : '' }}" data-cart-badge>{{ $cartCount ?? 0 }}</span>
                    </a>
                    <a class="btn btn-outline-ml btn-sm" href="{{ route('login') }}"><i class="bi bi-box-arrow-in-right"></i> Log in</a>
                    <a class="btn btn-ml btn-sm" href="{{ route('register') }}"><i class="bi bi-person-plus"></i> Join</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
<script>
    function searchBox() {
        return {
            q: '',
            open: false,
            loading: false,
            items: [],
            async lookup() {
                const term = (this.q || '').trim();
                if (term.length < 2) {
                    this.items = [];
                    this.open = false;
                    this.loading = false;
                    return;
                }
                this.loading = true;
                this.open = true;
                try {
                    const res = await fetch(`{{ route('search.suggest') }}?q=${encodeURIComponent(term)}`, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await res.json();
                    const products = (data.products || []).map(function (row) {
                        return { label: row.label, url: row.url, type: 'Produce' };
                    });
                    const farmers = (data.farmers || []).map(function (row) {
                        return { label: row.label, url: row.url, type: 'Stall' };
                    });
                    const markets = (data.markets || []).map(function (row) {
                        return { label: row.label, url: row.url, type: 'Market' };
                    });
                    this.items = products.concat(farmers, markets);
                } catch (e) {
                    this.items = [];
                } finally {
                    this.loading = false;
                    this.open = true;
                }
            }
        };
    }
    function notifyBell() {
        return {
            unread: {{ $unreadNotifications ?? 0 }},
            items: [],
            async pull() {
                @auth
                try {
                    const res = await fetch('{{ auth()->user()->isFarmer() ? route('farmer.notifications.poll') : (auth()->user()->isAdmin() ? route('admin.notifications.poll') : route('customer.notifications.poll')) }}');
                    const data = await res.json();
                    this.unread = data.unread;
                    this.items = data.items || [];
                } catch (e) {}
                @endauth
            },
            start() {
                this.pull();
                setInterval(() => this.pull(), 60000);
            }
        };
    }
</script>
