@extends('layouts.customer')
@section('title', 'Orders')
@section('content')
<h1 class="section-title">Order history</h1>
<form class="row g-2 mb-3" method="GET">
    <div class="col-md-3"><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Order number"></div>
    <div class="col-md-2"><input class="form-control" type="date" name="from" value="{{ request('from') }}"></div>
    <div class="col-md-2"><input class="form-control" type="date" name="to" value="{{ request('to') }}"></div>
    <div class="col-md-3"><select class="form-select" name="status" onchange="this.form.submit()">
        <option value="">All statuses</option>
        @foreach(['placed','accepted','declined','ready_for_pickup','completed','cancelled'] as $status)
            <option value="{{ $status }}" @selected(request('status')===$status)>{{ str_replace('_',' ',$status) }}</option>
        @endforeach
    </select></div>
    <div class="col-md-2"><button class="btn btn-ml w-100">Filter</button></div>
</form>
@forelse($orders as $order)
    <div class="card-ml p-3 mb-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <a href="{{ route('customer.orders.show', $order) }}"><strong>{{ $order->order_number }}</strong></a>
            <div class="small muted">{{ $order->farmer->stall_name }} · {{ $order->pickup_date->format('M j') }} · {{ money($order->total_amount) }}</div>
        </div>
        <div class="d-flex gap-2 align-items-center">
            @include('partials.order-status-badge', ['status' => $order->status])
            <form method="POST" action="{{ route('customer.orders.reorder', $order) }}">@csrf<button class="btn btn-outline-ml btn-sm">Reorder</button></form>
        </div>
    </div>
@empty
    <div class="empty-state card-ml"><i class="bi bi-receipt"></i><p>No orders yet.</p></div>
@endforelse
{{ $orders->links() }}
@endsection
