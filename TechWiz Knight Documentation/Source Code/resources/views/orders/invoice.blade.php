<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order slip {{ $order->order_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Segoe UI, sans-serif; color: #0e3b2e; }
        .slip { max-width: 720px; margin: 0 auto; }
        .slip-id { font-size: 1.35rem; font-weight: 800; letter-spacing: .02em; }
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body class="p-4">
<div class="slip">
    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
        <div>
            <div class="text-uppercase small text-muted fw-bold">MarketLink · Order slip</div>
            <div class="slip-id">{{ $order->order_number }}</div>
            <div class="mt-1">Pickup {{ $order->pickup_date->format('D, M j, Y') }} · {{ $order->pickup_slot }}</div>
        </div>
        <div class="text-end small">
            <div><strong>Status:</strong> {{ str_replace('_', ' ', $order->status) }}</div>
            <div><strong>Pay at stall:</strong> {{ money($order->total_amount) }}</div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="border rounded-3 p-3 h-100">
                <div class="small text-muted fw-bold">Customer</div>
                <div class="fw-semibold">{{ $order->buyerName() }}</div>
                @if($order->buyerPhone())<div>{{ $order->buyerPhone() }}</div>@endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="border rounded-3 p-3 h-100">
                <div class="small text-muted fw-bold">Farmer / stall</div>
                <div class="fw-semibold">{{ $order->farmer->stall_name ?? '—' }}</div>
                <div>{{ $order->market->name ?? '' }}</div>
                @if($order->farmerPhone())<div>{{ $order->farmerPhone() }}</div>@endif
            </div>
        </div>
    </div>

    <table class="table table-sm align-middle">
        <thead>
        <tr><th>Item</th><th>Qty</th><th>Price</th><th class="text-end">Subtotal</th></tr>
        </thead>
        <tbody>
        @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ money($item->unit_price) }}</td>
                <td class="text-end">{{ money($item->subtotal) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <h3 class="h5">Pay in person: {{ money($order->total_amount) }}</h3>
    @if($order->customer_note)<p class="mb-1"><strong>Customer note:</strong> {{ $order->customer_note }}</p>@endif
    @if($order->farmer_notes)<p><strong>Farmer note:</strong> {{ $order->farmer_notes }}</p>@endif

    <p class="small text-muted mt-3 mb-3">
        Show this slip (or Order ID <strong>{{ $order->order_number }}</strong>) when you contact the farmer or pick up.
    </p>

    <button class="btn btn-dark no-print" type="button" onclick="window.print()">Print slip</button>
</div>
</body>
</html>
