@php $product = $product ?? null; @endphp
<div class="row g-3">
    <div class="col-md-8"><label class="form-label">Name</label><input class="form-control" name="name" value="{{ old('name', $product->name ?? '') }}" required></div>
    <div class="col-md-4"><label class="form-label">Price</label><input type="number" step="0.01" class="form-control" name="price" value="{{ old('price', $product->price ?? '') }}" required></div>
    <div class="col-12"><label class="form-label">Description</label><textarea class="form-control" name="description" rows="3">{{ old('description', $product->description ?? '') }}</textarea></div>
    <div class="col-md-4">
        <label class="form-label">Unit</label>
        <select name="unit" class="form-select" required>
            @foreach(['kg','gram','dozen','bunch','litre','piece','pack'] as $u)
                <option value="{{ $u }}" @selected(old('unit', $product->unit ?? 'kg')==$u)>{{ $u }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4"><label class="form-label">Stock</label><input type="number" class="form-control" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity ?? 0) }}" required></div>
    <div class="col-md-4"><label class="form-label">Weekly template</label><input type="number" class="form-control" name="weekly_stock_template" value="{{ old('weekly_stock_template', $product->weekly_stock_template ?? '') }}"></div>
    <div class="col-md-6">
        <label class="form-label">Category</label>
        <select name="category_id" class="form-select" required>
            @foreach($categories as $c)<option value="{{ $c->id }}" @selected(old('category_id', $product->category_id ?? '')==$c->id)>{{ $c->name }}</option>@endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Market</label>
        <select name="market_id" class="form-select" required>
            @foreach($markets as $m)<option value="{{ $m->id }}" @selected(old('market_id', $product->market_id ?? '')==$m->id)>{{ $m->name }}</option>@endforeach
        </select>
    </div>
    <div class="col-md-6"><label class="form-label">Image</label><input type="file" name="image" class="form-control" accept="image/*"></div>
    <div class="col-md-6 d-flex align-items-end gap-3">
        <label class="form-check"><input class="form-check-input" type="checkbox" name="is_available" value="1" @checked(old('is_available', $product->is_available ?? true))> Available</label>
        <label class="form-check"><input class="form-check-input" type="checkbox" name="is_sold_out" value="1" @checked(old('is_sold_out', $product->is_sold_out ?? false))> Sold out</label>
        <label class="form-check"><input class="form-check-input" type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured ?? false))> Featured</label>
    </div>
</div>
