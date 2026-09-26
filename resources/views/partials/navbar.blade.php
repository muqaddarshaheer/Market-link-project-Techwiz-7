<nav class="navbar navbar-expand-lg navbar-ml sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
            <img class="brand-logo" src="{{ asset('images/logo.svg') }}" alt="" width="40" height="40">
            <span class="fw-bold">{{ $siteName ?? 'MarketLink' }}</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('markets.*') ? 'active' : '' }}" href="{{ route('markets.index') }}">Markets</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('farmers.*') ? 'active' : '' }}" href="{{ route('farmers.index') }}">Farmers</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">Products</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">About</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">Contact</a></li>
            </ul>

            <form class="nav-search ms-lg-2 me-lg-2 my-2 my-lg-0" action="{{ route('search') }}" method="GET" x-data="searchBox()" @click.outside="open = false" role="search">
                <label class="visually-hidden" for="navSearchInput">Search</label>
                <i class="bi bi-search" aria-hidden="true"></i>
                <input
                    id="navSearchInput"
                    class="form-control"
                    type="search"
                    name="q"
                    placeholder="Search produce, stalls, markets"
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
                <button class="btn btn-outline-ml btn-sm nav-icon-btn" type="button" onclick="mlTheme()" aria-label="Toggle dark mode"><i class="bi bi-moon-stars"></i></button>
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
                        <button class="btn btn-ml btn-sm dropdown-toggle" data-bs-toggle="dropdown">{{ auth()->user()->name }}</button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @if(auth()->user()->isCustomer())
                                <li><a class="dropdown-item" href="{{ route('customer.dashboard') }}">Dashboard</a></li>
                                <li><a class="dropdown-item" href="{{ route('customer.orders.index') }}">Orders</a></li>
                                <li><a class="dropdown-item" href="{{ route('customer.favorites') }}">Favorites</a></li>
                                <li><a class="dropdown-item" href="{{ route('customer.profile') }}">Profile</a></li>
                            @elseif(auth()->user()->isFarmer())
                                <li><a class="dropdown-item" href="{{ route('farmer.dashboard') }}">Farmer dashboard</a></li>
                                <li><a class="dropdown-item" href="{{ route('farmer.products.index') }}">Products</a></li>
                                <li><a class="dropdown-item" href="{{ route('farmer.orders.index') }}">Orders</a></li>
                                <li><a class="dropdown-item" href="{{ route('farmer.profile') }}">Stall profile</a></li>
                                <li><a class="dropdown-item" href="{{ route('farmer.insights') }}">Insights</a></li>
                            @else
                                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Admin dashboard</a></li>
                            @endif
                            <li>
                                <form method="POST" action="{{ route('logout') }}">@csrf
                                    <button class="dropdown-item">Log out</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a class="btn btn-outline-ml btn-sm position-relative" href="{{ route('guest.cart') }}" aria-label="Cart" data-cart-link>
                        <i class="bi bi-bag"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger {{ ($cartCount ?? 0) < 1 ? 'd-none' : '' }}" data-cart-badge>{{ $cartCount ?? 0 }}</span>
                    </a>
                    <a class="btn btn-outline-ml btn-sm" href="{{ route('login') }}">Log in</a>
                    <a class="btn btn-ml btn-sm" href="{{ route('register') }}">Join</a>
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
