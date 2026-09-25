@extends('layouts.app')
@section('title', 'Guest cart')
@section('content')
<h1 class="section-title">Guest cart</h1>
<p class="muted">No account is created. You pay the farmer at the stall.</p>
@forelse($products as $product)
    <div class="card-ml p-3 mb-2 d-flex justify-content-between align-items-center gap-2">
        <div>
            <strong>{{ $product->name }}</strong>
            <div class="small muted">{{ $product->farmer->stall_name }} · {{ money($product->price) }} × {{ $lines[$product->id] ?? 0 }}</div>
        </div>
        <form method="POST" action="{{ route('guest.cart.remove', $product) }}">@csrf<button class="btn btn-outline-ml btn-sm">Remove</button></form>
    </div>
@empty
    <p class="muted">Your guest cart is empty.</p>
@endforelse
@if($products->isNotEmpty())
    <a class="btn btn-ml" href="{{ route('guest.checkout') }}">Checkout as guest</a>
@endif
@endsection
