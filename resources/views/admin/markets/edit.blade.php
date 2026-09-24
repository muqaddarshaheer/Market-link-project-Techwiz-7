@extends('layouts.app')
@section('title', 'Edit market')
@section('content')
<div class="container py-4"><h1 class="h3 display-font mb-3">Edit market</h1>
<form method="POST" action="{{ route('admin.markets.update', $market) }}" enctype="multipart/form-data" class="panel">@csrf @method('PUT') @include('admin.markets._form')<button class="btn btn-primary mt-3">Save</button></form></div>
@endsection
