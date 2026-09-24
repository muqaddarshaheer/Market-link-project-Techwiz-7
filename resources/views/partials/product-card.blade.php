@php use App\Support\ImageStore; @endphp
<div class="card-ml h-100">
    <div class="thumb">
        @if($product->image)
            <img src="{{ ImageStore::url($product->image) }}" alt="{{ $product->name }}">
        @else
            <i class="bi {{ $product->category->icon ?? 'bi-basket' }}"></i>
        @endif
    </div>
    <div class="p-3">
        <div class="d-flex justify-content-between">
            <span class="badge badge-soft">{{ $product->category->name ?? 'Produce' }}</span>
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
            <a class="btn btn-ml btn-sm" href="{{ route('products.show', $product) }}">View</a>
        </div>
    </div>
</div>
