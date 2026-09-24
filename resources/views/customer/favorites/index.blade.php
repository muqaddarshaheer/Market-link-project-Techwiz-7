@extends('layouts.app')
@section('title', 'Favorites')
@section('content')
<div class="container py-5">
    <h1 class="display-font mb-4">Favorites</h1>
    <div class="row g-4">
        @forelse($favorites as $fav)
            <div class="col-md-4">
                <div class="panel h-100">
                    @if($fav->product)
                        <h5>{{ $fav->product->name }}</h5>
                        <a href="{{ route('products.show', $fav->product) }}">View product</a>
                    @elseif($fav->farmer)
                        <h5>{{ $fav->farmer->stall_name }}</h5>
                        <a href="{{ route('farmers.show', $fav->farmer) }}">View farmer</a>
                    @elseif($fav->market)
                        <h5>{{ $fav->market->name }}</h5>
                        <a href="{{ route('markets.show', $fav->market) }}">View market</a>
                    @endif
                    <form class="mt-2" method="POST" action="{{ route('customer.favorites.toggle') }}">
                        @csrf
                        @if($fav->product_id)<input type="hidden" name="product_id" value="{{ $fav->product_id }}">@endif
                        @if($fav->farmer_id)<input type="hidden" name="farmer_id" value="{{ $fav->farmer_id }}">@endif
                        @if($fav->market_id)<input type="hidden" name="market_id" value="{{ $fav->market_id }}">@endif
                        <button class="btn btn-sm btn-outline-danger">Remove</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="empty-state">No favorites yet.</div>
        @endforelse
    </div>
    {{ $favorites->links() }}
</div>
@endsection
