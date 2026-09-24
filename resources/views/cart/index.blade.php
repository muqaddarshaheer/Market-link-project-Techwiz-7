@extends('layouts.app')
@section('title', 'Cart')
@section('content')
<div class="container py-5">
    <h1 class="display-font mb-4">Your cart</h1>
    @if($cart->items->isEmpty())
        <div class="empty-state panel">Your cart is empty. <a href="{{ route('products.index') }}">Browse produce</a></div>
    @else
        <div class="table-responsive panel">
            <table class="table align-middle mb-0">
                <thead><tr><th>Product</th><th>Price</th><th>Qty</th><th>Subtotal</th><th></th></tr></thead>
                <tbody>
                @foreach($cart->items as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->product->name }}</strong><br>
                            <small class="text-muted">{{ $item->product->farmer->stall_name }} · {{ $item->product->market->name }}</small>
                        </td>
                        <td>${{ number_format($item->product->price, 2) }}</td>
                        <td style="width:140px">
                            <form method="POST" action="{{ route('customer.cart.update', $item) }}" class="d-flex gap-1">
                                @csrf @method('PUT')
                                <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock_quantity }}" class="form-control form-control-sm">
                                <button class="btn btn-sm btn-outline-primary">Update</button>
                            </form>
                        </td>
                        <td>${{ number_format($item->subtotal(), 2) }}</td>
                        <td>
                            <form method="POST" action="{{ route('customer.cart.destroy', $item) }}">@csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">&times;</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-4">
            <form method="POST" action="{{ route('customer.cart.clear') }}">@csrf @method('DELETE')
                <button class="btn btn-outline-secondary" onclick="return confirm('Clear cart?')">Clear cart</button>
            </form>
            <div class="text-end">
                <div class="fs-4 fw-bold">Total: ${{ number_format($cart->totalAmount(), 2) }}</div>
                <a href="{{ route('customer.checkout') }}" class="btn btn-primary btn-lg mt-2">Checkout pre-order</a>
            </div>
        </div>
    @endif
</div>
@endsection
