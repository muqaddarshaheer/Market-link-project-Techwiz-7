@extends('layouts.app')
@section('title', 'Farmer orders')
@section('content')
<div class="container py-5">
    <h1 class="display-font h3 mb-3">Incoming orders</h1>
    <form class="mb-3" method="GET">
        <select name="status" class="form-select w-auto d-inline" onchange="this.form.submit()">
            <option value="">All</option>
            @foreach(\App\Models\Order::STATUSES as $s)
                <option value="{{ $s }}" @selected(request('status')==$s)>{{ $s }}</option>
            @endforeach
        </select>
    </form>
    <div class="panel table-responsive">
        <table class="table mb-0">
            <thead><tr><th>Order</th><th>Customer</th><th>Pickup</th><th>Total</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>{{ $order->order_number }}</td>
                    <td>{{ $order->customer->name }}</td>
                    <td>{{ $order->pickup_date->format('M d') }} {{ $order->pickup_slot }}</td>
                    <td>${{ number_format($order->total_amount,2) }}</td>
                    <td><span class="badge {{ $order->statusBadgeClass() }}">{{ $order->statusLabel() }}</span></td>
                    <td><a href="{{ route('farmer.orders.show', $order) }}">Open</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {{ $orders->links() }}
</div>
@endsection
