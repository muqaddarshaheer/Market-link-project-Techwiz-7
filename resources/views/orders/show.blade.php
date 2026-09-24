@extends('layouts.app')
@section('title', 'Order '.$order->order_number)
@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
        <div>
            <h1 class="display-font h3 mb-1">Order {{ $order->order_number }}</h1>
            <span class="badge {{ $order->statusBadgeClass() }} badge-status">{{ $order->statusLabel() }}</span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('customer.orders.invoice', $order) }}" class="btn btn-outline-secondary" target="_blank">Print invoice</a>
            @if($order->canModify())
                <a href="{{ route('customer.orders.edit', $order) }}" class="btn btn-outline-primary">Modify</a>
                <form method="POST" action="{{ route('customer.orders.cancel', $order) }}">@csrf
                    <button class="btn btn-outline-danger" onclick="return confirm('Cancel this order?')">Cancel</button>
                </form>
            @endif
            @if($order->canReview())
                <a href="{{ route('customer.reviews.create', $order) }}" class="btn btn-primary">Leave review</a>
            @endif
            @if($order->status === 'completed')
                <form method="POST" action="{{ route('customer.orders.reorder', $order) }}">@csrf
                    <button class="btn btn-outline-success">Reorder</button>
                </form>
            @endif
        </div>
    </div>

    <ul class="timeline">
        @foreach($order->timelineSteps() as $step)
            <li class="{{ !empty($step['done']) ? 'done' : '' }} {{ !empty($step['failed']) ? 'failed' : '' }}">{{ $step['label'] }}</li>
        @endforeach
    </ul>

    <div class="row g-4">
        <div class="col-md-7">
            <div class="panel">
                <h5>Items</h5>
                <ul class="list-group list-group-flush">
                    @foreach($order->items as $item)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $item->product_name }} × {{ $item->quantity }}</span>
                            <span>${{ number_format($item->subtotal, 2) }}</span>
                        </li>
                    @endforeach
                </ul>
                <div class="mt-3 fw-bold fs-5">Total: ${{ number_format($order->total_amount, 2) }}</div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="panel">
                <h5>Pickup</h5>
                <p class="mb-1"><strong>Date:</strong> {{ $order->pickup_date->format('M d, Y') }}</p>
                <p class="mb-1"><strong>Slot:</strong> {{ $order->pickup_slot }}</p>
                <p class="mb-1"><strong>Market:</strong> {{ $order->market->name }}</p>
                <p class="mb-1"><strong>Farmer:</strong> {{ $order->farmer->stall_name }}</p>
                <p class="mb-1"><strong>Cutoff:</strong> {{ $order->cutoff_time?->format('M d, Y g:i A') }}</p>
                @if($order->customer_note)<p><strong>Your note:</strong> {{ $order->customer_note }}</p>@endif
                @if($order->farmer_notes)<p><strong>Farmer note:</strong> {{ $order->farmer_notes }}</p>@endif
            </div>
        </div>
    </div>
</div>
@endsection
