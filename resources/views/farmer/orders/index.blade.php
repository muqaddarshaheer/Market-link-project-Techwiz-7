@extends('layouts.app')
@section('title', 'Incoming orders')
@section('content')
<h1 class="section-title">Orders</h1>
<form method="GET" class="mb-3"><select class="form-select" style="max-width:240px" name="status" onchange="this.form.submit()"><option value="">All</option>@foreach(['placed','accepted','ready_for_pickup','completed','declined','cancelled'] as $s)<option @selected(request('status')===$s)>{{ $s }}</option>@endforeach</select></form>
@foreach($orders as $order)
<div class="card-ml p-3 mb-3">
    <div class="d-flex justify-content-between"><strong>{{ $order->order_number }}</strong>@include('partials.order-status-badge', ['status'=>$order->status])</div>
    <div class="small muted">{{ $order->customer->name }} · {{ $order->pickup_date->format('M j') }} {{ $order->pickup_slot }} · {{ $order->market->name }}</div>
    <ul class="mb-2">@foreach($order->items as $item)<li>{{ $item->product_name }} × {{ $item->quantity }}</li>@endforeach</ul>
    <strong>${{ number_format($order->total_amount, 2) }}</strong>
    @if($order->customer_note)<div>Note: {{ $order->customer_note }}</div>@endif
    @if($order->status === 'placed')
        <form method="POST" action="{{ route('farmer.orders.accept', $order) }}" class="d-flex gap-2 mt-2">@csrf<input class="form-control" name="farmer_notes" placeholder="Optional note"><button class="btn btn-ml">Accept</button></form>
        <form method="POST" action="{{ route('farmer.orders.decline', $order) }}" class="d-flex gap-2 mt-2">@csrf<input class="form-control" name="farmer_notes" placeholder="Reason"><button class="btn btn-outline-danger">Decline</button></form>
    @elseif($order->status === 'accepted')
        <form method="POST" action="{{ route('farmer.orders.ready', $order) }}" class="mt-2">@csrf<button class="btn btn-ml">Mark ready</button></form>
    @endif
    @if(in_array($order->status, ['accepted','ready_for_pickup']))
        <form method="POST" action="{{ route('farmer.orders.complete', $order) }}" class="mt-2">@csrf<button class="btn btn-outline-ml">Mark completed</button></form>
    @endif
</div>
@endforeach
{{ $orders->links() }}
@endsection
