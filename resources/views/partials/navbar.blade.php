<nav class="navbar navbar-expand-lg navbar-ml sticky-top" x-data="notifyBell()" x-init="start()">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
            <span class="brand-mark">M</span>
            <span class="fw-bold">{{ $siteName ?? 'MarketLink' }}</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="{{ route('markets.index') }}">Markets</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('farmers.index') }}">Farmers</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('products.index') }}">Products</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('about') }}">About</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('contact') }}">Contact</a></li>
            </ul>
            <form class="d-flex position-relative me-2 my-2 my-lg-0" action="{{ route('products.index') }}" method="GET" x-data="searchBox()" @click.outside="open=false">
                <input class="form-control" name="q" placeholder="Search produce, stalls, markets" autocomplete="off" x-model="q" @input.debounce.250ms="lookup()">
                <div class="search-pop mt-1" x-show="open" x-cloak>
                    <template x-for="item in items" :key="item.url">
                        <a class="d-block px-3 py-2 text-decoration-none" :href="item.url" x-text="item.label"></a>
                    </template>
                </div>
            </form>
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-outline-ml btn-sm" type="button" onclick="mlTheme()" aria-label="Toggle dark mode"><i class="bi bi-moon-stars"></i></button>
                @auth
                    <div class="dropdown">
                        <button class="btn btn-outline-ml btn-sm position-relative" data-bs-toggle="dropdown" aria-label="Notifications">
                            <i class="bi bi-bell"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" x-show="unread > 0" x-text="unread"></span>
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
                        <a class="btn btn-outline-ml btn-sm" href="{{ route('cart.index') }}"><i class="bi bi-bag"></i></a>
                    @endif
                    <div class="dropdown">
                        <button class="btn btn-ml btn-sm dropdown-toggle" data-bs-toggle="dropdown">{{ auth()->user()->name }}</button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ auth()->user()->dashboardRoute() }}">Dashboard</a></li>
                            @if(auth()->user()->isCustomer())
                                <li><a class="dropdown-item" href="{{ route('customer.profile') }}">Profile</a></li>
                            @elseif(auth()->user()->isFarmer())
                                <li><a class="dropdown-item" href="{{ route('farmer.profile') }}">Stall profile</a></li>
                            @endif
                            <li>
                                <form method="POST" action="{{ route('logout') }}">@csrf
                                    <button class="dropdown-item">Log out</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
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
            q: '', open: false, items: [],
            async lookup() {
                if (this.q.length < 2) { this.open = false; return; }
                const res = await fetch(`{{ route('search.suggest') }}?q=${encodeURIComponent(this.q)}`);
                const data = await res.json();
                this.items = [...(data.products||[]), ...(data.farmers||[]), ...(data.markets||[])];
                this.open = this.items.length > 0;
            }
        };
    }
    function notifyBell() {
        return {
            unread: {{ $unreadNotifications ?? 0 }},
            items: [],
            async pull() {
                @auth
                const res = await fetch('{{ auth()->user()->isFarmer() ? route('farmer.notifications.poll') : (auth()->user()->isAdmin() ? route('admin.notifications.poll') : route('customer.notifications.poll')) }}');
                const data = await res.json();
                this.unread = data.unread;
                this.items = data.items;
                @endauth
            },
            start() { this.pull(); setInterval(() => this.pull(), 30000); }
        };
    }
</script>
<style>[x-cloak]{display:none !important}</style>
