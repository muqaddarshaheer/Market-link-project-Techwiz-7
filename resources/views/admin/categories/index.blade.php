@extends('layouts.admin')
@section('title', 'Categories')
@section('content')
<h1 class="section-title">Categories</h1>
<form method="POST" action="{{ route('admin.categories.store') }}" class="row g-2 mb-4">@csrf
    <div class="col-md-3"><input class="form-control" name="name" placeholder="Name" required></div>
    <div class="col-md-3"><input class="form-control" name="icon" placeholder="bi-basket"></div>
    <div class="col-md-4"><input class="form-control" name="description" placeholder="Description"></div>
    <div class="col-md-2"><button class="btn btn-ml">Add</button></div>
</form>
@foreach($categories as $category)
<form method="POST" action="{{ route('admin.categories.update', $category) }}" class="card-ml p-2 mb-2 row g-2 align-items-center">
    @csrf @method('PUT')
    <div class="col-md-3"><input class="form-control" name="name" value="{{ $category->name }}"></div>
    <div class="col-md-2"><input class="form-control" name="icon" value="{{ $category->icon }}"></div>
    <div class="col-md-4"><input class="form-control" name="description" value="{{ $category->description }}"></div>
    <div class="col-md-1 small">{{ $category->products_count }}</div>
    <div class="col-md-2"><button class="btn btn-outline-ml btn-sm">Save</button></div>
</form>
<form method="POST" action="{{ route('admin.categories.destroy', $category) }}">@csrf @method('DELETE')<button class="btn btn-link btn-sm text-danger">Delete</button></form>
@endforeach
@endsection
