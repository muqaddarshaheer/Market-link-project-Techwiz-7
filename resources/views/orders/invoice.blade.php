<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Invoice {{ $order->order_number }}</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="p-4">
<h1>MarketLink invoice</h1>
<p>{{ $order->order_number }} · Pickup {{ $order->pickup_date->format('M j, Y') }} {{ $order->pickup_slot }}</p>
<p>{{ $order->customer->name }}<br>{{ $order->farmer->stall_name }} at {{ $order->market->name }}</p>
<table class="table"><thead><tr><th>Item</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr></thead>
<tbody>@foreach($order->items as $item)<tr><td>{{ $item->product_name }}</td><td>{{ $item->quantity }}</td><td>${{ number_format($item->unit_price,2) }}</td><td>${{ number_format($item->subtotal,2) }}</td></tr>@endforeach</tbody></table>
<h3>Pay in person: ${{ number_format($order->total_amount, 2) }}</h3>
<button class="btn btn-dark" onclick="window.print()">Print</button>
</body></html>
