@extends('layouts.admin')
@section('title', 'Categories')
@section('content')
<div class="panel-head mb-4">
    <div>
        <p class="panel-kicker mb-1">Catalog</p>
        <h1 class="section-title mb-1">Categories</h1>
        <p class="muted mb-0">Keep produce groups tidy so shoppers can filter quickly.</p>
    </div>
</div>
<form method="POST" action="{{ route('admin.categories.store') }}" class="card-ml panel-card p-3 mb-4">
    @csrf
    <div class="row g-2 align-items-end">
        <div class="col-md-3"><label class="form-label">Name</label><input class="form-control" name="name" placeholder="Name" required></div>
        <div class="col-md-3"><label class="form-label">Icon</label><input class="form-control" name="icon" placeholder="bi-basket"></div>
        <div class="col-md-4"><label class="form-label">Description</label><input class="form-control" name="description" placeholder="Description"></div>
        <div class="col-md-2"><button class="btn btn-ml w-100" type="submit">Add</button></div>
    </div>
</form>
@foreach($categories as $category)
<div class="admin-row-card card-ml panel-card p-3 mb-3">
    <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="row g-2 align-items-center">
        @csrf @method('PUT')
        <div class="col-md-3"><input class="form-control" name="name" value="{{ $category->name }}"></div>
        <div class="col-md-2"><input class="form-control" name="icon" value="{{ $category->icon }}"></div>
        <div class="col-md-4"><input class="form-control" name="description" value="{{ $category->description }}"></div>
        <div class="col-md-1 small muted">{{ $category->products_count }} items</div>
        <div class="col-md-2 d-flex gap-2">
            <button class="btn btn-ml btn-sm" type="submit">Save</button>
        </div>
    </form>
    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="mt-2">
        @csrf @method('DELETE')
        <button class="btn btn-outline-danger btn-sm" type="submit">Delete</button>
    </form>
</div>
@endforeach
@endsection
