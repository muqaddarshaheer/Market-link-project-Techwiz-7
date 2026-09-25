@extends('layouts.farmer')
@section('title', 'Farmer dashboard')
@section('content')
@if($farmer->approval_status !== 'approved')
    <div class="alert alert-warning d-flex align-items-start gap-2 mb-4">
        <i class="bi bi-hourglass-split fs-5"></i>
        <div>
            <strong>Stall status: {{ $farmer->approval_status }}</strong>
            <div class="small mb-0">Listings stay hidden until an admin approves your stall. You can still finish your profile and pickup slots.</div>
        </div>
    </div>
@endif

<div class="panel-head mb-4">
    <div>
        <p class="panel-kicker mb-1">Stall</p>
        <h1 class="section-title mb-1">{{ $farmer->stall_name }}</h1>
        <p class="muted mb-0">Manage stock, accept pickups, and track stall revenue in Rs.</p>
    </div>
    <div class="panel-actions">
        <a class="btn btn-outline-ml btn-sm" href="{{ route('farmer.products.index') }}"><i class="bi bi-basket"></i> Products</a>
        <a class="btn btn-ml btn-sm" href="{{ route('farmer.orders.index') }}"><i class="bi bi-receipt"></i> Orders</a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3"><a class="stat-tile tone-orange d-block text-decoration-none" href="{{ route('farmer.orders.index') }}"><span>Orders</span><strong>{{ $stats['orders'] }}</strong></a></div>
    <div class="col-6 col-md-3"><a class="stat-tile tone-amber d-block text-decoration-none" href="{{ route('farmer.orders.index', ['status' => 'placed']) }}"><span>Pending</span><strong>{{ $stats['pending'] }}</strong></a></div>
    <div class="col-6 col-md-3"><div class="stat-tile tone-forest"><span>Revenue</span><strong>{{ money($stats['revenue']) }}</strong></div></div>
    <div class="col-6 col-md-3"><a class="stat-tile tone-lime d-block text-decoration-none" href="{{ route('farmer.products.index') }}"><span>Products</span><strong>{{ $stats['products'] }}</strong></a></div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4"><a class="panel-quick card-ml panel-card p-3 d-flex align-items-center gap-3 text-decoration-none text-reset" href="{{ route('farmer.slots.index') }}"><span class="panel-quick-icon tone-amber"><i class="bi bi-clock"></i></span><span><strong>Pickup slots</strong><div class="small muted">Windows and cutoff hours</div></span></a></div>
    <div class="col-md-4"><a class="panel-quick card-ml panel-card p-3 d-flex align-items-center gap-3 text-decoration-none text-reset" href="{{ route('farmer.insights') }}"><span class="panel-quick-icon tone-teal"><i class="bi bi-bar-chart"></i></span><span><strong>Insights</strong><div class="small muted">Sales by day</div></span></a></div>
    <div class="col-md-4"><a class="panel-quick card-ml panel-card p-3 d-flex align-items-center gap-3 text-decoration-none text-reset" href="{{ route('farmer.profile') }}"><span class="panel-quick-icon tone-green"><i class="bi bi-shop"></i></span><span><strong>Stall profile</strong><div class="small muted">Photo, phone, markets</div></span></a></div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h2 class="h5 mb-0">Recent orders</h2>
            <a class="small fw-bold" href="{{ route('farmer.orders.index') }}">Manage orders</a>
        </div>
        @forelse($recent as $order)
            <a class="panel-list-item card-ml panel-card p-3 mb-2 d-flex justify-content-between align-items-center gap-2 text-decoration-none text-reset" href="{{ route('farmer.orders.index') }}">
                <span>
                    <strong>{{ $order->order_number }}</strong>
                    <div class="small muted">{{ $order->buyerName() }} · {{ money($order->total_amount) }}</div>
                </span>
                @include('partials.order-status-badge', ['status'=>$order->status])
            </a>
        @empty
            <div class="empty-state card-ml panel-card"><i class="bi bi-receipt"></i><p>No pickup orders yet.</p></div>
        @endforelse
    </div>
    <div class="col-lg-5">
        <h2 class="h5 mb-2">Low stock</h2>
        @forelse($lowStock as $product)
            <a class="stat-tile tone-rose p-2 mb-2 d-block text-decoration-none" href="{{ route('farmer.products.index') }}">
                <strong>{{ $product->name }}</strong>
                <span>{{ $product->stock_quantity }} left</span>
            </a>
        @empty
            <p class="muted">Stock looks healthy.</p>
        @endforelse

        @if(($best ?? collect())->isNotEmpty())
            <h2 class="h5 mt-4 mb-2">Top sellers</h2>
            @foreach($best as $product)
                <div class="panel-list-item card-ml panel-card p-2 px-3 mb-2 d-flex justify-content-between">
                    <span>{{ $product->name }}</span>
                    <strong>{{ (int) ($product->sold ?? 0) }} sold</strong>
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection
