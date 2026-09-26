@extends('layouts.app')
@section('title', 'Guest cart')
@section('content')
<h1 class="section-title">Guest cart</h1>
<p class="muted">No account is created. You pay the farmer at the stall.</p>
@php $total = 0; @endphp
@forelse($products as $product)
    @php
        $qty = (int) ($lines[$product->id] ?? 0);
        $total += $qty * $product->price;
    @endphp
    <article class="card-ml cart-row p-3 mb-2">
        <div class="cart-row-main">
            <div>
                <strong>{{ $product->name }}</strong>
                <div class="small muted">{{ $product->farmer->stall_name }} · {{ money($product->price) }}</div>
            </div>
            <div class="cart-row-sub"><strong>{{ money($qty * $product->price) }}</strong></div>
        </div>
        <div class="cart-row-actions mt-2">
            <div class="d-flex gap-1 align-items-center flex-wrap">
                <form method="POST" action="{{ route('guest.cart.update', $product) }}" data-ajax-cart>@csrf @method('PUT')
                    <button class="btn btn-outline-ml btn-sm" name="quantity" value="{{ max(1, $qty - 1) }}" type="submit">−</button>
                </form>
                <form method="POST" action="{{ route('guest.cart.update', $product) }}" class="d-flex gap-1" data-ajax-cart>@csrf @method('PUT')
                    <input class="form-control cart-qty-input" type="number" name="quantity" value="{{ $qty }}" min="1" max="{{ $product->stock_quantity }}">
                    <button class="btn btn-outline-ml btn-sm" type="submit">Update</button>
                </form>
                <form method="POST" action="{{ route('guest.cart.update', $product) }}" data-ajax-cart>@csrf @method('PUT')
                    <button class="btn btn-outline-ml btn-sm" name="quantity" value="{{ min($product->stock_quantity, $qty + 1) }}" type="submit">+</button>
                </form>
            </div>
            <form method="POST" action="{{ route('guest.cart.remove', $product) }}" data-ajax-cart>@csrf
                <button class="btn btn-link text-danger px-0" type="submit">Remove</button>
            </form>
        </div>
    </article>
@empty
    <div class="empty-state card-ml"><i class="bi bi-bag"></i><p>Your guest cart is empty.</p><a class="btn btn-ml" href="{{ route('products.index') }}">Browse products</a></div>
@endforelse
@if($products->isNotEmpty())
    <div class="cart-checkout-bar card-ml p-3 mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <strong>Total {{ money($total) }}</strong>
        <a class="btn btn-ml" href="{{ route('guest.checkout') }}">Checkout as guest</a>
    </div>
@endif
@endsection
