@extends('layouts.admin')
@section('title', 'Smart Crops')
@section('content')
<div class="panel-head mb-4">
    <div>
        <p class="panel-kicker mb-1">Catalog</p>
        <h1 class="section-title mb-1">Smart Crop Guide</h1>
        <p class="muted mb-0">Add or edit fruits &amp; vegetables shown to farmers. English + Urdu details power the popup and weather advice.</p>
    </div>
</div>

<form method="POST" action="{{ route('admin.smart-crops.store') }}" class="card-ml panel-card p-3 mb-4">
    @csrf
    <h2 class="h5 mb-3">Add crop</h2>
    <div class="row g-2 mb-2">
        <div class="col-md-3"><label class="form-label">Name (EN)</label><input class="form-control" name="name_en" required placeholder="Tomato"></div>
        <div class="col-md-3"><label class="form-label">Name (UR)</label><input class="form-control" name="name_ur" required placeholder="ٹماٹر"></div>
        <div class="col-md-2"><label class="form-label">Emoji</label><input class="form-control" name="emoji" placeholder="🍅"></div>
        <div class="col-md-2"><label class="form-label">Category</label>
            <select class="form-select" name="category"><option value="vegetable">Vegetable</option><option value="fruit">Fruit</option></select>
        </div>
        <div class="col-md-2"><label class="form-label">Sort</label><input class="form-control" type="number" name="sort_order" value="0"></div>
    </div>
    <div class="row g-2 mb-2">
        <div class="col-md-2"><label class="form-label">Temp min °C</label><input class="form-control" type="number" step="0.1" name="temp_min_c" value="15"></div>
        <div class="col-md-2"><label class="form-label">Temp max °C</label><input class="form-control" type="number" step="0.1" name="temp_max_c" value="32"></div>
        <div class="col-md-2 d-flex align-items-end"><label class="form-check"><input class="form-check-input" type="checkbox" name="sensitive_to_rain" value="1" checked> Rain sensitive</label></div>
        <div class="col-md-2 d-flex align-items-end"><label class="form-check"><input class="form-check-input" type="checkbox" name="is_active" value="1" checked> Active</label></div>
    </div>
    <details class="mb-2">
        <summary class="fw-bold mb-2">English details</summary>
        @include('admin.smart-crops._fields', ['prefix' => 'en_'])
    </details>
    <details class="mb-3">
        <summary class="fw-bold mb-2">Urdu details</summary>
        @include('admin.smart-crops._fields', ['prefix' => 'ur_'])
    </details>
    <button class="btn btn-ml" type="submit">Add crop</button>
</form>

@foreach($crops as $crop)
@php $en = $crop->details_en ?? []; $ur = $crop->details_ur ?? []; @endphp
<div class="admin-row-card card-ml panel-card p-3 mb-3">
    <form method="POST" action="{{ route('admin.smart-crops.update', $crop) }}">
        @csrf @method('PUT')
        <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
            <span style="font-size:1.5rem">{{ $crop->emoji }}</span>
            <strong>{{ $crop->name_en }}</strong>
            <span class="muted">{{ $crop->name_ur }}</span>
            <span class="badge text-bg-light">{{ $crop->category }}</span>
            @unless($crop->is_active)<span class="badge text-bg-secondary">inactive</span>@endunless
            <span class="ms-auto small muted">#{{ $crop->sort_order }} · {{ $crop->slug }}</span>
        </div>
        <div class="row g-2 mb-2">
            <div class="col-md-3"><input class="form-control" name="name_en" value="{{ $crop->name_en }}" required></div>
            <div class="col-md-3"><input class="form-control" name="name_ur" value="{{ $crop->name_ur }}" required></div>
            <div class="col-md-1"><input class="form-control" name="emoji" value="{{ $crop->emoji }}"></div>
            <div class="col-md-2">
                <select class="form-select" name="category">
                    <option value="vegetable" @selected($crop->category==='vegetable')>Vegetable</option>
                    <option value="fruit" @selected($crop->category==='fruit')>Fruit</option>
                </select>
            </div>
            <div class="col-md-1"><input class="form-control" type="number" name="sort_order" value="{{ $crop->sort_order }}"></div>
            <div class="col-md-2"><input class="form-control" type="number" step="0.1" name="temp_min_c" value="{{ $crop->temp_min_c }}" placeholder="min °C"></div>
            <div class="col-md-2"><input class="form-control" type="number" step="0.1" name="temp_max_c" value="{{ $crop->temp_max_c }}" placeholder="max °C"></div>
        </div>
        <div class="d-flex flex-wrap gap-3 mb-2">
            <label class="form-check"><input class="form-check-input" type="checkbox" name="sensitive_to_rain" value="1" @checked($crop->sensitive_to_rain)> Rain sensitive</label>
            <label class="form-check"><input class="form-check-input" type="checkbox" name="is_active" value="1" @checked($crop->is_active)> Active</label>
        </div>
        <details class="mb-2">
            <summary>English details</summary>
            @include('admin.smart-crops._fields', ['prefix' => 'en_', 'data' => $en])
        </details>
        <details class="mb-2">
            <summary>Urdu details</summary>
            @include('admin.smart-crops._fields', ['prefix' => 'ur_', 'data' => $ur])
        </details>
        <button class="btn btn-ml btn-sm" type="submit">Save</button>
    </form>
    <form method="POST" action="{{ route('admin.smart-crops.destroy', $crop) }}" class="mt-2" onsubmit="return confirm('Delete this crop?')">
        @csrf @method('DELETE')
        <button class="btn btn-outline-danger btn-sm" type="submit">Delete</button>
    </form>
</div>
@endforeach
@endsection
