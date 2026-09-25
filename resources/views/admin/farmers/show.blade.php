@extends('layouts.admin')
@section('title', $farmer->stall_name)
@section('content')
<p><a href="{{ route('admin.farmers.index') }}">All farmers</a></p>
<h1 class="section-title">{{ $farmer->stall_name }}</h1>
<p class="muted">{{ $farmer->user->name }} · {{ $farmer->user->email }} · {{ $farmer->user->phone }}</p>
<p>Approval: <strong>{{ $farmer->approval_status }}</strong> · Account: <strong>{{ $farmer->user->status }}</strong></p>
<p>{{ $farmer->business_description }}</p>
<form method="POST" action="{{ route('admin.farmers.update', $farmer) }}" class="card-ml p-3 mb-3">@csrf @method('PUT')
    <h2 class="h6">Farmer details</h2>
    <div class="row g-2">
        <div class="col-md-4"><label class="form-label">Stall</label><input class="form-control" name="stall_name" value="{{ $farmer->stall_name }}" required></div>
        <div class="col-md-4"><label class="form-label">Contact name</label><input class="form-control" name="contact_person" value="{{ $farmer->contact_person }}" required></div>
        <div class="col-md-4"><label class="form-label">Phone</label><input class="form-control" name="phone" value="{{ $farmer->user->phone }}" required></div>
        <div class="col-12"><label class="form-label">Address</label><input class="form-control" name="address" value="{{ $farmer->address }}"></div>
        <div class="col-12"><label class="form-label">About the stall</label><textarea class="form-control" name="business_description">{{ $farmer->business_description }}</textarea></div>
        <div class="col-12 d-flex flex-wrap gap-2">
            @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)
                <label class="small"><input type="checkbox" name="operating_days[]" value="{{ $day }}" @checked(in_array($day, $farmer->operating_days ?? [], true))> {{ $day }}</label>
            @endforeach
        </div>
    </div>
    <button class="btn btn-ml mt-2">Save farmer</button>
</form>
<p class="small muted">Cutoff {{ $farmer->cutoff_hours }} hours</p>
<div class="d-flex gap-2 mb-3 flex-wrap">
    <form method="POST" action="{{ route('admin.farmers.decide', $farmer) }}">@csrf<input type="hidden" name="approval_status" value="approved"><button class="btn btn-ml">Approve</button></form>
    <form method="POST" action="{{ route('admin.farmers.decide', $farmer) }}">@csrf<input type="hidden" name="approval_status" value="rejected"><button class="btn btn-outline-danger">Reject</button></form>
    <form method="POST" action="{{ route('admin.farmers.suspend', $farmer) }}">@csrf<button class="btn btn-outline-ml">Suspend / restore</button></form>
    <form method="POST" action="{{ route('admin.farmers.destroy', $farmer) }}" onsubmit="return confirm('Delete {{ $farmer->stall_name }}? This removes the stall, products, and login.')">
        @csrf @method('DELETE')
        <button class="btn btn-outline-danger" type="submit">Delete farmer</button>
    </form>
</div>
<h2 class="h5">Markets</h2>
<ul>@forelse($farmer->markets as $market)<li>{{ $market->name }} @if($market->pivot->stall_number) · stall {{ $market->pivot->stall_number }} @endif</li>@empty<li class="muted">No markets assigned.</li>@endforelse</ul>
<h2 class="h5">Products ({{ $farmer->products->count() }})</h2>
<ul>@foreach($farmer->products as $product)<li><a href="{{ route('products.show', $product) }}">{{ $product->name }}</a> · {{ money($product->price) }} · stock {{ $product->stock_quantity }}</li>@endforeach</ul>
<h2 class="h5">Orders ({{ $farmer->orders->count() }})</h2>
<ul>@foreach($farmer->orders->take(8) as $order)<li><a href="{{ route('admin.orders.show', $order) }}">{{ $order->order_number }}</a> · {{ $order->status }}</li>@endforeach</ul>
@endsection
