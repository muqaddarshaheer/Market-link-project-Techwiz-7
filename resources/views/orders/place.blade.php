@extends('layouts.app')
@section('title', 'Place pre-order')
@section('content')
<h1 class="section-title">Pickup details</h1>
@if($cart->items->isEmpty())
    <div class="empty-state card-ml"><p>Add products before checkout.</p></div>
@else
    <div class="row g-4">
        <div class="col-lg-7">
            <form method="POST" action="{{ route('orders.store') }}" class="card-ml p-4">
                @csrf
                <label class="form-label">Pickup date</label>
                <input class="form-control mb-3" type="date" name="pickup_date" required min="{{ now()->toDateString() }}" value="{{ old('pickup_date', now()->addDay()->toDateString()) }}">
                <label class="form-label">Time slot</label>
                <select class="form-select mb-3" name="pickup_slot" required>
                    @foreach($slots as $slot)<option @selected(old('pickup_slot')===$slot)>{{ $slot }}</option>@endforeach
                </select>
                <label class="form-label">Note for the farmer</label>
                <textarea class="form-control mb-3" name="customer_note" rows="3">{{ old('customer_note') }}</textarea>
                <button class="btn btn-ml">Place pre-order</button>
            </form>
        </div>
        <div class="col-lg-5">
            <div class="card-ml p-3">
                <h2 class="h6">Summary</h2>
                @foreach($cart->items as $item)
                    <div class="d-flex justify-content-between"><span>{{ $item->product->name }} × {{ $item->quantity }}</span><span>${{ number_format($item->quantity * $item->product->price, 2) }}</span></div>
                @endforeach
                <hr>
                <strong>Due at pickup: ${{ number_format($cart->total(), 2) }}</strong>
            </div>
        </div>
    </div>
@endif
@endsection
