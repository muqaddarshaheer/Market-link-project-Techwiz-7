@extends('layouts.app')
@section('title', 'Moderate products')
@section('content')
<h1 class="section-title">Products</h1>
<form method="GET" class="mb-3"><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Search"></form>
@foreach($products as $product)
<form method="POST" action="{{ route('admin.products.update', $product) }}" class="card-ml p-2 mb-2 row g-2 align-items-center">
    @csrf @method('PUT')
    <div class="col-md-3"><input class="form-control" name="name" value="{{ $product->name }}"></div>
    <div class="col-md-2"><input class="form-control" name="price" value="{{ $product->price }}"></div>
    <div class="col-md-3 small">{{ $product->farmer->stall_name }} · {{ $product->market->name }}</div>
    <div class="col-md-2"><label class="small"><input type="checkbox" name="is_available" value="1" @checked($product->is_available)> Available</label><label class="small"><input type="checkbox" name="is_featured" value="1" @checked($product->is_featured)> Featured</label></div>
    <div class="col-md-2"><button class="btn btn-sm btn-outline-ml">Save</button></div>
</form>
<form method="POST" action="{{ route('admin.products.destroy', $product) }}">@csrf @method('DELETE')<button class="btn btn-link btn-sm text-danger">Delete</button></form>
@endforeach
{{ $products->links() }}
@endsection
