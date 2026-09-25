@php use App\Support\ImageStore; @endphp
<div class="card-ml h-100">
    <div class="thumb">
        <img src="{{ ImageStore::picture($product->image, $product->name) }}" alt="{{ $product->name }}" width="640" height="420" loading="lazy" decoding="async">
        <div class="thumb-label">{{ $product->name }}</div>
    </div>
    <div class="p-3">
        <div class="d-flex justify-content-between">
            <span class="badge badge-soft">{{ $product->category->name ?? 'Produce' }}</span>
            <span class="badge badge-soft">{{ ucfirst($product->quality ?? 'fresh') }}</span>
            @if($product->is_sold_out || $product->stock_quantity < 1)
                <span class="badge text-bg-secondary">Sold out</span>
            @elseif($product->is_available)
                <span class="badge text-bg-success">Available</span>
            @else
                <span class="badge text-bg-warning">Unavailable</span>
            @endif
        </div>
        <h5 class="mt-2 mb-1"><a class="text-decoration-none" href="{{ route('products.show', $product) }}">{{ $product->name }}</a></h5>
        <div class="small muted">{{ $product->farmer->stall_name ?? '' }} · {{ $product->market->name ?? '' }}</div>
        @include('partials.star-rating', ['rating' => $product->rating_avg ?? $product->averageRating()])
        <div class="d-flex justify-content-between align-items-center mt-2">
            <strong>${{ number_format($product->price, 2) }} <span class="small muted">/ {{ $product->unit }}</span></strong>
            <div class="d-flex gap-1">
                @auth
                    @if(auth()->user()->isCustomer())
                        <form method="POST" action="{{ route('customer.favorites.toggle') }}">@csrf<input type="hidden" name="product_id" value="{{ $product->id }}"><button class="btn btn-outline-ml btn-sm" aria-label="Favorite"><i class="bi bi-heart"></i></button></form>
                        @if($product->is_available && ! $product->is_sold_out && $product->stock_quantity > 0)
                            <form method="POST" action="{{ route('cart.add', $product) }}">@csrf<input type="hidden" name="quantity" value="1"><button class="btn btn-ml btn-sm">Add</button></form>
                        @endif
                    @endif
                @endauth
                @guest
                    @if($product->is_available && ! $product->is_sold_out && $product->stock_quantity > 0)
                        <form method="POST" action="{{ route('guest.cart.add', $product) }}">@csrf<input type="hidden" name="quantity" value="1"><button class="btn btn-ml btn-sm">Add</button></form>
                    @endif
                @endguest
                <a class="btn btn-outline-ml btn-sm" href="{{ route('products.show', $product) }}">View</a>
            </div>
        </div>
    </div>
</div>
