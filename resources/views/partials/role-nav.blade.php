@auth
<nav class="role-nav" aria-label="Account">
    @if(auth()->user()->isCustomer())
        <a class="{{ request()->routeIs('customer.dashboard') ? 'active' : '' }}" href="{{ route('customer.dashboard') }}">Dashboard</a>
        <a class="{{ request()->routeIs('customer.orders.*') ? 'active' : '' }}" href="{{ route('customer.orders.index') }}">Orders</a>
        <a class="{{ request()->routeIs('customer.favorites') ? 'active' : '' }}" href="{{ route('customer.favorites') }}">Favorites</a>
        <a class="{{ request()->routeIs('customer.reviews') ? 'active' : '' }}" href="{{ route('customer.reviews') }}">Reviews</a>
        <a class="{{ request()->routeIs('customer.notifications') ? 'active' : '' }}" href="{{ route('customer.notifications') }}">Notifications</a>
        <a class="{{ request()->routeIs('customer.profile') ? 'active' : '' }}" href="{{ route('customer.profile') }}">Profile</a>
        <a class="{{ request()->routeIs('cart.*') ? 'active' : '' }}" href="{{ route('cart.index') }}">Cart</a>
    @elseif(auth()->user()->isFarmer())
        <a class="{{ request()->routeIs('farmer.dashboard') ? 'active' : '' }}" href="{{ route('farmer.dashboard') }}">Dashboard</a>
        <a class="{{ request()->routeIs('farmer.profile') ? 'active' : '' }}" href="{{ route('farmer.profile') }}">Stall profile</a>
        <a class="{{ request()->routeIs('farmer.products.*') ? 'active' : '' }}" href="{{ route('farmer.products.index') }}">Products</a>
        <a class="{{ request()->routeIs('farmer.orders.*') ? 'active' : '' }}" href="{{ route('farmer.orders.index') }}">Orders</a>
        <a class="{{ request()->routeIs('farmer.slots.*') ? 'active' : '' }}" href="{{ route('farmer.slots.index') }}">Pickup slots</a>
        <a class="{{ request()->routeIs('farmer.reviews') ? 'active' : '' }}" href="{{ route('farmer.reviews') }}">Reviews</a>
        <a class="{{ request()->routeIs('farmer.insights') ? 'active' : '' }}" href="{{ route('farmer.insights') }}">Insights</a>
        <a class="{{ request()->routeIs('farmer.notifications') ? 'active' : '' }}" href="{{ route('farmer.notifications') }}">Notifications</a>
    @endif
</nav>
@endauth
