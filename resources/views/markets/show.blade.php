@extends('layouts.app')
@section('title', $market->name)
@section('content')
<div class="row g-4">
    <div class="col-lg-7">
        <h1 class="section-title">{{ $market->name }}</h1>
        <p class="muted">{{ $market->address }}, {{ $market->city }}</p>
        <p>Open {{ implode(', ', $market->operating_days ?? []) }} · {{ $market->hoursLabel() }}</p>
        @auth
            @if(auth()->user()->isCustomer())
                <form method="POST" action="{{ route('customer.favorites.toggle') }}">@csrf
                    <input type="hidden" name="market_id" value="{{ $market->id }}">
                    <button class="btn btn-outline-ml btn-sm">Save market</button>
                </form>
            @endif
        @endauth
        <h2 class="h5 mt-4">Stalls</h2>
        <div class="row g-3">
            @foreach($market->farmers as $farmer)
                <div class="col-md-6">@include('partials.farmer-card', compact('farmer'))</div>
            @endforeach
        </div>
        <h2 class="h5 mt-4">Available now</h2>
        <div class="row g-3">
            @foreach($market->products as $product)
                <div class="col-md-6">@include('partials.product-card', compact('product'))</div>
            @endforeach
        </div>
    </div>
    <div class="col-lg-5">
        @include('partials.map', ['id' => 'market-map', 'points' => [['lat' => $market->latitude, 'lng' => $market->longitude, 'title' => $market->name, 'subtitle' => $market->address]]])
    </div>
</div>
@endsection
