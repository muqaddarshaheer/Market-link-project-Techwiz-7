@extends('layouts.app')
@section('title', 'Guest basket')
@section('content')
@php
    use App\Support\ImageStore;
    $count = 0;
    $total = 0;
    $rows = [];
    foreach ($products as $product) {
        $qty = (int) ($lines[$product->id] ?? 0);
        if ($qty < 1) {
            continue;
        }
        $count += $qty;
        $total += $qty * $product->price;
        $key = ($product->farmer->stall_name ?? 'Stall').' · '.($product->market->name ?? 'Market');
        $rows[$key][] = compact('product', 'qty');
    }
    $stallCount = count($rows);
@endphp

<section class="cart-page">
    <header class="cart-hero">
        <div class="cart-hero-copy">
            <p class="cart-kicker">Guest basket</p>
            <h1 class="cart-title">Your basket</h1>
            @if($count < 1)
                <p class="cart-lead">No account needed. Add produce, then checkout as guest and pay at the stall.</p>
            @else
                <p class="cart-lead">{{ $count }} {{ Str::plural('item', $count) }} · {{ $stallCount }} {{ Str::plural('stall', $stallCount) }} · no account · pay at pickup</p>
            @endif
        </div>
        @if($count > 0)
            <div class="cart-hero-stats" aria-hidden="true">
                <span><i class="bi bi-basket2"></i> {{ $count }}</span>
                <span><i class="bi bi-shop"></i> {{ $stallCount }}</span>
            </div>
        @endif
    </header>

    @if($count < 1)
        <div class="cart-empty">
            <div class="cart-empty-icon" aria-hidden="true"><i class="bi bi-basket"></i></div>
            <h2 class="cart-empty-title">Guest basket is empty</h2>
            <p class="cart-empty-text">Browse the market and add what you need. Checkout stays guest-only — you still pay the farmer in person.</p>
            <a class="btn btn-ml" href="{{ route('products.index') }}">Browse produce</a>
        </div>
    @else
        <div class="cart-layout">
            <div class="cart-main">
                @foreach($rows as $stallLabel => $stallItems)
                    <section class="cart-stall">
                        <header class="cart-stall-head">
                            <i class="bi bi-geo-alt" aria-hidden="true"></i>
                            <div>
                                <h2 class="cart-stall-name">{{ $stallLabel }}</h2>
                                <p class="cart-stall-note">Pickup &amp; pay at this stall</p>
                            </div>
                        </header>
                        <ul class="cart-list">
                            @foreach($stallItems as $row)
                                @php
                                    $product = $row['product'];
                                    $qty = $row['qty'];
                                @endphp
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
                                            <strong class="cart-line-total">{{ money($qty * $product->price) }}</strong>
                                        </div>
                                        <div class="cart-row-actions">
                                            <div class="cart-qty" role="group" aria-label="Quantity">
                                                <form method="POST" action="{{ route('guest.cart.update', $product) }}" data-ajax-cart>@csrf @method('PUT')
                                                    <button class="cart-qty-btn" name="quantity" value="{{ max(1, $qty - 1) }}" type="submit" aria-label="Decrease">−</button>
                                                </form>
                                                <form method="POST" action="{{ route('guest.cart.update', $product) }}" class="cart-qty-form" data-ajax-cart>@csrf @method('PUT')
                                                    <input class="cart-qty-input" type="number" name="quantity" value="{{ $qty }}" min="1" max="{{ $product->stock_quantity }}" aria-label="Quantity" onchange="this.form.requestSubmit()">
                                                </form>
                                                <form method="POST" action="{{ route('guest.cart.update', $product) }}" data-ajax-cart>@csrf @method('PUT')
                                                    <button class="cart-qty-btn" name="quantity" value="{{ min($product->stock_quantity, $qty + 1) }}" type="submit" aria-label="Increase">+</button>
                                                </form>
                                            </div>
                                            <form method="POST" action="{{ route('guest.cart.remove', $product) }}" data-ajax-cart>@csrf
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
                        <div class="cart-summary-total"><dt>Due at stall</dt><dd>{{ money($total) }}</dd></div>
                    </dl>
                    <a class="btn btn-ml w-100 cart-cta" href="{{ route('guest.checkout') }}">Checkout as guest</a>
                    <p class="cart-pay-note"><i class="bi bi-cash-coin" aria-hidden="true"></i> Pay the farmer in Rs. No account · no delivery.</p>
                    <a class="cart-keep" href="{{ route('products.index') }}">Keep browsing</a>
                </div>
            </aside>
        </div>

        <div class="cart-checkout-bar">
            <div>
                <span class="cart-bar-label">Due at stall</span>
                <strong>{{ money($total) }}</strong>
            </div>
            <a class="btn btn-ml" href="{{ route('guest.checkout') }}"><span class="cart-cta-short">Checkout</span><span class="cart-cta-full">Checkout as guest</span></a>
        </div>
    @endif
</section>
@endsection
