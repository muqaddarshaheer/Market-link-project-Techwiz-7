@extends('layouts.customer')
@section('title', $order->order_number)
@section('content')
<div class="d-flex justify-content-between align-items-start">
    <div>
        <h1 class="section-title">{{ $order->order_number }}</h1>
        <p class="muted">{{ $order->farmer->stall_name }} · {{ $order->market->name }} · {{ $order->pickup_date->format('M j, Y') }} {{ $order->pickup_slot }}</p>
    </div>
    @include('partials.order-status-badge', ['status' => $order->status])
</div>
@include('partials.order-timeline')
<div class="card-ml p-3 mb-3">
    @foreach($order->items as $item)
        <div class="d-flex justify-content-between"><span>{{ $item->product_name }} × {{ $item->quantity }}</span><span>${{ number_format($item->subtotal, 2) }}</span></div>
    @endforeach
    <hr>
    <strong>Total ${{ number_format($order->total_amount, 2) }}</strong>
    @if($order->customer_note)<p class="mt-2 mb-0">Note: {{ $order->customer_note }}</p>@endif
    @if($order->farmer_notes)<p class="mb-0">Farmer: {{ $order->farmer_notes }}</p>@endif
    @if($order->cutoff_time)<p class="small muted mb-0">Changes allowed until {{ $order->cutoff_time->format('M j, g:i A') }}</p>@endif
</div>
<div class="d-flex gap-2 flex-wrap no-print">
    <a class="btn btn-outline-ml" href="{{ route('customer.orders.invoice', $order) }}">Print invoice</a>
    <form method="POST" action="{{ route('customer.orders.reorder', $order) }}">@csrf<button class="btn btn-outline-ml">Reorder</button></form>
</div>
@if($order->isOpenForChange())
    <form method="POST" action="{{ route('customer.orders.update', $order) }}" class="card-ml p-3 mt-3">
        @csrf @method('PUT')
        <h2 class="h6">Change pickup</h2>
        <div class="row g-2">
            <div class="col-md-4"><input class="form-control" type="date" name="pickup_date" value="{{ $order->pickup_date->toDateString() }}" min="{{ now()->toDateString() }}"></div>
            <div class="col-md-4"><input class="form-control" name="pickup_slot" value="{{ $order->pickup_slot }}"></div>
            <div class="col-md-4"><input class="form-control" name="customer_note" value="{{ $order->customer_note }}" placeholder="Note"></div>
        </div>
        <button class="btn btn-ml mt-2">Save changes</button>
    </form>
    <form method="POST" action="{{ route('customer.orders.cancel', $order) }}" class="mt-2" onsubmit="return confirm('Cancel this order?')">@csrf<button class="btn btn-outline-danger">Cancel order</button></form>
@endif
@php $canReview = $order->status === 'completed' && $order->reviews->where('customer_id', auth()->id())->isEmpty(); @endphp
@if($canReview)
    <form method="POST" action="{{ route('customer.reviews.store', $order) }}" class="card-ml p-3 mt-3">
        @csrf
        <h2 class="h6">Review this pickup</h2>
        <label class="form-label">Stars</label>
        <select class="form-select mb-2" name="rating" required>@for($i=5;$i>=1;$i--)<option value="{{ $i }}">{{ $i }}</option>@endfor</select>
        <select class="form-select mb-2" name="product_id">
            <option value="">Whole stall</option>
            @foreach($order->items as $item)
                @if($item->product_id)
                    <option value="{{ $item->product_id }}">{{ $item->product_name }}</option>
                @endif
            @endforeach
        </select>
        <textarea class="form-control mb-2" name="comment" placeholder="How was the produce?"></textarea>
        <button class="btn btn-ml">Submit review</button>
    </form>
@endif
@endsection
