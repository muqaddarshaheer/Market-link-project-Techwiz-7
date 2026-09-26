@extends('layouts.app')
@section('title', 'Your basket')
@section('content')
@php
    use App\Support\ImageStore;
    $items = $cart->items;
    $count = $items->sum('quantity');
    $groups = $items->groupBy(fn ($item) => ($item->product->farmer->stall_name ?? 'Stall').' · '.($item->product->market->name ?? 'Market'));
    $stallCount = $groups->count();
@endphp

<section class="cart-page">
    <header class="cart-hero">
        <div class="cart-hero-copy">
            <p class="cart-kicker">MarketLink basket</p>
            <h1 class="cart-title">Your basket</h1>
            @if($items->isEmpty())
                <p class="cart-lead">Nothing here yet — pick fresh produce from a nearby stall.</p>
            @else
                <p class="cart-lead">{{ $count }} {{ Str::plural('item', $count) }} · {{ $stallCount }} {{ Str::plural('stall', $stallCount) }} · pay in person at pickup</p>
            @endif
        </div>
        @unless($items->isEmpty())
            <div class="cart-hero-stats" aria-hidden="true">
                <span><i class="bi bi-basket2"></i> {{ $count }}</span>
                <span><i class="bi bi-shop"></i> {{ $stallCount }}</span>
            </div>
        @endunless
    </header>

    @if($items->isEmpty())
        <div class="cart-empty">
            <div class="cart-empty-icon" aria-hidden="true"><i class="bi bi-basket"></i></div>
            <h2 class="cart-empty-title">Basket is empty</h2>
            <p class="cart-empty-text">Browse the market, add what you need, then choose a pickup window. You pay the farmer at the stall — no delivery.</p>
            <a class="btn btn-ml" href="{{ route('products.index') }}">Browse produce</a>
        </div>
    @else
        <div class="cart-layout">
            <div class="cart-main">
                @foreach($groups as $stallLabel => $stallItems)
                    <section class="cart-stall">
                        <header class="cart-stall-head">
                            <i class="bi bi-geo-alt" aria-hidden="true"></i>
                            <div>
                                <h2 class="cart-stall-name">{{ $stallLabel }}</h2>
                                <p class="cart-stall-note">Pickup &amp; pay at this stall</p>
                            </div>
                        </header>
                        <ul class="cart-list">
                            @foreach($stallItems as $item)
                                @php $product = $item->product; @endphp
                                <li class="cart-row">
                                    <a class="cart-thumb" href="{{ route('products.show', $product) }}">
                                        <img src="{{ ImageStore::picture($product->image, $product->name) }}" alt="" width="96" height="96" loading="lazy" decoding="async">
                                    </a>
                                    <div class="cart-row-body">
                                        <div class="cart-row-top">
                                            <div>
                                                <a class="cart-item-name" href="{{ route('products.show', $product) }}">{{ $product->name }}</a>
                                                <p class="cart-item-meta">{{ money($product->price) }} / {{ $product->unit }}</p>
                                            </div>
                                            <strong class="cart-line-total">{{ money($item->quantity * $product->price) }}</strong>
                                        </div>
                                        <div class="cart-row-actions">
                                            <div class="cart-qty" role="group" aria-label="Quantity">
                                                <form method="POST" action="{{ route('cart.update', $product) }}" data-ajax-cart>@csrf @method('PUT')
                                                    <button class="cart-qty-btn" name="quantity" value="{{ max(1, $item->quantity - 1) }}" type="submit" aria-label="Decrease">−</button>
                                                </form>
                                                <form method="POST" action="{{ route('cart.update', $product) }}" class="cart-qty-form" data-ajax-cart>@csrf @method('PUT')
                                                    <input class="cart-qty-input" type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $product->stock_quantity }}" aria-label="Quantity" onchange="this.form.requestSubmit()">
                                                </form>
                                                <form method="POST" action="{{ route('cart.update', $product) }}" data-ajax-cart>@csrf @method('PUT')
                                                    <button class="cart-qty-btn" name="quantity" value="{{ min($product->stock_quantity, $item->quantity + 1) }}" type="submit" aria-label="Increase">+</button>
                                                </form>
                                            </div>
                                            <form method="POST" action="{{ route('cart.remove', $product) }}" data-ajax-cart>@csrf @method('DELETE')
                                                <button class="cart-remove" type="submit"><i class="bi bi-trash3" aria-hidden="true"></i> Remove</button>
                                            </form>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endforeach
            </div>

            <aside class="cart-aside">
                <div class="cart-summary">
                    <h2 class="cart-summary-title">Pickup total</h2>
                    <dl class="cart-summary-rows">
                        <div><dt>Items</dt><dd>{{ $count }}</dd></div>
                        <div><dt>Stalls</dt><dd>{{ $stallCount }}</dd></div>
                        <div class="cart-summary-total"><dt>Due at stall</dt><dd>{{ money($cart->total()) }}</dd></div>
                    </dl>
                    <a class="btn btn-ml w-100 cart-cta" href="{{ route('orders.create') }}">Choose pickup</a>
                    <p class="cart-pay-note"><i class="bi bi-cash-coin" aria-hidden="true"></i> Pay the farmer in Rs. No online payment · no delivery.</p>
                    <a class="cart-keep" href="{{ route('products.index') }}">Keep browsing</a>
                </div>
            </aside>
        </div>

        <div class="cart-checkout-bar">
            <div>
                <span class="cart-bar-label">Due at stall</span>
                <strong>{{ money($cart->total()) }}</strong>
            </div>
            <a class="btn btn-ml" href="{{ route('orders.create') }}"><span class="cart-cta-short">Pickup</span><span class="cart-cta-full">Choose pickup</span></a>
        </div>
    @endif
</section>
@endsection
