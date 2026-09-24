@extends('layouts.app')
@section('title', 'My orders')
@section('content')
<div class="container py-5">
    <h1 class="display-font mb-3">Order history</h1>
    <form class="mb-3 d-flex gap-2" method="GET">
        <select name="status" class="form-select w-auto">
            <option value="">All statuses</option>
            @foreach(\App\Models\Order::STATUSES as $s)
                <option value="{{ $s }}" @selected(request('status')==$s)>{{ str_replace('_',' ',$s) }}</option>
            @endforeach
        </select>
        <button class="btn btn-outline-primary">Filter</button>
    </form>
    <div class="panel table-responsive">
        <table class="table mb-0">
            <thead><tr><th>Order</th><th>Farmer</th><th>Pickup</th><th>Total</th><th>Status</th></tr></thead>
            <tbody>
            @foreach($orders as $order)
                <tr>
                    <td><a href="{{ route('customer.orders.show', $order) }}">{{ $order->order_number }}</a></td>
                    <td>{{ $order->farmer->stall_name }}</td>
                    <td>{{ $order->pickup_date->format('M d, Y') }}</td>
                    <td>${{ number_format($order->total_amount, 2) }}</td>
                    <td><span class="badge {{ $order->statusBadgeClass() }}">{{ $order->statusLabel() }}</span></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $orders->links() }}</div>
</div>
@endsection
