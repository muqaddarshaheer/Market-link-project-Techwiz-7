@extends('layouts.customer')
@section('title', 'Your dashboard')
@section('content')
<h1 class="section-title">Hello, {{ auth()->user()->name }}</h1>
<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="card-ml stat"><span class="muted">Orders</span><strong>{{ $stats['orders'] }}</strong></div></div>
    <div class="col-md-4"><div class="card-ml stat"><span class="muted">Open pickups</span><strong>{{ $stats['open'] }}</strong></div></div>
    <div class="col-md-4"><div class="card-ml stat"><span class="muted">Favorites</span><strong>{{ $stats['favorites'] }}</strong></div></div>
</div>
<div class="row g-4">
    <div class="col-lg-7">
        <h2 class="h5">Recent orders</h2>
        @forelse($orders as $order)
            <a class="card-ml p-3 mb-2 d-block text-decoration-none text-reset" href="{{ route('customer.orders.show', $order) }}">
                <div class="d-flex justify-content-between"><strong>{{ $order->order_number }}</strong>@include('partials.order-status-badge', ['status'=>$order->status])</div>
                <div class="small muted">{{ $order->farmer->stall_name }}</div>
            </a>
        @empty
            <div class="empty-state card-ml"><i class="bi bi-bag"></i><p>Start with a market browse.</p></div>
        @endforelse
    </div>
    <div class="col-lg-5">
        <h2 class="h5">Notifications</h2>
        @forelse($notifications as $note)
            <div class="card-ml p-3 mb-2 {{ $note->is_read ? '' : 'border-success' }}"><strong>{{ $note->title }}</strong><div class="small">{{ $note->message }}</div></div>
        @empty
            <p class="muted">You are all caught up.</p>
        @endforelse
    </div>
</div>
@endsection
