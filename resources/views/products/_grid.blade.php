<div id="productGrid" class="product-grid-wrap" data-count="{{ $products->total() }}">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <p class="mb-0 small muted" id="productResultCount">{{ $products->total() }} produce listed</p>
        <div class="product-sort-inline d-lg-none">
            <span class="small muted">Swipe filters above · cards load live</span>
        </div>
    </div>
    <div class="row g-3 product-grid-row">
        @forelse($products as $product)
            <div class="col-6 col-md-6 col-xl-4 shop-reveal">@include('partials.product-card', compact('product'))</div>
        @empty
            <div class="col-12">
                <div class="empty-state"><i class="bi bi-basket"></i><p>No products match those filters.</p></div>
            </div>
        @endforelse
    </div>
    <div class="mt-3 product-pagination">{{ $products->links() }}</div>
</div>
