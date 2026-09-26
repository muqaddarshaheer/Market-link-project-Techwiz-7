@extends('layouts.customer')
@section('title', 'Your dashboard')
@section('content')
<div class="panel-head mb-4">
    <div>
        <p class="panel-kicker mb-1">Customer</p>
        <h1 class="section-title mb-1">Hello, {{ auth()->user()->name }}</h1>
        <p class="muted mb-0">Track pickups, favorites, and market orders — pay the farmer when you collect.</p>
    </div>
    <div class="panel-actions">
        <a class="btn btn-outline-ml btn-sm" href="{{ route('products.index') }}"><i class="bi bi-basket"></i> Browse produce</a>
        <a class="btn btn-ml btn-sm" href="{{ route('cart.index') }}"><i class="bi bi-bag"></i> Cart</a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4"><a class="stat-tile tone-orange d-block text-decoration-none" href="{{ route('customer.orders.index') }}"><span><i class="bi bi-receipt"></i> Orders</span><strong>{{ $stats['orders'] }}</strong></a></div>
    <div class="col-md-4"><a class="stat-tile tone-amber d-block text-decoration-none" href="{{ route('customer.orders.index') }}"><span><i class="bi bi-clock"></i> Open pickups</span><strong>{{ $stats['open'] }}</strong></a></div>
    <div class="col-md-4"><a class="stat-tile tone-rose d-block text-decoration-none" href="{{ route('customer.favorites') }}"><span><i class="bi bi-heart"></i> Favorites</span><strong>{{ $stats['favorites'] }}</strong></a></div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4"><a class="panel-quick card-ml panel-card p-3 d-flex align-items-center gap-3 text-decoration-none text-reset" href="{{ route('markets.index') }}"><span class="panel-quick-icon tone-green"><i class="bi bi-geo-alt"></i></span><span><strong>Find a market</strong><div class="small muted">See days and pickup windows</div></span></a></div>
    <div class="col-md-4"><a class="panel-quick card-ml panel-card p-3 d-flex align-items-center gap-3 text-decoration-none text-reset" href="{{ route('farmers.index') }}"><span class="panel-quick-icon tone-lime"><i class="bi bi-shop"></i></span><span><strong>Trusted farmers</strong><div class="small muted">Call a stall directly</div></span></a></div>
    <div class="col-md-4"><a class="panel-quick card-ml panel-card p-3 d-flex align-items-center gap-3 text-decoration-none text-reset" href="{{ route('customer.profile') }}"><span class="panel-quick-icon tone-blue"><i class="bi bi-person"></i></span><span><strong>Your profile</strong><div class="small muted">Update details or password</div></span></a></div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h2 class="h5 mb-0">Recent orders</h2>
            <a class="small fw-bold" href="{{ route('customer.orders.index') }}">All orders</a>
        </div>
        @forelse($orders as $order)
            <a class="panel-list-item card-ml panel-card p-3 mb-2 d-block text-decoration-none text-reset" href="{{ route('customer.orders.show', $order) }}">
                <div class="d-flex justify-content-between gap-2"><strong>{{ $order->order_number }}</strong>@include('partials.order-status-badge', ['status'=>$order->status])</div>
                <div class="small muted mt-1">{{ $order->farmer->stall_name }} · {{ money($order->total_amount) }} · {{ $order->created_at->diffForHumans() }}</div>
            </a>
        @empty
            <div class="empty-state card-ml panel-card"><i class="bi bi-bag"></i><p>No orders yet. Start with a market browse.</p><a class="btn btn-ml btn-sm mt-2" href="{{ route('products.index') }}">Browse products</a></div>
        @endforelse
    </div>
    <div class="col-lg-5">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h2 class="h5 mb-0">Notifications</h2>
            <a class="small fw-bold" href="{{ route('customer.notifications') }}">Inbox</a>
        </div>
        @forelse($notifications as $note)
            <div class="panel-list-item card-ml panel-card p-3 mb-2 {{ $note->is_read ? '' : 'is-unread' }}">
                <strong>{{ $note->title }}</strong>
                <div class="small mt-1">{{ $note->message }}</div>
                <div class="small muted mt-1">{{ $note->created_at?->diffForHumans() }}</div>
            </div>
        @empty
            <p class="muted">You are all caught up.</p>
        @endforelse
    </div>
</div>
@endsection
