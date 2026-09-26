@extends('layouts.farmer')
@section('title', 'Products')

@section('content')
<div class="fp-page">
    <div class="panel-head mb-3">
        <div>
            <p class="panel-kicker mb-1" data-i18n="products.kicker">Stall</p>
            <h1 class="section-title mb-0" data-i18n="products.title">Products</h1>
            <p class="fp-lead" data-i18n="products.lead">Add and manage your stall items easily.</p>
        </div>
        <div class="panel-actions">
            <form class="d-inline" method="POST" action="{{ route('farmer.products.template.save') }}">@csrf
                <button class="btn btn-outline-ml btn-sm" type="submit" data-i18n="products.templateSave">Save weekly template</button>
            </form>
            <form class="d-inline" method="POST" action="{{ route('farmer.products.template.apply') }}">@csrf
                <button class="btn btn-outline-ml btn-sm" type="submit" data-i18n="products.templateApply">Apply template</button>
            </form>
        </div>
    </div>

    @if($farmer->isApproved() && $markets->isNotEmpty())
        <form method="POST" action="{{ route('farmer.products.store') }}" enctype="multipart/form-data" class="fp-add-card">
            @csrf
            <h2 data-i18n="products.addTitle">Add new product</h2>
            <div class="fp-grid">
                <div>
                    <label class="fp-label" data-i18n="products.name">Product name</label>
                    <input class="form-control" name="name" required data-i18n-placeholder="products.name" placeholder="Product name">
                </div>
                <div>
                    <label class="fp-label" data-i18n="products.category">Category</label>
                    <select class="form-select" name="category_id" required>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="fp-label" data-i18n="products.market">Market</label>
                    <select class="form-select" name="market_id" required>
                        @foreach($markets as $m)
                            <option value="{{ $m->id }}">{{ $m->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="fp-label" data-i18n="products.price">Price (Rs)</label>
                    <input class="form-control" name="price" type="number" step="0.01" min="0" required placeholder="0">
                </div>
                <div>
                    <label class="fp-label" data-i18n="products.unit">Unit</label>
                    <select class="form-select" name="unit">
                        @foreach(['kg','gram','dozen','bunch','litre','piece','pack'] as $u)
                            <option>{{ $u }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="fp-label" data-i18n="products.quality">Quality</label>
                    <select class="form-select" name="quality">
                        @foreach(['premium'=>'products.premium','fresh'=>'products.fresh','standard'=>'products.standard'] as $val=>$key)
                            <option value="{{ $val }}" data-i18n="{{ $key }}">{{ $val === 'premium' ? 'Premium' : ($val === 'fresh' ? 'Fresh' : 'Standard') }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="fp-label" data-i18n="products.stock">Stock</label>
                    <input class="form-control" name="stock_quantity" type="number" value="10" min="0" required>
                </div>
                <div>
                    <label class="fp-label" data-i18n="products.image">Photo</label>
                    <input class="form-control" type="file" name="image" accept="image/*">
                </div>
                <div class="fp-span-2">
                    <label class="fp-label" data-i18n="products.desc">Description (optional)</label>
                    <textarea class="form-control" name="description" rows="2" data-i18n-placeholder="products.desc" placeholder="Description (optional)"></textarea>
                </div>
                <div class="fp-span-2">
                    <label class="fp-check">
                        <input type="checkbox" name="is_available" value="1" checked>
                        <span data-i18n="products.available">Available for sale</span>
                    </label>
                </div>
            </div>
            <button class="btn btn-ml fp-add-btn" type="submit">
                <i class="bi bi-plus-circle"></i> <span data-i18n="products.addBtn">Add product</span>
            </button>
        </form>
    @else
        <div class="alert alert-info">
            <span data-i18n="products.needMarket">Join at least one market on your profile, and wait for approval, before listing products.</span>
        </div>
    @endif

    <h2 class="fp-list-title" data-i18n="products.listTitle">Your products</h2>

    @if($products->isEmpty())
        <div class="fp-empty" data-i18n="products.empty">No products yet. Add your first item above.</div>
    @else
        <div class="fp-list">
            @foreach($products as $product)
                @php
                    $img = $product->image ? \App\Support\ImageStore::url($product->image) : null;
                @endphp
                <article class="fp-item">
                    @if($img)
                        <img class="fp-thumb" src="{{ $img }}" alt="">
                    @else
                        <div class="fp-thumb fp-thumb-empty"><i class="bi bi-basket2"></i></div>
                    @endif
                    <div>
                        <h3 class="fp-item-name">{{ $product->name }}</h3>
                        <p class="fp-item-meta">
                            {{ $product->market->name ?? '—' }} · {{ money($product->price) }}/{{ $product->unit }}
                            · {{ $product->views_count }} <span data-i18n="products.views">views</span>
                            · {{ $product->favorites_count }} <span data-i18n="products.saves">saves</span>
                        </p>
                        <form method="POST" action="{{ route('farmer.products.update', $product) }}" class="fp-item-controls" enctype="multipart/form-data">
                            @csrf @method('PUT')
                            <input type="hidden" name="name" value="{{ $product->name }}">
                            <input type="hidden" name="category_id" value="{{ $product->category_id }}">
                            <input type="hidden" name="market_id" value="{{ $product->market_id }}">
                            <input type="hidden" name="price" value="{{ $product->price }}">
                            <input type="hidden" name="unit" value="{{ $product->unit }}">
                            <div>
                                <label class="fp-label" data-i18n="products.stock">Stock</label>
                                <input class="form-control form-control-sm" name="stock_quantity" value="{{ $product->stock_quantity }}">
                            </div>
                            <label class="fp-check">
                                <input type="checkbox" name="is_available" value="1" @checked($product->is_available)>
                                <span data-i18n="products.live">Live</span>
                            </label>
                            <label class="fp-check">
                                <input type="checkbox" name="is_sold_out" value="1" @checked($product->is_sold_out)>
                                <span data-i18n="products.soldout">Sold out</span>
                            </label>
                            <button class="btn btn-sm btn-ml" type="submit" data-i18n="products.save">Save</button>
                        </form>
                    </div>
                    <div class="fp-item-actions">
                        <form method="POST" action="{{ route('farmer.products.destroy', $product) }}" onsubmit="return confirm('Delete product? / پروڈکٹ حذف کریں؟')">
                            @csrf @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm w-100" type="submit" data-i18n="products.delete">Delete</button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>
        <div class="mt-3">{{ $products->links() }}</div>
    @endif
</div>
@endsection
