<div class="product-card">
    <a href="{{ route('products.show', $product) }}">
        <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}">
    </a>
    <div class="p-3">
        <div class="d-flex justify-content-between align-items-start gap-2">
            <h5 class="mb-1"><a href="{{ route('products.show', $product) }}" class="text-decoration-none text-reset">{{ $product->name }}</a></h5>
            @if(!$product->isInStock())
                <span class="badge bg-secondary">Sold out</span>
            @endif
        </div>
        <p class="small text-muted mb-2">{{ $product->farmer->stall_name ?? 'Farmer' }} · {{ $product->market->name ?? '' }}</p>
        <div class="d-flex justify-content-between align-items-center">
            <strong>${{ number_format($product->price, 2) }} <span class="fw-normal text-muted">/ {{ $product->unit }}</span></strong>
            @auth
                @if(auth()->user()->isCustomer() && $product->isInStock())
                    <form method="POST" action="{{ route('cart.store') }}">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button class="btn btn-sm btn-primary" type="submit"><i class="bi bi-plus"></i></button>
                    </form>
                @endif
            @endauth
        </div>
    </div>
</div>
