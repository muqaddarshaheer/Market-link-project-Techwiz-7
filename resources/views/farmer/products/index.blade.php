@extends('layouts.app')
@section('title', 'My products')
@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between mb-3">
        <h1 class="display-font h3">Products</h1>
        <div class="d-flex gap-2">
            <form method="POST" action="{{ route('farmer.products.copy-stock') }}">@csrf<button class="btn btn-outline-secondary">Copy weekly stock</button></form>
            <a href="{{ route('farmer.products.create') }}" class="btn btn-primary">Add product</a>
        </div>
    </div>
    <div class="row g-4">
        @foreach($products as $product)
            <div class="col-md-4">
                <div class="product-card">
                    <img src="{{ $product->imageUrl() }}" alt="">
                    <div class="p-3">
                        <h5>{{ $product->name }}</h5>
                        <p class="mb-1">${{ number_format($product->price,2) }} · stock {{ $product->stock_quantity }}</p>
                        <a href="{{ route('farmer.products.edit', $product) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form class="d-inline" method="POST" action="{{ route('farmer.products.destroy', $product) }}">@csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    {{ $products->links() }}
</div>
@endsection
