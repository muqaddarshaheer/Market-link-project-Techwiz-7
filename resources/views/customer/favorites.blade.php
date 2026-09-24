@extends('layouts.customer')
@section('title', 'Favorites')
@section('content')
<h1 class="section-title">Favorites</h1>
@forelse($favorites as $favorite)
    <div class="card-ml p-3 mb-2 d-flex justify-content-between">
        <div>
            @if($favorite->product)<a href="{{ route('products.show', $favorite->product) }}">{{ $favorite->product->name }}</a>@endif
            @if($favorite->farmer)<a href="{{ route('farmers.show', $favorite->farmer) }}">{{ $favorite->farmer->stall_name }}</a>@endif
            @if($favorite->market)<a href="{{ route('markets.show', $favorite->market) }}">{{ $favorite->market->name }}</a>@endif
        </div>
        <form method="POST" action="{{ route('customer.favorites.toggle') }}">@csrf
            <input type="hidden" name="product_id" value="{{ $favorite->product_id }}">
            <input type="hidden" name="farmer_id" value="{{ $favorite->farmer_id }}">
            <input type="hidden" name="market_id" value="{{ $favorite->market_id }}">
            <button class="btn btn-link text-danger">Remove</button>
        </form>
    </div>
@empty
    <div class="empty-state card-ml"><i class="bi bi-heart"></i><p>Save farmers, products, or markets as you browse.</p></div>
@endforelse
{{ $favorites->links() }}
@endsection
