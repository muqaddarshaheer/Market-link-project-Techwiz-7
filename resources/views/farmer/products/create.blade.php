@extends('layouts.app')
@section('title', 'Add product')
@section('content')
<div class="container py-5">
    <h1 class="display-font h3 mb-3">Add product</h1>
    <form method="POST" action="{{ route('farmer.products.store') }}" enctype="multipart/form-data" class="panel col-lg-8">
        @csrf
        @include('farmer.products._form')
        <button class="btn btn-primary">Create</button>
    </form>
</div>
@endsection
