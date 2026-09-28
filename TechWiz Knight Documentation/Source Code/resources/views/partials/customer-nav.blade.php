<nav class="role-nav" aria-label="Customer">
    <a class="{{ request()->routeIs('customer.dashboard') ? 'active' : '' }}" href="{{ route('customer.dashboard') }}">Dashboard</a>
    <a class="{{ request()->routeIs('customer.orders.*') ? 'active' : '' }}" href="{{ route('customer.orders.index') }}">Orders</a>
    <a class="{{ request()->routeIs('customer.favorites') ? 'active' : '' }}" href="{{ route('customer.favorites') }}">Favorites</a>
    <a class="{{ request()->routeIs('customer.reviews') ? 'active' : '' }}" href="{{ route('customer.reviews') }}">Reviews</a>
    <a class="{{ request()->routeIs('customer.notifications') ? 'active' : '' }}" href="{{ route('customer.notifications') }}">Notifications</a>
    <a class="{{ request()->routeIs('customer.profile') ? 'active' : '' }}" href="{{ route('customer.profile') }}">Profile</a>
    <a class="{{ request()->routeIs('cart.*') ? 'active' : '' }}" href="{{ route('cart.index') }}">Cart</a>
</nav>
