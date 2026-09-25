@extends('layouts.admin')
@section('title', 'Orders')
@section('content')
<h1 class="section-title">Orders</h1>
<form class="row g-2 mb-3" method="GET">
    <div class="col-md-3"><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Order number"></div>
    <div class="col-md-2">
        <select class="form-select" name="status">
            <option value="">Any status</option>
            @foreach(['placed','accepted','declined','ready_for_pickup','completed','cancelled'] as $status)
                <option value="{{ $status }}" @selected(request('status')===$status)>{{ str_replace('_',' ',$status) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2"><select class="form-select" name="market"><option value="">Any market</option>@foreach($markets as $market)<option value="{{ $market->id }}" @selected(request('market')==$market->id)>{{ $market->name }}</option>@endforeach</select></div>
    <div class="col-md-2"><select class="form-select" name="farmer"><option value="">Any farmer</option>@foreach($farmers as $farmer)<option value="{{ $farmer->id }}" @selected(request('farmer')==$farmer->id)>{{ $farmer->stall_name }}</option>@endforeach</select></div>
    <div class="col-md-1"><input class="form-control" type="date" name="from" value="{{ request('from') }}"></div>
    <div class="col-md-1"><input class="form-control" type="date" name="to" value="{{ request('to') }}"></div>
    <div class="col-md-1"><button class="btn btn-ml w-100">Filter</button></div>
</form>
<div class="table-responsive card-ml">
<table class="table mb-0">
    <thead><tr><th>Order</th><th>Customer</th><th>Farmer</th><th>Market</th><th>Pickup</th><th>Total</th><th>Status</th><th></th></tr></thead>
    <tbody>
    @forelse($orders as $order)
        <tr>
            <td>{{ $order->order_number }}</td>
            <td>{{ $order->buyerName() }}</td>
            <td>{{ $order->farmer->stall_name }}</td>
            <td>{{ $order->market->name }}</td>
            <td>{{ $order->pickup_date }} {{ $order->pickup_slot }}</td>
            <td>${{ number_format($order->total_amount, 2) }}</td>
            <td>@include('partials.order-status-badge', ['status'=>$order->status])</td>
            <td><a href="{{ route('admin.orders.show', $order) }}">View</a></td>
        </tr>
    @empty
        <tr><td colspan="8" class="p-4 muted">No orders match these filters.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
<div class="mt-3">{{ $orders->links() }}</div>
@endsection
