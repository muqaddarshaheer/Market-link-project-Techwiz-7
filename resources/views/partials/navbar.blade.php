<nav class="navbar navbar-expand-lg navbar-ml sticky-top">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">MarketLink</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="{{ route('markets.index') }}">Markets</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('farmers.index') }}">Farmers</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('products.index') }}">Products</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('about') }}">About</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('contact') }}">Contact</a></li>
            </ul>

            <form class="d-flex me-3 position-relative" action="{{ route('products.index') }}" method="GET" x-data="searchAutocomplete()" @submit.prevent="if(q.length) $el.submit()">
                <input class="form-control form-control-sm" type="search" name="q" placeholder="Search produce..."
                       x-model="q" @input.debounce.300ms="fetchResults()" autocomplete="off">
                <div class="position-absolute top-100 start-0 w-100 bg-white border rounded shadow-sm mt-1"
                     style="z-index:20" x-show="results.length" x-cloak>
                    <template x-for="item in results" :key="item.id">
                        <a :href="item.url" class="d-block px-3 py-2 text-decoration-none border-bottom">
                            <span x-text="item.name"></span>
                            <small class="text-muted"> — $<span x-text="item.price"></span>/<span x-text="item.unit"></span></small>
                        </a>
                    </template>
                </div>
            </form>

            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" id="themeToggle" title="Toggle dark mode">
                    <i class="bi bi-moon-stars"></i>
                </button>

                @auth
                    <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-outline-secondary position-relative" id="notifBell">
                        <i class="bi bi-bell"></i>
                        @if($unreadNotifications > 0)
                            <span class="badge rounded-pill bg-danger notif-badge" id="notifCount">{{ $unreadNotifications }}</span>
                        @endif
                    </a>

                    @if(auth()->user()->isCustomer())
                        <a href="{{ route('cart.index') }}" class="btn btn-sm btn-outline-primary position-relative">
                            <i class="bi bi-basket"></i>
                            @if($cartCount > 0)
                                <span class="badge rounded-pill bg-primary notif-badge">{{ $cartCount }}</span>
                            @endif
                        </a>
                    @endif

                    <div class="dropdown">
                        <button class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                            {{ auth()->user()->name }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @if(auth()->user()->isAdmin())
                                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Admin dashboard</a></li>
                            @elseif(auth()->user()->isFarmer())
                                <li><a class="dropdown-item" href="{{ route('farmer.dashboard') }}">Farmer dashboard</a></li>
                            @else
                                <li><a class="dropdown-item" href="{{ route('customer.dashboard') }}">My dashboard</a></li>
                                <li><a class="dropdown-item" href="{{ route('customer.favorites.index') }}">Favorites</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button class="dropdown-item" type="submit">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-sm btn-primary">Register</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
