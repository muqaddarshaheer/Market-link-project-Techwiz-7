@extends('layouts.admin')
@section('title', $farmer->stall_name)
@section('content')
<p><a href="{{ route('admin.farmers.index') }}">All farmers</a></p>
<h1 class="section-title">{{ $farmer->stall_name }}</h1>
<p class="muted">{{ $farmer->user->name }} · {{ $farmer->user->email }} · {{ $farmer->user->phone }}</p>
<p>Approval: <strong>{{ $farmer->approval_status }}</strong> · Account: <strong>{{ $farmer->user->status }}</strong></p>
<p>{{ $farmer->business_description }}</p>
<p class="small muted">{{ $farmer->address }} · Cutoff {{ $farmer->cutoff_hours }} hours</p>
<div class="d-flex gap-2 mb-3">
    <form method="POST" action="{{ route('admin.farmers.decide', $farmer) }}">@csrf<input type="hidden" name="approval_status" value="approved"><button class="btn btn-ml">Approve</button></form>
    <form method="POST" action="{{ route('admin.farmers.decide', $farmer) }}">@csrf<input type="hidden" name="approval_status" value="rejected"><button class="btn btn-outline-danger">Reject</button></form>
    <form method="POST" action="{{ route('admin.farmers.suspend', $farmer) }}">@csrf<button class="btn btn-outline-ml">Suspend / restore</button></form>
</div>
<h2 class="h5">Markets</h2>
<ul>@forelse($farmer->markets as $market)<li>{{ $market->name }} @if($market->pivot->stall_number) · stall {{ $market->pivot->stall_number }} @endif</li>@empty<li class="muted">No markets assigned.</li>@endforelse</ul>
<h2 class="h5">Products ({{ $farmer->products->count() }})</h2>
<ul>@foreach($farmer->products as $product)<li><a href="{{ route('products.show', $product) }}">{{ $product->name }}</a> · ${{ number_format($product->price, 2) }} · stock {{ $product->stock_quantity }}</li>@endforeach</ul>
<h2 class="h5">Orders ({{ $farmer->orders->count() }})</h2>
<ul>@foreach($farmer->orders->take(8) as $order)<li><a href="{{ route('admin.orders.show', $order) }}">{{ $order->order_number }}</a> · {{ $order->status }}</li>@endforeach</ul>
@endsection
