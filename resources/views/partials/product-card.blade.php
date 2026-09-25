@php use App\Support\ImageStore; @endphp
<article class="card-ml product-card h-100">
    <a class="thumb" href="{{ route('products.show', $product) }}">
        <img src="{{ ImageStore::picture($product->image, $product->name) }}" alt="{{ $product->name }}" width="640" height="420" loading="lazy" decoding="async">
        <span class="thumb-label">{{ $product->name }}</span>
    </a>
    <div class="product-card-body">
        <div class="product-card-meta">
            <span class="badge badge-soft">{{ $product->category->name ?? 'Produce' }}</span>
            @if($product->is_sold_out || $product->stock_quantity < 1)
                <span class="badge text-bg-secondary">Sold out</span>
            @elseif($product->is_available)
                <span class="badge text-bg-success">Available</span>
            @else
                <span class="badge text-bg-warning">Unavailable</span>
            @endif
        </div>
        <h3 class="product-card-title">
            <a href="{{ route('products.show', $product) }}">{{ $product->name }}</a>
        </h3>
        <p class="product-card-stall">{{ $product->farmer->stall_name ?? '' }}@if($product->market) · {{ $product->market->name }}@endif</p>
        @include('partials.star-rating', ['rating' => $product->rating_avg ?? $product->averageRating()])
        <div class="product-card-foot">
            <strong>{{ money($product->price) }} <span>/ {{ $product->unit }}</span></strong>
            <div class="product-card-actions">
                @auth
                    @if(auth()->user()->isCustomer())
                        <form method="POST" action="{{ route('customer.favorites.toggle') }}">@csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <button class="btn btn-outline-ml btn-sm" type="submit" aria-label="Favorite"><i class="bi bi-heart"></i></button>
                        </form>
                        @if($product->is_available && ! $product->is_sold_out && $product->stock_quantity > 0)
                            <form method="POST" action="{{ route('cart.add', $product) }}">@csrf
                                <input type="hidden" name="quantity" value="1">
                                <button class="btn btn-ml btn-sm" type="submit">Add</button>
                            </form>
                        @endif
                    @endif
                @endauth
                @guest
                    @if($product->is_available && ! $product->is_sold_out && $product->stock_quantity > 0)
                        <form method="POST" action="{{ route('guest.cart.add', $product) }}">@csrf
                            <input type="hidden" name="quantity" value="1">
                            <button class="btn btn-ml btn-sm" type="submit">Add</button>
                        </form>
                    @endif
                @endguest
                <a class="btn btn-outline-ml btn-sm" href="{{ route('products.show', $product) }}">View</a>
            </div>
        </div>
    </div>
</article>
