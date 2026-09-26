<nav class="role-nav" aria-label="Farmer">
    <a class="{{ request()->routeIs('farmer.dashboard') ? 'active' : '' }}" href="{{ route('farmer.dashboard') }}">Dashboard</a>
    <a class="{{ request()->routeIs('farmer.profile') ? 'active' : '' }}" href="{{ route('farmer.profile') }}">Stall profile</a>
    <a class="{{ request()->routeIs('farmer.products.*') ? 'active' : '' }}" href="{{ route('farmer.products.index') }}">Products</a>
    <a class="{{ request()->routeIs('farmer.orders.*') ? 'active' : '' }}" href="{{ route('farmer.orders.index') }}">Orders</a>
    <a class="{{ request()->routeIs('farmer.slots.*') ? 'active' : '' }}" href="{{ route('farmer.slots.index') }}">Pickup slots</a>
    <a class="{{ request()->routeIs('farmer.reviews') ? 'active' : '' }}" href="{{ route('farmer.reviews') }}">Reviews</a>
    <a class="{{ request()->routeIs('farmer.insights') ? 'active' : '' }}" href="{{ route('farmer.insights') }}">Insights</a>
    <a class="{{ request()->routeIs('farmer.notifications') ? 'active' : '' }}" href="{{ route('farmer.notifications') }}">Notifications</a>
</nav>
