@extends('layouts.admin')
@section('title', 'Orders')
@section('content')
<div class="panel-head mb-4">
    <div>
        <p class="panel-kicker mb-1">Operations</p>
        <h1 class="section-title mb-1">Orders</h1>
        <p class="muted mb-0">Pickup pre-orders across stalls — paid in person at the market.</p>
    </div>
</div>
<form class="desk-toolbar mb-3" method="GET">
    <input class="form-control" name="q" value="{{ request('q') }}" placeholder="Order number">
    <select class="form-select" name="status">
        <option value="">Any status</option>
        @foreach(['placed','accepted','declined','ready_for_pickup','completed','cancelled'] as $status)
            <option value="{{ $status }}" @selected(request('status')===$status)>{{ str_replace('_',' ',$status) }}</option>
        @endforeach
    </select>
    <select class="form-select" name="market"><option value="">Any market</option>@foreach($markets as $market)<option value="{{ $market->id }}" @selected(request('market')==$market->id)>{{ $market->name }}</option>@endforeach</select>
    <select class="form-select" name="farmer"><option value="">Any farmer</option>@foreach($farmers as $farmer)<option value="{{ $farmer->id }}" @selected(request('farmer')==$farmer->id)>{{ $farmer->stall_name }}</option>@endforeach</select>
    <input class="form-control" type="date" name="from" value="{{ request('from') }}">
    <input class="form-control" type="date" name="to" value="{{ request('to') }}">
    <button class="btn btn-ml" type="submit">Filter</button>
</form>
<div class="table-responsive card-ml panel-card admin-table-wrap">
<table class="table admin-table mb-0 align-middle">
    <thead><tr><th>Order</th><th>Customer</th><th>Farmer</th><th>Market</th><th>Pickup</th><th>Total</th><th>Status</th><th></th></tr></thead>
    <tbody>
    @forelse($orders as $order)
        <tr>
            <td class="fw-semibold">{{ $order->order_number }}</td>
            <td>{{ $order->buyerName() }}</td>
            <td>{{ $order->farmer->stall_name }}</td>
            <td>{{ $order->market->name }}</td>
            <td>{{ $order->pickup_date }} · {{ $order->pickup_slot }}</td>
            <td>{{ money($order->total_amount) }}</td>
            <td>@include('partials.order-status-badge', ['status'=>$order->status])</td>
            <td><a class="btn btn-sm btn-outline-ml" href="{{ route('admin.orders.show', $order) }}">View</a></td>
        </tr>
    @empty
        <tr><td colspan="8" class="p-4 muted text-center">No orders match these filters.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
<div class="mt-3">{{ $orders->links() }}</div>
@endsection
