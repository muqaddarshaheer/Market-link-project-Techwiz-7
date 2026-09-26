@php
    $dockCartUrl = auth()->check() && auth()->user()->isCustomer()
        ? route('cart.index')
        : (auth()->guest() ? route('guest.cart') : null);
    $dockAccount = auth()->check()
        ? (auth()->user()->isCustomer() ? route('customer.dashboard') : (auth()->user()->isFarmer() ? route('farmer.dashboard') : route('admin.dashboard')))
        : route('login');
@endphp
<nav class="mobile-dock d-lg-none" aria-label="Quick navigation">
    <a class="mobile-dock-item {{ request()->routeIs('home') ? 'is-on' : '' }}" href="{{ route('home') }}">
        <i class="bi bi-house"></i><span>Home</span>
    </a>
    <a class="mobile-dock-item {{ request()->routeIs('products.*') ? 'is-on' : '' }}" href="{{ route('products.index') }}">
        <i class="bi bi-grid"></i><span>Browse</span>
    </a>
    @if($dockCartUrl)
    <a class="mobile-dock-item {{ request()->routeIs('guest.cart', 'guest.checkout', 'guest.checkout.store') || request()->routeIs('cart.*') ? 'is-on' : '' }}" href="{{ $dockCartUrl }}" data-cart-link>
            <i class="bi bi-bag"></i><span>Cart</span>
            <span class="mobile-dock-badge {{ ($cartCount ?? 0) < 1 ? 'd-none' : '' }}" data-cart-badge>{{ $cartCount ?? 0 }}</span>
        </a>
    @endif
    <a class="mobile-dock-item {{ request()->routeIs('customer.*', 'farmer.*', 'admin.*', 'login', 'register') ? 'is-on' : '' }}" href="{{ $dockAccount }}">
        <i class="bi bi-person"></i><span>{{ auth()->check() ? 'You' : 'Join' }}</span>
    </a>
</nav>
