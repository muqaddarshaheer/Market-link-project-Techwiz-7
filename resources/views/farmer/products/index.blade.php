@extends('layouts.farmer')
@section('title', 'Products')
@section('content')
<div class="d-flex justify-content-between flex-wrap gap-2">
    <h1 class="section-title">Products</h1>
    <div>
        <form class="d-inline" method="POST" action="{{ route('farmer.products.template.save') }}">@csrf<button class="btn btn-outline-ml btn-sm">Save weekly template</button></form>
        <form class="d-inline" method="POST" action="{{ route('farmer.products.template.apply') }}">@csrf<button class="btn btn-outline-ml btn-sm">Apply template</button></form>
    </div>
</div>
@if($farmer->isApproved() && $markets->isNotEmpty())
<form method="POST" action="{{ route('farmer.products.store') }}" enctype="multipart/form-data" class="card-ml p-3 mb-4">
    @csrf
    <div class="row g-2">
        <div class="col-md-4"><input class="form-control" name="name" placeholder="Name" required></div>
        <div class="col-md-4"><select class="form-select" name="category_id" required>@foreach($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
        <div class="col-md-4"><select class="form-select" name="market_id" required>@foreach($markets as $m)<option value="{{ $m->id }}">{{ $m->name }}</option>@endforeach</select></div>
        <div class="col-md-3"><input class="form-control" name="price" type="number" step="0.01" placeholder="Price" required></div>
        <div class="col-md-3"><select class="form-select" name="unit">@foreach(['kg','gram','dozen','bunch','litre','piece','pack'] as $u)<option>{{ $u }}</option>@endforeach</select></div>
        <div class="col-md-3"><select class="form-select" name="quality">@foreach(['premium'=>'Premium','fresh'=>'Fresh','standard'=>'Standard'] as $val=>$label)<option value="{{ $val }}">{{ $label }}</option>@endforeach</select></div>
        <div class="col-md-2"><input class="form-control" name="stock_quantity" type="number" value="10" required></div>
        <div class="col-md-4"><input class="form-control" type="file" name="image" accept="image/*"></div>
        <div class="col-12"><textarea class="form-control" name="description" placeholder="Description"></textarea></div>
        <div class="col-12"><label><input type="checkbox" name="is_available" value="1" checked> Available</label></div>
    </div>
    <button class="btn btn-ml mt-2">Add product</button>
</form>
@else
    <div class="alert alert-info">Join at least one market on your profile, and wait for approval, before listing products.</div>
@endif
<div class="table-responsive card-ml">
<table class="table align-middle mb-0">
<thead><tr><th>Name</th><th>Price</th><th>Stock</th><th>Views</th><th>Saves</th><th></th></tr></thead>
<tbody>
@foreach($products as $product)
<tr>
<td>{{ $product->name }}<div class="small muted">{{ $product->market->name }}</div></td>
<td>{{ money($product->price) }}/{{ $product->unit }}</td>
<td colspan="4">
<form method="POST" action="{{ route('farmer.products.update', $product) }}" class="row g-1 align-items-center" enctype="multipart/form-data">
@csrf @method('PUT')
<input type="hidden" name="name" value="{{ $product->name }}">
<input type="hidden" name="category_id" value="{{ $product->category_id }}">
<input type="hidden" name="market_id" value="{{ $product->market_id }}">
<input type="hidden" name="price" value="{{ $product->price }}">
<input type="hidden" name="unit" value="{{ $product->unit }}">
<div class="col-auto"><input class="form-control form-control-sm" style="width:80px" name="stock_quantity" value="{{ $product->stock_quantity }}"></div>
<div class="col-auto"><label class="small"><input type="checkbox" name="is_available" value="1" @checked($product->is_available)> Live</label></div>
<div class="col-auto"><label class="small"><input type="checkbox" name="is_sold_out" value="1" @checked($product->is_sold_out)> Sold out</label></div>
<div class="col-auto small">{{ $product->views_count }} views · {{ $product->favorites_count }} saves</div>
<div class="col-auto"><button class="btn btn-sm btn-outline-ml">Save</button></div>
</form>
<form method="POST" action="{{ route('farmer.products.destroy', $product) }}" onsubmit="return confirm('Delete product?')">@csrf @method('DELETE')<button class="btn btn-link btn-sm text-danger">Delete</button></form>
</td>
</tr>
@endforeach
</tbody></table></div>
{{ $products->links() }}
@endsection
