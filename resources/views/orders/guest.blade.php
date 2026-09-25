@extends('layouts.app')
@section('title', 'Guest checkout')
@section('content')
<h1 class="section-title">Guest checkout</h1>
<form method="POST" action="{{ route('guest.checkout.store') }}" class="card-ml p-4" id="guestForm">@csrf
    <label class="form-label">Name</label>
    <input class="form-control mb-2" name="name" value="{{ old('name') }}" required>
    <label class="form-label">Phone</label>
    <input class="form-control mb-2" name="phone" value="{{ old('phone') }}" required>
    <label class="form-label">Where should the farmer reach you</label>
    <input class="form-control mb-2" name="address" value="{{ old('address') }}" required>
    <label class="form-label">Pickup date</label>
    <input class="form-control mb-2" type="date" name="pickup_date" value="{{ old('pickup_date') }}" required>
    <label class="form-label">Pickup slot</label>
    <select class="form-select mb-2" name="pickup_slot" required>
        @foreach($slots as $slot)<option @selected(old('pickup_slot')===$slot)>{{ $slot }}</option>@endforeach
    </select>
    <label class="form-label">Note</label>
    <textarea class="form-control mb-3" name="customer_note">{{ old('customer_note') }}</textarea>
    <button class="btn btn-ml" id="guestBtn" type="submit">Place guest pre-order</button>
</form>
<script>
document.getElementById('guestForm').addEventListener('submit', function () {
    const button = document.getElementById('guestBtn');
    button.disabled = true;
    button.textContent = 'Placing order…';
});
</script>
@endsection
