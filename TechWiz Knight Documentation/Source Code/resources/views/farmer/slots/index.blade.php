@extends('layouts.farmer')
@section('title', 'Pickup slots')
@section('content')
<h1 class="section-title" data-i18n="slot.title">Pickup slots</h1>
<form method="POST" action="{{ route('farmer.slots.update') }}" class="card-ml p-4">
    @csrf @method('PUT')
    <label class="form-label" data-i18n="slot.cutoff">Cutoff hours before the slot starts</label>
    <input class="form-control mb-3" type="number" name="cutoff_hours" min="1" max="72" value="{{ $farmer->cutoff_hours }}" required>
    @foreach($farmer->slots() as $i => $slot)
        <input class="form-control mb-2" name="slots[]" value="{{ $slot['label'] }}" required>
    @endforeach
    <input class="form-control mb-2" name="slots[]" data-i18n-placeholder="slot.addPh" placeholder="Add another slot, e.g. 14:00-16:00">
    <button class="btn btn-ml" data-i18n="slot.save">Save slots</button>
</form>
@endsection
