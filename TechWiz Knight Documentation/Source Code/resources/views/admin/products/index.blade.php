@extends('layouts.admin')
@section('title', 'Products')
@section('content')
<div class="desk-head panel-head mb-4">
    <div>
        <p class="panel-kicker mb-1">Catalog</p>
        <h1 class="section-title mb-1">Products</h1>
        <p class="muted mb-0">Price, stock, quality, and availability for every listing.</p>
    </div>
</div>
<form method="GET" class="desk-toolbar mb-3"><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Search products"><button class="btn btn-ml" type="submit">Search</button></form>
@foreach($products as $product)
<div class="admin-row-card card-ml panel-card p-3 mb-3">
    <form method="POST" action="{{ route('admin.products.update', $product) }}">
        @csrf @method('PUT')
        <div class="row g-2 align-items-center">
            <div class="col-md-2"><input class="form-control" name="name" value="{{ $product->name }}"></div>
            <div class="col-md-1"><input class="form-control" name="price" value="{{ $product->price }}"></div>
            <div class="col-md-1"><input class="form-control" name="stock_quantity" type="number" min="0" value="{{ $product->stock_quantity }}"></div>
            <div class="col-md-2"><select class="form-select" name="quality">@foreach(['premium','fresh','standard'] as $q)<option value="{{ $q }}" @selected(($product->quality ?? 'fresh')===$q)>{{ ucfirst($q) }}</option>@endforeach</select></div>
            <div class="col-md-2"><input class="form-control" name="description" value="{{ $product->description }}" placeholder="Description"></div>
            <div class="col-md-2 small muted">{{ $product->farmer->stall_name }}</div>
            <div class="col-md-2 d-flex gap-2 align-items-center flex-wrap">
                <label class="small mb-0"><input type="checkbox" name="is_available" value="1" @checked($product->is_available)> Live</label>
                <label class="small mb-0"><input type="checkbox" name="is_featured" value="1" @checked($product->is_featured)> Featured</label>
                <button class="btn btn-sm btn-ml" type="submit">Save</button>
            </div>
        </div>
    </form>
    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="mt-2" onsubmit="return confirm('Delete {{ $product->name }}?')">
        @csrf @method('DELETE')
        <button class="btn btn-outline-danger btn-sm" type="submit">Delete</button>
    </form>
</div>
@endforeach
{{ $products->links() }}
@endsection
