@extends('layouts.farmer')
@section('title', 'Incoming orders')
@section('content')
<div class="panel-head mb-4">
    <div>
        <p class="panel-kicker mb-1">Sales</p>
        <h1 class="section-title mb-0">Orders</h1>
    </div>
    <form method="GET" class="d-flex gap-2">
        <select class="form-select form-select-sm" style="max-width:200px" name="status" onchange="this.form.submit()">
            <option value="">All statuses</option>
            @foreach(['placed','accepted','ready_for_pickup','completed','declined','cancelled'] as $s)
                <option value="{{ $s }}" @selected(request('status')===$s)>{{ str_replace('_', ' ', $s) }}</option>
            @endforeach
        </select>
    </form>
</div>
@forelse($orders as $order)
<div class="panel-list-item card-ml panel-card p-3 mb-3">
    <div class="d-flex justify-content-between gap-2"><strong>{{ $order->order_number }}</strong>@include('partials.order-status-badge', ['status'=>$order->status])</div>
    <div class="small muted">{{ $order->buyerName() }} · {{ $order->pickup_date->format('M j') }} {{ $order->pickup_slot }} · {{ $order->market->name }}</div>
    <ul class="mb-2 mt-2">@foreach($order->items as $item)<li>{{ $item->product_name }} × {{ $item->quantity }}</li>@endforeach</ul>
    <strong>{{ money($order->total_amount) }}</strong>
    @if($order->customer_note)<div class="small mt-1">Note: {{ $order->customer_note }}</div>@endif
    @if($order->status === 'placed')
        <form method="POST" action="{{ route('farmer.orders.accept', $order) }}" class="d-flex gap-2 mt-2">@csrf<input class="form-control" name="farmer_notes" placeholder="Optional note"><button class="btn btn-ml" type="submit">Accept</button></form>
        <form method="POST" action="{{ route('farmer.orders.decline', $order) }}" class="d-flex gap-2 mt-2">@csrf<input class="form-control" name="farmer_notes" placeholder="Reason"><button class="btn btn-outline-danger" type="submit">Decline</button></form>
    @elseif($order->status === 'accepted')
        <form method="POST" action="{{ route('farmer.orders.ready', $order) }}" class="mt-2">@csrf<button class="btn btn-ml" type="submit">Mark ready</button></form>
    @endif
    @if(in_array($order->status, ['accepted','ready_for_pickup']))
        <form method="POST" action="{{ route('farmer.orders.complete', $order) }}" class="mt-2">@csrf<button class="btn btn-outline-ml" type="submit">Mark completed</button></form>
    @endif
</div>
@empty
    <div class="empty-state card-ml panel-card"><i class="bi bi-receipt"></i><p>No orders for this filter.</p></div>
@endforelse
{{ $orders->links() }}
@endsection
