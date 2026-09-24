<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $order->order_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>@media print { .no-print { display:none } }</style>
</head>
<body class="p-4">
    <div class="d-flex justify-content-between mb-4">
        <div>
            <h2>MarketLink</h2>
            <p class="mb-0">Pickup invoice (pay in person)</p>
        </div>
        <button class="btn btn-primary no-print" onclick="window.print()">Print</button>
    </div>
    <p><strong>Order:</strong> {{ $order->order_number }}<br>
       <strong>Customer:</strong> {{ $order->customer->name }}<br>
       <strong>Farmer:</strong> {{ $order->farmer->stall_name }}<br>
       <strong>Market:</strong> {{ $order->market->name }}<br>
       <strong>Pickup:</strong> {{ $order->pickup_date->format('Y-m-d') }} {{ $order->pickup_slot }}</p>
    <table class="table table-bordered">
        <thead><tr><th>Item</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr></thead>
        <tbody>
        @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>${{ number_format($item->unit_price, 2) }}</td>
                <td>${{ number_format($item->subtotal, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot><tr><th colspan="3" class="text-end">Total</th><th>${{ number_format($order->total_amount, 2) }}</th></tr></tfoot>
    </table>
</body>
</html>
