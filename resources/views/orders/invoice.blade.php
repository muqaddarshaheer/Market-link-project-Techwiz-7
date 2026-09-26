<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Invoice {{ $order->order_number }}</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="p-4">
<h1>MarketLink invoice</h1>
<p>{{ $order->order_number }} · Pickup {{ $order->pickup_date->format('M j, Y') }} {{ $order->pickup_slot }}</p>
<p>{{ $order->customer->name }}<br>{{ $order->farmer->stall_name }} at {{ $order->market->name }}</p>
<table class="table"><thead><tr><th>Item</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr></thead>
<tbody>@foreach($order->items as $item)<tr><td>{{ $item->product_name }}</td><td>{{ $item->quantity }}</td><td>{{ money($item->unit_price) }}</td><td>{{ money($item->subtotal) }}</td></tr>@endforeach</tbody></table>
<h3>Pay in person: {{ money($order->total_amount) }}</h3>
<button class="btn btn-dark" onclick="window.print()">Print</button>
</body></html>
