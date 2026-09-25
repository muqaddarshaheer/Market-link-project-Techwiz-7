@extends('layouts.admin')
@section('title', $user->name)
@section('content')
<p><a href="{{ route('admin.users.index') }}">All customers</a></p>
<h1 class="section-title">{{ $user->name }}</h1>
<p class="muted">{{ $user->email }} · {{ $user->phone }} · {{ $user->role }}</p>
<p>{{ $user->address }}</p>
<form method="POST" action="{{ route('admin.users.pin', $user) }}" class="card-ml p-3 mb-4">@csrf
    <h2 class="h6">Set 4-digit PIN</h2>
    <p class="small muted">The current PIN is never displayed.</p>
    <div class="d-flex gap-2 flex-wrap">
        <input class="form-control" style="max-width:140px" name="pin" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" required placeholder="New PIN">
        <input class="form-control" style="max-width:160px" name="pin_confirmation" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" required placeholder="Confirm PIN">
        <button class="btn btn-ml">Save PIN</button>
    </div>
</form>
@if($user->role === 'customer')
<form method="POST" action="{{ route('admin.users.status', $user) }}" class="d-flex gap-2 mb-4">@csrf
    <select class="form-select" name="status" style="max-width:220px">
        @foreach(['active','inactive','suspended'] as $status)
            <option value="{{ $status }}" @selected($user->status===$status)>{{ ucfirst($status) }}</option>
        @endforeach
    </select>
    <button class="btn btn-ml">Update status</button>
</form>
<h2 class="h5">Order history</h2>
@forelse($user->orders as $order)
    <a class="card-ml p-3 mb-2 d-block text-decoration-none text-reset" href="{{ route('admin.orders.show', $order) }}">
        <strong>{{ $order->order_number }}</strong> · {{ $order->farmer->stall_name ?? '' }} · {{ money($order->total_amount) }} · {{ $order->status }}
    </a>
@empty
    <p class="muted">This customer has no orders yet. Their account can still be updated without deleting history.</p>
@endforelse
@endif
@endsection