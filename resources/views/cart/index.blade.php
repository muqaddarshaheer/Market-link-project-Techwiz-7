@extends('layouts.app')
@section('title', 'Cart')
@section('content')
<h1 class="section-title">Cart</h1>
@if($cart->items->isEmpty())
    <div class="empty-state card-ml"><i class="bi bi-bag"></i><p>Your basket is empty.</p><a class="btn btn-ml" href="{{ route('products.index') }}">Browse products</a></div>
@else
    <div class="table-responsive card-ml">
        <table class="table mb-0 align-middle">
            <thead><tr><th>Product</th><th>Stall</th><th>Qty</th><th>Subtotal</th><th></th></tr></thead>
            <tbody>
            @foreach($cart->items as $item)
                <tr>
                    <td>{{ $item->product->name }}<div class="small muted">${{ number_format($item->product->price, 2) }}/{{ $item->product->unit }}</div></td>
                    <td>{{ $item->product->farmer->stall_name }}<div class="small muted">{{ $item->product->market->name }}</div></td>
                    <td>
                        <div class="d-flex gap-1">
                            <form method="POST" action="{{ route('cart.update', $item->product) }}">@csrf @method('PUT')<button class="btn btn-outline-ml btn-sm" name="quantity" value="{{ max(1, $item->quantity - 1) }}">−</button></form>
                            <form method="POST" action="{{ route('cart.update', $item->product) }}" class="d-flex gap-1">@csrf @method('PUT')<input class="form-control" style="width:70px" type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock_quantity }}"><button class="btn btn-outline-ml btn-sm">Update</button></form>
                            <form method="POST" action="{{ route('cart.update', $item->product) }}">@csrf @method('PUT')<button class="btn btn-outline-ml btn-sm" name="quantity" value="{{ min($item->product->stock_quantity, $item->quantity + 1) }}">+</button></form>
                        </div>
                    </td>
                    <td>${{ number_format($item->quantity * $item->product->price, 2) }}</td>
                    <td>
                        <form method="POST" action="{{ route('cart.remove', $item->product) }}">@csrf @method('DELETE')<button class="btn btn-link text-danger">Remove</button></form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-between align-items-center mt-3">
        <strong>Total ${{ number_format($cart->total(), 2) }}</strong>
        <a class="btn btn-ml" href="{{ route('orders.create') }}">Choose pickup</a>
    </div>
    <p class="small muted mt-2">You pay the farmer in person. No delivery.</p>
@endif
@endsection
