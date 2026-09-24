@extends('layouts.app')
@section('title', 'Order '.$order->order_number)
@section('content')
<div class="container py-5">
    <h1 class="display-font h3">{{ $order->order_number }}</h1>
    <p>Customer: {{ $order->customer->name }} ({{ $order->customer->phone }}) · Status: <span class="badge {{ $order->statusBadgeClass() }}">{{ $order->statusLabel() }}</span></p>
    <ul class="timeline">
        @foreach($order->timelineSteps() as $step)
            <li class="{{ !empty($step['done'])?'done':'' }} {{ !empty($step['failed'])?'failed':'' }}">{{ $step['label'] }}</li>
        @endforeach
    </ul>
    <div class="panel mb-3">
        @foreach($order->items as $item)
            <div class="d-flex justify-content-between"><span>{{ $item->product_name }} × {{ $item->quantity }}</span><span>${{ number_format($item->subtotal,2) }}</span></div>
        @endforeach
        <hr><strong>Total ${{ number_format($order->total_amount,2) }}</strong>
    </div>
    <form method="POST" action="{{ route('farmer.orders.status', $order) }}" class="panel col-lg-6">
        @csrf
        <div class="mb-3">
            <label class="form-label">Update status</label>
            <select name="status" class="form-select" required>
                @if($order->status==='placed')
                    <option value="accepted">Accept</option>
                    <option value="declined">Decline</option>
                @elseif($order->status==='accepted')
                    <option value="ready_for_pickup">Ready for pickup</option>
                    <option value="declined">Decline</option>
                @elseif($order->status==='ready_for_pickup')
                    <option value="completed">Completed</option>
                @endif
            </select>
        </div>
        <div class="mb-3"><label class="form-label">Notes</label><textarea name="farmer_notes" class="form-control" rows="2">{{ $order->farmer_notes }}</textarea></div>
        @if(in_array($order->status, ['placed','accepted','ready_for_pickup']))
            <button class="btn btn-primary">Save status</button>
        @endif
    </form>
</div>
@endsection
