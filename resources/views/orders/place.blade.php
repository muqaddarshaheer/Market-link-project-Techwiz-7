@extends('layouts.app')
@section('title', 'Place pre-order')
@section('content')
@php
    $stalls = $cart->items->map(fn ($item) => $item->product->farmer->stall_name.' · '.$item->product->market->name)->unique()->values();
@endphp
<h1 class="section-title">Pickup details</h1>
@if($cart->items->isEmpty())
    <div class="empty-state card-ml"><p>Add products before checkout.</p></div>
@else
    <div class="row g-4">
        <div class="col-lg-7">
            @if($stalls->count() > 1)
                <div class="alert alert-warning ml-alert mb-3">
                    Your cart covers {{ $stalls->count() }} stalls. One pickup window is applied to each pre-order — pay each farmer in person.
                    <ul class="mb-0 mt-2">@foreach($stalls as $stall)<li>{{ $stall }}</li>@endforeach</ul>
                </div>
            @endif
            <form method="POST" action="{{ route('orders.store') }}" class="card-ml p-4" data-checkout-guard>
                @csrf
                <label class="form-label">Pickup date</label>
                <input class="form-control mb-3" type="date" name="pickup_date" required min="{{ now()->toDateString() }}" value="{{ old('pickup_date', now()->addDay()->toDateString()) }}">
                <label class="form-label">Time slot</label>
                <select class="form-select mb-3" name="pickup_slot" required>
                    @foreach($slots as $slot)<option @selected(old('pickup_slot')===$slot)>{{ $slot }}</option>@endforeach
                </select>
                <label class="form-label">Note for the farmer</label>
                <textarea class="form-control mb-3" name="customer_note" rows="3">{{ old('customer_note') }}</textarea>
                <button class="btn btn-ml" type="submit">Place pre-order</button>
                <p class="small muted mt-2 mb-0">Pay at the stall in Rs. No online payment · no delivery.</p>
            </form>
        </div>
        <div class="col-lg-5">
            <div class="card-ml p-3 checkout-summary">
                <h2 class="h6">Summary</h2>
                @foreach($cart->items as $item)
                    <div class="d-flex justify-content-between gap-2"><span>{{ $item->product->name }} × {{ $item->quantity }}</span><span>{{ money($item->quantity * $item->product->price) }}</span></div>
                @endforeach
                <hr>
                <strong>Due at pickup: {{ money($cart->total()) }}</strong>
            </div>
        </div>
    </div>
@endif
@endsection
