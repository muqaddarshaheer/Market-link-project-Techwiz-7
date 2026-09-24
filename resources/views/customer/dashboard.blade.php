@extends('layouts.app')
@section('title', 'Customer dashboard')
@section('content')
<div class="container py-5">
    <h1 class="display-font">Hello, {{ auth()->user()->name }}</h1>
    <p class="section-sub">Track pre-orders, favorites, and pickup plans.</p>
    <div class="row g-3 mb-4">
        @foreach($stats as $label => $value)
            <div class="col-6 col-md-3"><div class="stat-tile"><div class="text-muted small text-uppercase">{{ str_replace('_',' ',$label) }}</div><div class="value">{{ $value }}</div></div></div>
        @endforeach
    </div>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="panel">
                <div class="d-flex justify-content-between mb-3"><h5 class="mb-0">Recent orders</h5><a href="{{ route('customer.orders.index') }}">View all</a></div>
                @forelse($orders as $order)
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <div>
                            <a href="{{ route('customer.orders.show', $order) }}">{{ $order->order_number }}</a>
                            <div class="small text-muted">{{ $order->farmer->stall_name }} · {{ $order->pickup_date->format('M d') }}</div>
                        </div>
                        <span class="badge {{ $order->statusBadgeClass() }}">{{ $order->statusLabel() }}</span>
                    </div>
                @empty
                    <p class="text-muted mb-0">No orders yet.</p>
                @endforelse
            </div>
        </div>
        <div class="col-lg-4">
            <div class="panel mb-3">
                <h5>Quick links</h5>
                <a class="d-block mb-2" href="{{ route('products.index') }}">Browse products</a>
                <a class="d-block mb-2" href="{{ route('cart.index') }}">Cart</a>
                <a class="d-block mb-2" href="{{ route('customer.favorites.index') }}">Favorites</a>
                <a class="d-block" href="{{ route('customer.profile') }}">Profile</a>
            </div>
            @foreach($announcements as $a)
                <div class="alert alert-success">{{ $a->title }}</div>
            @endforeach
        </div>
    </div>
</div>
@endsection
