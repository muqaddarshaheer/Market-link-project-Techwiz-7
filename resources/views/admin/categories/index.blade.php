@extends('layouts.app')
@section('title', 'Categories')
@section('content')
<div class="container py-4">
    <h1 class="h3 display-font mb-3">Categories</h1>
    <form method="POST" action="{{ route('admin.categories.store') }}" class="panel mb-4 row g-2 align-items-end">
        @csrf
        <div class="col-md-3"><label class="form-label">Name</label><input class="form-control" name="name" required></div>
        <div class="col-md-3"><label class="form-label">Icon (bi-...)</label><input class="form-control" name="icon" placeholder="bi-carrot"></div>
        <div class="col-md-4"><label class="form-label">Description</label><input class="form-control" name="description"></div>
        <div class="col-md-2"><button class="btn btn-primary w-100">Add</button></div>
    </form>
    @foreach($categories as $category)
        <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="panel mb-2 row g-2 align-items-center">
            @csrf @method('PUT')
            <div class="col-md-3"><input class="form-control" name="name" value="{{ $category->name }}"></div>
            <div class="col-md-2"><input class="form-control" name="icon" value="{{ $category->icon }}"></div>
            <div class="col-md-4"><input class="form-control" name="description" value="{{ $category->description }}"></div>
            <div class="col-md-3 text-end">
                <button class="btn btn-sm btn-outline-primary">Save</button>
                <button formaction="{{ route('admin.categories.destroy', $category) }}" formmethod="POST" name="_method" value="DELETE" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')">Delete</button>
            </div>
        </form>
    @endforeach
    {{ $categories->links() }}
</div>
@endsection
