@extends('layouts.farmer')
@section('title', 'Smart Crop Guide')

@section('content')
<link rel="stylesheet" href="{{ asset('css/smart-crop-guide.css') }}?v={{ @filemtime(public_path('css/smart-crop-guide.css')) }}">

<div class="scg-app" id="scgApp"
     data-show-base="{{ url('/farmer/smart-crop-guide') }}"
     data-lang="en">

    <header class="scg-hero">
        <div class="scg-hero-text">
            <p class="scg-kicker" data-i18n="scg.kicker">Smart Crop Guide</p>
            <h1 class="scg-title" data-i18n="scg.title">Pick a crop for live weather advice</h1>
            <p class="scg-lead" data-i18n="scg.lead">Water, soil, pests, and today’s farming action — English or Urdu.</p>
        </div>
        @if($weather)
            <div class="scg-wx-chip" aria-live="polite">
                <div class="scg-wx-icon"><i class="bi {{ $weather['icon'] }}"></i></div>
                <div>
                    <strong class="scg-wx-temp">{{ $weather['temp'] ?? '—' }}°C</strong>
                    <span class="scg-wx-place">{{ $weather['place'] }}</span>
                    <span class="scg-wx-meta">
                        <span class="scg-en">{{ $weather['summary'] }} · Rain {{ $weather['rain_chance'] }}% · Wind {{ $weather['wind'] }} km/h</span>
                        <span class="scg-ur" dir="rtl" hidden>{{ $weather['summary_ur'] ?? $weather['summary'] }} · بارش {{ $weather['rain_chance'] }}% · ہوا {{ $weather['wind'] }}</span>
                    </span>
                </div>
            </div>
        @endif
    </header>

    <form class="scg-filters" method="GET" action="{{ route('farmer.smart-crop-guide') }}">
        <label class="visually-hidden" for="scgSearch" data-i18n="scg.search">Search crops</label>
        <input class="scg-input" id="scgSearch" type="search" name="q" value="{{ $filters['q'] }}" placeholder="Search tomato, ٹماٹر…" data-ph-en="Search tomato, ٹماٹر…" data-ph-ur="فصل تلاش کریں…">
        <select class="scg-input scg-select" name="category" aria-label="Category">
            <option value="" @selected($filters['category']==='') data-i18n="scg.all">All crops</option>
            <option value="vegetable" @selected($filters['category']==='vegetable') data-i18n="scg.veg">Vegetables</option>
            <option value="fruit" @selected($filters['category']==='fruit') data-i18n="scg.fruit">Fruits</option>
        </select>
        <button class="btn btn-ml scg-filter-btn" type="submit" data-i18n="scg.filter">Filter</button>
    </form>

    <div class="scg-grid" id="scgGrid">
        @forelse($crops as $crop)
            <button type="button"
                    class="scg-card"
                    data-slug="{{ $crop->slug }}"
                    data-name-en="{{ $crop->name_en }}"
                    data-name-ur="{{ $crop->name_ur }}">
                <span class="scg-card-emoji" aria-hidden="true">{{ $crop->emoji }}</span>
                <span class="scg-card-name">
                    <span class="scg-en">{{ $crop->name_en }}</span>
                    <span class="scg-ur" dir="rtl" hidden>{{ $crop->name_ur }}</span>
                </span>
                <span class="scg-card-sub">
                    <span class="scg-en">{{ $crop->name_ur }}</span>
                    <span class="scg-ur" dir="rtl" hidden>{{ $crop->name_en }}</span>
                </span>
                <span class="scg-card-cat">{{ $crop->category === 'fruit' ? 'Fruit' : 'Vegetable' }}</span>
            </button>
        @empty
            <p class="scg-empty" data-i18n="scg.empty">No crops found. Ask admin to add more in Smart Crops.</p>
        @endforelse
    </div>
</div>

{{-- Modal --}}
<div class="scg-modal" id="scgModal" hidden>
    <div class="scg-modal-backdrop" data-scg-close></div>
    <div class="scg-modal-panel scg-sheet" role="dialog" aria-modal="true" aria-labelledby="scgModalTitle">
        <div class="scg-modal-top">
            <button type="button" class="scg-close" data-scg-close aria-label="Close"><i class="bi bi-x-lg"></i></button>
            <div class="scg-lang-toggle" role="group" aria-label="Language">
                <button type="button" class="scg-lang-btn is-active" data-scg-lang="en">EN</button>
                <button type="button" class="scg-lang-btn" data-scg-lang="ur">اردو</button>
            </div>
        </div>
        <div class="scg-modal-body" id="scgModalBody">
            <div class="scg-loading" id="scgLoading">
                <div class="scg-spinner"></div>
                <p data-i18n="scg.loading">Loading crop & weather…</p>
            </div>
            <div id="scgContent" hidden></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/smart-crop-guide.js') }}?v={{ @filemtime(public_path('js/smart-crop-guide.js')) }}" defer></script>
@endpush
