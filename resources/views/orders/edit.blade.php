@extends('layouts.app')
@section('title', 'Edit order')
@section('content')
<div class="container py-5">
    <h1 class="display-font h3">Modify {{ $order->order_number }}</h1>
    <p class="text-muted">Changes allowed until {{ $order->cutoff_time?->format('M d, Y g:i A') }}.</p>
    <form method="POST" action="{{ route('customer.orders.update', $order) }}" class="panel col-lg-6">
        @csrf @method('PUT')
        <div class="mb-3">
            <label class="form-label">Pickup date</label>
            <input type="date" name="pickup_date" class="form-control" value="{{ old('pickup_date', $order->pickup_date->toDateString()) }}" min="{{ date('Y-m-d') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Pickup slot</label>
            <select name="pickup_slot" class="form-select" required>
                @foreach(['08:00-10:00','10:00-12:00','12:00-14:00','14:00-16:00','16:00-18:00'] as $slot)
                    <option value="{{ $slot }}" @selected($order->pickup_slot==$slot)>{{ $slot }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Note</label>
            <textarea name="customer_note" class="form-control" rows="3">{{ old('customer_note', $order->customer_note) }}</textarea>
        </div>
        <button class="btn btn-primary">Save changes</button>
    </form>
</div>
@endsection
