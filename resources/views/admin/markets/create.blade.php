@extends('layouts.app')
@section('title', 'Create market')
@section('content')
<div class="container py-4"><h1 class="h3 display-font mb-3">Create market</h1>
<form method="POST" action="{{ route('admin.markets.store') }}" enctype="multipart/form-data" class="panel">@csrf @include('admin.markets._form')<button class="btn btn-primary mt-3">Create</button></form></div>
@endsection
