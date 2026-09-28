@extends('layouts.farmer')
@section('title', $order->order_number)
@section('content')
<div class="panel-head mb-3">
    <div>
        <p class="panel-kicker mb-1">Order</p>
        <h1 class="section-title mb-0">{{ $order->order_number }}</h1>
        <p class="muted mb-0">{{ $order->buyerName() }} · {{ $order->pickup_date->format('M j, Y') }} {{ $order->pickup_slot }} · {{ $order->market->name ?? '' }}</p>
    </div>
    <div class="d-flex flex-wrap gap-2 align-items-center">
        @include('partials.order-status-badge', ['status' => $order->status])
        <a class="btn btn-outline-ml btn-sm" href="{{ route('farmer.orders.index') }}"><i class="bi bi-arrow-left"></i> All orders</a>
        <a class="btn btn-outline-ml btn-sm" href="{{ route('farmer.orders.slip', $order) }}" target="_blank" rel="noopener"><i class="bi bi-receipt"></i> Order slip</a>
    </div>
</div>

<div class="card-ml panel-card p-3 mb-3">
    <ul class="mb-2">@foreach($order->items as $item)<li>{{ $item->product_name }} × {{ $item->quantity }} — {{ money($item->subtotal) }}</li>@endforeach</ul>
    <strong>Total {{ money($order->total_amount) }}</strong>
    @if($order->customer_note)<div class="small mt-2"><strong>Customer note:</strong> {{ $order->customer_note }}</div>@endif
    @if($order->farmer_notes)<div class="small"><strong>Your note:</strong> {{ $order->farmer_notes }}</div>@endif

    @include('partials.contact-actions', [
        'phone' => $order->buyerPhone(),
        'label' => 'Call / WhatsApp customer',
        'prefill' => "Assalam o Alaikum! MarketLink Order ".$order->order_number." — total ".money($order->total_amount)." — pickup ".$order->pickup_date->format('M j')." ".$order->pickup_slot.". ",
        'class' => 'mt-3',
    ])

    @if($order->status === 'placed')
        <form method="POST" action="{{ route('farmer.orders.accept', $order) }}" class="d-flex gap-2 mt-3">@csrf
            <input class="form-control" name="farmer_notes" placeholder="Optional note">
            <button class="btn btn-ml" type="submit">Accept</button>
        </form>
        <form method="POST" action="{{ route('farmer.orders.decline', $order) }}" class="d-flex gap-2 mt-2">@csrf
            <input class="form-control" name="farmer_notes" placeholder="Reason">
            <button class="btn btn-outline-danger" type="submit">Decline</button>
        </form>
    @elseif($order->status === 'accepted')
        <form method="POST" action="{{ route('farmer.orders.ready', $order) }}" class="mt-3">@csrf
            <button class="btn btn-ml" type="submit">Mark ready</button>
        </form>
    @endif
    @if(in_array($order->status, ['accepted','ready_for_pickup'], true))
        <form method="POST" action="{{ route('farmer.orders.complete', $order) }}" class="mt-2">@csrf
            <button class="btn btn-outline-ml" type="submit">Mark completed</button>
        </form>
    @endif
</div>

@include('partials.order-chat', [
    'order' => $order,
    'postRoute' => route('farmer.orders.chat', $order),
    'slipRoute' => route('farmer.orders.slip', $order),
    'peerLabel' => $order->buyerName(),
])
@endsection
