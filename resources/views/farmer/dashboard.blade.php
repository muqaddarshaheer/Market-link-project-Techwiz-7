@extends('layouts.farmer')
@section('title', 'Farmer dashboard')
@section('content')
@if($farmer->approval_status !== 'approved')
    <div class="alert alert-warning">Your stall is {{ $farmer->approval_status }}. You can edit your profile, and product listings open after approval.</div>
@endif
<h1 class="section-title">{{ $farmer->stall_name }}</h1>
<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card-ml stat"><span class="muted">Orders</span><strong>{{ $stats['orders'] }}</strong></div></div>
    <div class="col-md-3"><div class="card-ml stat"><span class="muted">Pending</span><strong>{{ $stats['pending'] }}</strong></div></div>
    <div class="col-md-3"><div class="card-ml stat"><span class="muted">Completed revenue</span><strong>${{ number_format($stats['revenue'], 2) }}</strong></div></div>
    <div class="col-md-3"><div class="card-ml stat"><span class="muted">Products</span><strong>{{ $stats['products'] }}</strong></div></div>
</div>
<div class="row g-4">
    <div class="col-lg-7">
        <h2 class="h5">Recent orders</h2>
        @foreach($recent as $order)
            <div class="card-ml p-3 mb-2 d-flex justify-content-between"><span>{{ $order->order_number }} · {{ $order->customer->name }}</span>@include('partials.order-status-badge', ['status'=>$order->status])</div>
        @endforeach
        <a href="{{ route('farmer.orders.index') }}">Manage orders</a>
    </div>
    <div class="col-lg-5">
        <h2 class="h5">Low stock</h2>
        @forelse($lowStock as $product)
            <div class="card-ml p-2 mb-2">{{ $product->name }} · {{ $product->stock_quantity }} left</div>
        @empty
            <p class="muted">Stock looks healthy.</p>
        @endforelse
    </div>
</div>
@endsection
