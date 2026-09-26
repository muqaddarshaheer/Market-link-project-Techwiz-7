@extends('layouts.app')
@section('title', 'Cart')
@section('content')
<h1 class="section-title">Cart</h1>
@if($cart->items->isEmpty())
    <div class="empty-state card-ml"><i class="bi bi-bag"></i><p>Your basket is empty.</p><a class="btn btn-ml" href="{{ route('products.index') }}">Browse products</a></div>
@else
    <div class="cart-list">
        @foreach($cart->items as $item)
            <article class="card-ml cart-row p-3 mb-2">
                <div class="cart-row-main">
                    <div>
                        <strong>{{ $item->product->name }}</strong>
                        <div class="small muted">{{ $item->product->farmer->stall_name }} · {{ $item->product->market->name }}</div>
                        <div class="small muted">{{ money($item->product->price) }}/{{ $item->product->unit }}</div>
                    </div>
                    <div class="cart-row-sub"><strong>{{ money($item->quantity * $item->product->price) }}</strong></div>
                </div>
                <div class="cart-row-actions mt-2">
                    <div class="d-flex gap-1 align-items-center flex-wrap">
                        <form method="POST" action="{{ route('cart.update', $item->product) }}" data-ajax-cart>@csrf @method('PUT')
                            <button class="btn btn-outline-ml btn-sm" name="quantity" value="{{ max(1, $item->quantity - 1) }}" type="submit">−</button>
                        </form>
                        <form method="POST" action="{{ route('cart.update', $item->product) }}" class="d-flex gap-1" data-ajax-cart>@csrf @method('PUT')
                            <input class="form-control cart-qty-input" type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock_quantity }}" aria-label="Quantity">
                            <button class="btn btn-outline-ml btn-sm" type="submit">Update</button>
                        </form>
                        <form method="POST" action="{{ route('cart.update', $item->product) }}" data-ajax-cart>@csrf @method('PUT')
                            <button class="btn btn-outline-ml btn-sm" name="quantity" value="{{ min($item->product->stock_quantity, $item->quantity + 1) }}" type="submit">+</button>
                        </form>
                    </div>
                    <form method="POST" action="{{ route('cart.remove', $item->product) }}" data-ajax-cart>@csrf @method('DELETE')
                        <button class="btn btn-link text-danger px-0" type="submit">Remove</button>
                    </form>
                </div>
            </article>
        @endforeach
    </div>
    <div class="cart-checkout-bar card-ml p-3 mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <strong>Total {{ money($cart->total()) }}</strong>
        <a class="btn btn-ml" href="{{ route('orders.create') }}">Choose pickup</a>
    </div>
    <p class="small muted mt-2">You pay the farmer in person. No delivery.</p>
@endif
@endsection
