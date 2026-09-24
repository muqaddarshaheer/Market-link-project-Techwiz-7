@extends('layouts.app')
@section('title', 'Edit product')
@section('content')
<div class="container py-5">
    <h1 class="display-font h3 mb-3">Edit {{ $product->name }}</h1>
    <form method="POST" action="{{ route('farmer.products.update', $product) }}" enctype="multipart/form-data" class="panel col-lg-8">
        @csrf @method('PUT')
        @include('farmer.products._form')
        <button class="btn btn-primary">Save</button>
    </form>
</div>
@endsection
