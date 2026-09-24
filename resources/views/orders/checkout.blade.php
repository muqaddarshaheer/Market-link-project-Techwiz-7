@extends('layouts.app')
@section('title', 'Checkout')
@section('content')
<div class="container py-5">
    <h1 class="display-font mb-3">Pre-order checkout</h1>
    <p class="text-muted">Pay in person at pickup. No online payment. Orders are split per farmer if needed.</p>

    @foreach($groups as $farmerId => $items)
        <div class="panel mb-3">
            <h5>{{ $items->first()->product->farmer->stall_name }} — {{ $items->first()->product->market->name }}</h5>
            <ul class="mb-0">
                @foreach($items as $item)
                    <li>{{ $item->product->name }} × {{ $item->quantity }} — ${{ number_format($item->subtotal(), 2) }}</li>
                @endforeach
            </ul>
        </div>
    @endforeach

    <form method="POST" action="{{ route('customer.checkout.store') }}" class="panel">
        @csrf
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Pickup date</label>
                <input type="date" name="pickup_date" class="form-control" min="{{ date('Y-m-d') }}" value="{{ old('pickup_date', now()->addDay()->toDateString()) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Pickup slot</label>
                <select name="pickup_slot" class="form-select" required>
                    @foreach(['08:00-10:00','10:00-12:00','12:00-14:00','14:00-16:00','16:00-18:00'] as $slot)
                        <option value="{{ $slot }}" @selected(old('pickup_slot')==$slot)>{{ $slot }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Note for farmers</label>
                <input class="form-control" name="customer_note" value="{{ old('customer_note') }}" maxlength="1000">
            </div>
        </div>
        <button class="btn btn-primary mt-4" type="submit">Place pre-order</button>
    </form>
</div>
@endsection
