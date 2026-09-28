@extends('layouts.app')
@section('title', 'Order '.$order->order_number)
@section('content')
<section class="guest-confirm">
    <header class="guest-confirm-hero">
        <p class="guest-confirm-kicker">Order confirmed</p>
        <h1 class="guest-confirm-title">{{ $order->order_number }}</h1>
        <p class="guest-confirm-lead">
            Save this Order ID. Chat with <strong>{{ $order->farmer->stall_name ?? 'the farmer' }}</strong> below,
            or Call / WhatsApp with your slip ready. Pay at the stall.
        </p>
        <div class="guest-confirm-meta">
            @include('partials.order-status-badge', ['status' => $order->status])
            <span><i class="bi bi-calendar3"></i> {{ $order->pickup_date->format('D, M j') }} · {{ $order->pickup_slot }}</span>
            <span><i class="bi bi-geo-alt"></i> {{ $order->market->name ?? 'Market' }}</span>
            <span><i class="bi bi-cash-coin"></i> {{ money($order->total_amount) }} unpaid</span>
        </div>
        <div class="guest-confirm-actions">
            <a class="btn btn-ml" href="#order-chat"><i class="bi bi-chat-dots"></i> Open chat</a>
            <a class="btn btn-outline-ml" href="{{ route('guest.orders.slip', $order) }}" target="_blank" rel="noopener"><i class="bi bi-receipt"></i> Order slip</a>
        </div>
    </header>

    <div class="guest-confirm-grid">
        <div class="card-ml p-3">
            <h2 class="h6">Your items</h2>
            @foreach($order->items as $item)
                <div class="d-flex justify-content-between gap-2 py-1">
                    <span>{{ $item->product_name }} × {{ $item->quantity }}</span>
                    <span>{{ money($item->subtotal) }}</span>
                </div>
            @endforeach
            <hr>
            <strong class="d-flex justify-content-between"><span>Total</span><span>{{ money($order->total_amount) }}</span></strong>
        </div>
        <div class="card-ml p-3">
            <h2 class="h6">Farmer contact</h2>
            <p class="mb-2">{{ $order->farmer->stall_name ?? 'Stall' }} · {{ $order->market->name ?? '' }}</p>
            @include('partials.contact-actions', [
                'phone' => $order->farmerPhone(),
                'label' => 'Call / WhatsApp farmer',
                'prefill' => 'Assalam o Alaikum! MarketLink Order '.$order->order_number.' (total '.money($order->total_amount).') — pickup '.$order->pickup_date->format('M j').' '.$order->pickup_slot.'. Mere paas order slip hai. ',
            ])
            <p class="small muted mt-2 mb-0">Guest checkout — no account. Keep this tab open to see farmer replies in chat.</p>
        </div>
    </div>

    @include('partials.order-chat', [
        'order' => $order,
        'postRoute' => route('guest.orders.chat', $order),
        'slipRoute' => route('guest.orders.slip', $order),
        'peerLabel' => $order->farmer->stall_name ?? 'the farmer',
        'asGuest' => true,
    ])
</section>
@endsection
