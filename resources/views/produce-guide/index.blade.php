@extends('layouts.app')
@section('title', 'HarvestWise')

@section('content')
<link rel="stylesheet" href="{{ asset('css/produce-guide.css') }}?v={{ @filemtime(public_path('css/produce-guide.css')) }}">

<section class="pg-banner" aria-label="HarvestWise">
    <div class="pg-banner-media" aria-hidden="true">
        <img src="{{ asset('images/farmers/banner-farmer.jpg') }}" alt="" width="1200" height="882" fetchpriority="high">
    </div>
    <div class="pg-banner-shade"></div>
    <div class="container pg-banner-content">
        <p class="pg-kicker">MarketLink · HarvestWise</p>
        <h1>Know your fruit &amp; veg — benefits and cautions</h1>
        <p class="pg-lead">Tap any item for a quick popup. Switch EN / اردو inside. These are general food notes, not medical advice.</p>
        <div class="pg-banner-actions">
            <button type="button" class="pg-chip" id="pgSurprise">
                <i class="bi bi-shuffle"></i> Surprise me
            </button>
            <span class="pg-count" id="pgCount" aria-live="polite"></span>
        </div>
    </div>
</section>

<section class="container pg-section">
    <div class="pg-toolbar" id="pgToolbar">
        <div class="pg-filters" role="tablist" aria-label="Filter produce">
            <button type="button" class="pg-filter is-on" data-filter="all" aria-pressed="true">
                All <span class="pg-badge" data-badge="all">0</span>
            </button>
            <button type="button" class="pg-filter" data-filter="fruit" aria-pressed="false">
                Fruits <span class="pg-badge" data-badge="fruit">0</span>
            </button>
            <button type="button" class="pg-filter" data-filter="vegetable" aria-pressed="false">
                Vegetables <span class="pg-badge" data-badge="vegetable">0</span>
            </button>
        </div>
        <label class="pg-search-wrap">
            <i class="bi bi-search" aria-hidden="true"></i>
            <input type="search" id="pgSearch" placeholder="Search apple, tomato, انار…" autocomplete="off" enterkeyhint="search">
            <button type="button" class="pg-search-clear" id="pgSearchClear" hidden aria-label="Clear search">×</button>
        </label>
    </div>

    <p class="pg-empty" id="pgEmpty" hidden>No match. Try another name or clear the search.</p>

    <h2 class="pg-h" data-group="fruit">Fruits</h2>
    <div class="pg-grid" id="pgFruits">
        @foreach($fruits as $item)
            <button type="button" class="pg-card" data-type="fruit"
                    data-key="{{ $item['key'] }}"
                    data-en="{{ $item['en'] }}"
                    data-ur="{{ $item['ur'] }}">
                <span class="pg-icon" aria-hidden="true">{{ $item['icon'] }}</span>
                <span class="pg-name">{{ $item['en'] }}</span>
                <span class="pg-name-ur">{{ $item['ur'] }}</span>
                <span class="pg-tap">View</span>
            </button>
        @endforeach
    </div>

    <h2 class="pg-h" data-group="vegetable">Vegetables</h2>
    <div class="pg-grid" id="pgVeggies">
        @foreach($vegetables as $item)
            <button type="button" class="pg-card" data-type="vegetable"
                    data-key="{{ $item['key'] }}"
                    data-en="{{ $item['en'] }}"
                    data-ur="{{ $item['ur'] }}">
                <span class="pg-icon" aria-hidden="true">{{ $item['icon'] }}</span>
                <span class="pg-name">{{ $item['en'] }}</span>
                <span class="pg-name-ur">{{ $item['ur'] }}</span>
                <span class="pg-tap">View</span>
            </button>
        @endforeach
    </div>
</section>

<div class="pg-modal-backdrop" id="pgModal" hidden>
    <div class="pg-modal" role="dialog" aria-modal="true" aria-labelledby="pgModalTitle">
        <div class="pg-modal-handle" aria-hidden="true"></div>
        <header class="pg-modal-bar">
            <div class="pg-lang" role="group" aria-label="Language">
                <button type="button" class="pg-lang-btn is-on" id="pgLangEn" data-lang="en">EN</button>
                <button type="button" class="pg-lang-btn" id="pgLangUr" data-lang="ur">اردو</button>
            </div>
            <div class="pg-modal-bar-right">
                <button type="button" class="pg-nav-btn" id="pgPrev" aria-label="Previous item"><i class="bi bi-chevron-left"></i></button>
                <button type="button" class="pg-nav-btn" id="pgNext" aria-label="Next item"><i class="bi bi-chevron-right"></i></button>
                <button type="button" class="pg-modal-close" id="pgModalClose" aria-label="Close">×</button>
            </div>
        </header>

        <div class="pg-modal-hero">
            <span class="pg-modal-icon" id="pgModalIcon"></span>
            <div class="pg-modal-titles">
                <h2 id="pgModalTitle"></h2>
                <p class="pg-modal-sub" id="pgModalSub"></p>
                <span class="pg-type-pill" id="pgModalType"></span>
            </div>
        </div>

        <div class="pg-modal-tabs" role="tablist">
            <button type="button" class="pg-tab is-on" data-tab="overview" role="tab" aria-selected="true">Overview</button>
            <button type="button" class="pg-tab" data-tab="benefits" role="tab" aria-selected="false">Benefits</button>
            <button type="button" class="pg-tab" data-tab="cautions" role="tab" aria-selected="false">Cautions</button>
        </div>

        <div class="pg-panel is-on" data-panel="overview">
            <div class="pg-modal-body-block">
                <h3 id="pgLabelBody"><i class="bi bi-heart-pulse"></i> <span></span></h3>
                <p id="pgModalBody"></p>
            </div>
            <div class="pg-cols pg-cols-preview">
                <div class="pg-col pg-good">
                    <h3 id="pgLabelBenefits"><i class="bi bi-check-circle-fill"></i> <span></span></h3>
                    <ul id="pgModalBenefits"></ul>
                </div>
                <div class="pg-col pg-care">
                    <h3 id="pgLabelCautions"><i class="bi bi-exclamation-triangle-fill"></i> <span></span></h3>
                    <ul id="pgModalCautions"></ul>
                </div>
            </div>
        </div>

        <div class="pg-panel" data-panel="benefits" hidden>
            <div class="pg-col pg-good pg-col-solo">
                <h3 id="pgLabelBenefits2"><i class="bi bi-check-circle-fill"></i> <span></span></h3>
                <ul id="pgModalBenefits2"></ul>
            </div>
        </div>

        <div class="pg-panel" data-panel="cautions" hidden>
            <div class="pg-col pg-care pg-col-solo">
                <h3 id="pgLabelCautions2"><i class="bi bi-exclamation-triangle-fill"></i> <span></span></h3>
                <ul id="pgModalCautions2"></ul>
            </div>
        </div>

        <p class="pg-disclaimer" id="pgDisclaimer"></p>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.PG_ITEMS = @json($items);
</script>
<script src="{{ asset('js/produce-guide.js') }}?v={{ @filemtime(public_path('js/produce-guide.js')) }}"></script>
@endpush
