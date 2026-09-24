@extends('layouts.admin')
@section('title', $user->name)
@section('content')
<p><a href="{{ route('admin.users.index') }}">All customers</a></p>
<h1 class="section-title">{{ $user->name }}</h1>
<p class="muted">{{ $user->email }} · {{ $user->phone }} · {{ $user->role }}</p>
<p>{{ $user->address }}</p>
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
        <strong>{{ $order->order_number }}</strong> · {{ $order->farmer->stall_name ?? '' }} · ${{ number_format($order->total_amount, 2) }} · {{ $order->status }}
    </a>
@empty
    <p class="muted">This customer has no orders yet. Their account can still be updated without deleting history.</p>
@endforelse
@endif
@endsection