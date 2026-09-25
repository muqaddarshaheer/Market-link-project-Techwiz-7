@extends('layouts.customer')
@section('title', 'Favorites')
@section('content')
<div class="panel-head mb-4">
    <div>
        <p class="panel-kicker mb-1">Saved</p>
        <h1 class="section-title mb-0">Favorites</h1>
    </div>
    <a class="btn btn-ml btn-sm" href="{{ route('products.index') }}">Browse produce</a>
</div>
@forelse($favorites as $favorite)
    <div class="panel-list-item card-ml panel-card p-3 mb-2 d-flex justify-content-between align-items-center gap-2">
        <div>
            @if($favorite->product)<a class="fw-bold text-decoration-none" href="{{ route('products.show', $favorite->product) }}">{{ $favorite->product->name }}</a>@endif
            @if($favorite->farmer)<div class="small"><a href="{{ route('farmers.show', $favorite->farmer) }}">{{ $favorite->farmer->stall_name }}</a></div>@endif
            @if($favorite->market)<div class="small muted"><a href="{{ route('markets.show', $favorite->market) }}">{{ $favorite->market->name }}</a></div>@endif
        </div>
        <form method="POST" action="{{ route('customer.favorites.toggle') }}">@csrf
            <input type="hidden" name="product_id" value="{{ $favorite->product_id }}">
            <input type="hidden" name="farmer_id" value="{{ $favorite->farmer_id }}">
            <input type="hidden" name="market_id" value="{{ $favorite->market_id }}">
            <button class="btn btn-sm btn-outline-danger" type="submit">Remove</button>
        </form>
    </div>
@empty
    <div class="empty-state card-ml panel-card"><i class="bi bi-heart"></i><p>Save farmers, products, or markets as you browse.</p></div>
@endforelse
{{ $favorites->links() }}
@endsection
