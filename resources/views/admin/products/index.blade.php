@extends('layouts.app')
@section('title', 'Moderate products')
@section('content')
<div class="container py-4">
    <h1 class="h3 display-font mb-3">Products</h1>
    <form method="GET" class="mb-3"><input class="form-control w-auto d-inline" name="q" value="{{ request('q') }}" placeholder="Search"><button class="btn btn-outline-primary">Go</button></form>
    <div class="panel table-responsive">
        <table class="table mb-0">
            <thead><tr><th>Name</th><th>Farmer</th><th>Price</th><th>Stock</th><th>Available</th><th></th></tr></thead>
            <tbody>
            @foreach($products as $p)
                <tr>
                    <td>{{ $p->name }}</td><td>{{ $p->farmer->stall_name }}</td><td>${{ number_format($p->price,2) }}</td><td>{{ $p->stock_quantity }}</td>
                    <td>{{ $p->is_available ? 'yes' : 'no' }}</td>
                    <td>
                        <form class="d-inline" method="POST" action="{{ route('admin.products.toggle', $p) }}">@csrf<button class="btn btn-sm btn-outline-secondary">Toggle</button></form>
                        <form class="d-inline" method="POST" action="{{ route('admin.products.destroy', $p) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove?')">Remove</button></form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {{ $products->links() }}
</div>
@endsection
