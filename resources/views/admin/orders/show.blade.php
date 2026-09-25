@extends('layouts.admin')
@section('title', $order->order_number)
@section('content')
<p><a href="{{ route('admin.orders.index') }}">All orders</a></p>
<h1 class="section-title">{{ $order->order_number }}</h1>
<p class="muted">{{ $order->buyerName() }} @if($order->guest_phone)· {{ $order->guest_phone }}@endif · {{ $order->farmer->stall_name }} · {{ $order->market->name }}</p>
@include('partials.order-status-badge', ['status' => $order->status])
<div class="row g-3 mt-2">
    <div class="col-lg-7">
        <div class="card-ml p-3">
            @foreach($order->items as $item)
                <div class="d-flex justify-content-between"><span>{{ $item->product_name }} × {{ $item->quantity }}</span><span>{{ money($item->subtotal) }}</span></div>
            @endforeach
            <hr>
            <strong>Pay at pickup {{ money($order->total_amount) }}</strong>
            <form method="POST" action="{{ route('admin.orders.payment', $order) }}" class="d-flex gap-2 mt-2">@csrf
                <select class="form-select" name="payment_status">
                    <option value="unpaid" @selected($order->payment_status==='unpaid')>Unpaid</option>
                    <option value="paid" @selected($order->payment_status==='paid')>Paid in person</option>
                </select>
                <button class="btn btn-ml btn-sm">Save</button>
            </form>
            <div class="small muted mt-2">Pickup {{ $order->pickup_date }} {{ $order->pickup_slot }}. Cutoff {{ $order->cutoff_time }}.</div>
            @if($order->customer_note)<p class="mt-2 mb-0">Customer note: {{ $order->customer_note }}</p>@endif
            @if($order->farmer_notes)<p class="mb-0">Farmer note: {{ $order->farmer_notes }}</p>@endif
        </div>
    </div>
    <div class="col-lg-5">
        @include('partials.order-timeline', ['order' => $order])
        <div class="mt-3 d-flex gap-2 flex-wrap">
            @if($order->customer)<a class="btn btn-outline-ml btn-sm" href="{{ route('admin.users.show', $order->customer) }}">Customer</a>@else<span class="small muted">Guest · {{ $order->guest_address }}</span>@endif
            <a class="btn btn-outline-ml btn-sm" href="{{ route('admin.farmers.show', $order->farmer) }}">Farmer</a>
            <a class="btn btn-outline-ml btn-sm" href="{{ route('markets.show', $order->market) }}">Market</a>
        </div>
    </div>
</div>
@endsection
