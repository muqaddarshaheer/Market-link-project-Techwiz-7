@extends('layouts.farmer')
@section('title', 'Crop Calculator')

@section('content')
<link rel="stylesheet" href="{{ asset('css/crop-calculator.css') }}?v={{ @filemtime(public_path('css/crop-calculator.css')) }}">

@php
    $fixedLand = [
        'kanal' => 'Kanal',
        'acre' => 'Acre',
        'marla_272' => 'Marla',
        'bigha_punjab' => 'Bigha',
    ];
    $fixedProd = [
        'kg' => 'Kg',
        'maund' => 'Maund',
        'ton' => 'Ton',
        'crate' => 'Crate',
    ];
    $fixedPrice = [
        'per_kg' => 'Per Kg',
        'per_maund' => 'Per Maund',
        'per_ton' => 'Per Ton',
    ];
@endphp

<div class="scc-app" id="sccApp"
     data-calc-url="{{ $calculateUrl }}"
     data-csrf="{{ csrf_token() }}"
     data-insights='@json($cropInsights)'
     data-step="1">

    <header class="scc-hero">
        <div class="scc-hero-text">
            <p class="scc-kicker" data-i18n="calc.badge">Crop Profit Calculator</p>
            <h1 class="scc-title" data-i18n="calc.titleEasy">4 easy steps — find your profit</h1>
            <p class="scc-lead" data-i18n="calc.leadEasy">Tap big buttons. No dropdowns. Live estimate as you go.</p>
        </div>
        <div class="scc-hero-photo">
            <img src="{{ asset('images/farmers/banner-farmer.jpg') }}" alt="Farmer" width="160" height="160" loading="eager">
        </div>
    </header>

    <div class="scc-live" id="sccLive" aria-live="polite">
        <div class="scc-live-main">
            <span class="scc-live-kicker" data-i18n="calc.liveKicker">Live estimate</span>
            <strong class="scc-live-amt" id="sccLiveAmt">—</strong>
            <span class="scc-live-sub" id="sccLiveSub" data-i18n="calc.liveNeedMore">Fill steps to see profit</span>
        </div>
        <div class="scc-live-pills" id="sccLivePills">
            <span class="scc-pill" id="sccPillCrop">🌱</span>
            <span class="scc-pill" id="sccPillLand">—</span>
            <span class="scc-pill" id="sccPillCost">—</span>
        </div>
    </div>

    <div class="scc-progress" role="navigation" aria-label="Steps">
        <div class="scc-progress-bar" id="sccProgressBar"></div>
        <div class="scc-progress-steps">
            <button type="button" class="scc-dot is-on" data-step-dot="1" data-goto="1"><span>1</span><small data-i18n="calc.dotCrop">Crop</small></button>
            <button type="button" class="scc-dot" data-step-dot="2" data-goto="2"><span>2</span><small data-i18n="calc.dotLand">Land</small></button>
            <button type="button" class="scc-dot" data-step-dot="3" data-goto="3"><span>3</span><small data-i18n="calc.dotCost">Cost</small></button>
            <button type="button" class="scc-dot" data-step-dot="4" data-goto="4"><span>4</span><small data-i18n="calc.dotSale">Sale</small></button>
        </div>
    </div>

    <div class="scc-layout">
        <form id="sccForm" class="scc-form" novalidate>
            {{-- STEP 1: Crop --}}
            <section class="scc-panel is-active" data-step-panel="1">
                <p class="scc-ask" data-i18n="calc.step1">Which crop?</p>
                <p class="scc-hint" data-i18n="calc.step1Hint2">Tap fasal — then Next. Double-tap jumps to land.</p>
                <div class="scc-search-wrap">
                    <i class="bi bi-search" aria-hidden="true"></i>
                    <input type="search" class="scc-search" id="sccCropSearch" placeholder="Search tomato / ٹماٹر…" data-ph-en="Search tomato / ٹماٹر…" data-ph-ur="فصل تلاش کریں…" autocomplete="off">
                </div>
                <div class="scc-crop-grid" id="sccCropGrid" role="listbox" aria-label="Crops">
                    @foreach($crops as $key => $crop)
                        <button type="button"
                                class="scc-crop-chip{{ $loop->first ? ' is-selected' : '' }}"
                                data-crop="{{ $key }}"
                                data-en="{{ strtolower($crop['en'] ?? $key) }}"
                                data-ur="{{ $crop['ur'] ?? '' }}"
                                role="option"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                            <span class="scc-crop-emoji">{{ $crop['icon'] ?? '🌱' }}</span>
                            <span class="scc-crop-en">{{ $crop['en'] ?? $key }}</span>
                            <span class="scc-crop-ur">{{ $crop['ur'] ?? '' }}</span>
                        </button>
                    @endforeach
                </div>
                <p class="scc-empty-search" id="sccCropEmpty" hidden data-i18n="calc.noCrop">No crop match — try another word.</p>
                <select class="visually-hidden" id="sccCrop" name="crop" required tabindex="-1" aria-hidden="true">
                    @foreach($crops as $key => $crop)
                        <option value="{{ $key }}" @selected($loop->first)>{{ $crop['en'] ?? $key }}</option>
                    @endforeach
                </select>
                <div id="sccCropOtherWrap" class="scc-other" hidden>
                    <label class="scc-label" for="sccCropOther" data-i18n="calc.cropOther">Crop name</label>
                    <input class="scc-input scc-input-lg" id="sccCropOther" name="crop_other" type="text" maxlength="80" placeholder="Apni fasal ka naam" data-ph-en="Your crop name" data-ph-ur="اپنی فصل کا نام">
                </div>
            </section>

            {{-- STEP 2: Land --}}
            <section class="scc-panel" data-step-panel="2" hidden>
                <p class="scc-ask" data-i18n="calc.step2">How much land?</p>
                <p class="scc-hint" data-i18n="calc.step2Hint">Use + / − or tap a number, then pick unit.</p>
                <div class="scc-stepper">
                    <button type="button" class="scc-stepper-btn" data-nudge="sccLand" data-delta="-0.5" aria-label="Decrease">−</button>
                    <div class="scc-big-field scc-big-field-mid">
                        <label class="scc-label" for="sccLand" data-i18n="calc.land">Land amount</label>
                        <input class="scc-input scc-input-lg" id="sccLand" name="land_amount" type="number" min="0.01" step="0.01" value="2" inputmode="decimal" required>
                    </div>
                    <button type="button" class="scc-stepper-btn" data-nudge="sccLand" data-delta="0.5" aria-label="Increase">+</button>
                </div>
                <div class="scc-quick-nums" id="sccQuickLand">
                    <button type="button" data-set="sccLand" data-val="1">1</button>
                    <button type="button" data-set="sccLand" data-val="2" class="is-on">2</button>
                    <button type="button" data-set="sccLand" data-val="4">4</button>
                    <button type="button" data-set="sccLand" data-val="8">8</button>
                </div>
                <p class="scc-label mt-2 mb-1" data-i18n="calc.popularUnits">Pick unit</p>
                <div class="scc-unit-row scc-unit-row-fixed" id="sccLandUnits">
                    @foreach($fixedLand as $key => $label)
                        @if(isset($landUnits[$key]))
                            <button type="button" class="scc-unit-chip{{ $key==='kanal' ? ' is-selected' : '' }}" data-land-unit="{{ $key }}">{{ $label }}</button>
                        @endif
                    @endforeach
                </div>
                <select class="visually-hidden" id="sccLandUnit" name="land_unit" required tabindex="-1" aria-hidden="true">
                    @foreach($fixedLand as $key => $label)
                        @if(isset($landUnits[$key]))
                            <option value="{{ $key }}" @selected($key==='kanal')>{{ $label }}</option>
                        @endif
                    @endforeach
                </select>
            </section>

            {{-- STEP 3: Cost --}}
            <section class="scc-panel" data-step-panel="3" hidden>
                <p class="scc-ask" data-i18n="calc.step3">Total cost (Rs)</p>
                <p class="scc-hint" data-i18n="calc.costHint">Seed + water + labor + transport — one total.</p>
                <div class="scc-stepper">
                    <button type="button" class="scc-stepper-btn" data-nudge="sccBudget" data-delta="-5000" aria-label="Decrease">−</button>
                    <div class="scc-big-field scc-big-field-mid">
                        <label class="scc-label" for="sccBudget" data-i18n="calc.cost">Total estimated cost</label>
                        <div class="scc-money-wrap scc-money-lg">
                            <span class="scc-currency">Rs</span>
                            <input class="scc-input scc-money scc-input-lg" id="sccBudget" name="budget" type="number" min="0" step="1000" value="50000" inputmode="numeric" required>
                        </div>
                    </div>
                    <button type="button" class="scc-stepper-btn" data-nudge="sccBudget" data-delta="5000" aria-label="Increase">+</button>
                </div>
                <div class="scc-quick-money" id="sccQuickMoney">
                    <button type="button" data-money="25000">25k</button>
                    <button type="button" data-money="50000" class="is-on">50k</button>
                    <button type="button" data-money="100000">1 lac</button>
                    <button type="button" data-money="200000">2 lac</button>
                    <button type="button" data-money="500000">5 lac</button>
                </div>
                <input type="hidden" name="seed_qty" value="0">
                <input type="hidden" name="seed_unit" value="kg">
                <input type="hidden" name="seed_unit_cost" value="0">
                <input type="hidden" name="fertilizer" value="0">
                <input type="hidden" name="water" value="0">
                <input type="hidden" name="labor" value="0">
                <input type="hidden" name="transport" value="0">
                <input type="hidden" name="other_cost" id="sccOtherCost" value="50000">
            </section>

            {{-- STEP 4: Sale --}}
            <section class="scc-panel" data-step-panel="4" hidden>
                <p class="scc-ask" data-i18n="calc.step4">Expected sale</p>
                <p class="scc-hint" data-i18n="calc.step4Hint">Harvest + rate — then tap Calculate for your bill.</p>

                <div class="scc-stepper">
                    <button type="button" class="scc-stepper-btn" data-nudge="sccProd" data-delta="-100" aria-label="Decrease">−</button>
                    <div class="scc-big-field scc-big-field-mid">
                        <label class="scc-label" for="sccProd" data-i18n="calc.prod">Expected harvest</label>
                        <input class="scc-input scc-input-lg" id="sccProd" name="prod_qty" type="number" min="0.01" step="1" value="5000" inputmode="decimal" required>
                    </div>
                    <button type="button" class="scc-stepper-btn" data-nudge="sccProd" data-delta="100" aria-label="Increase">+</button>
                </div>
                <div class="scc-quick-nums" id="sccQuickProd">
                    <button type="button" data-set="sccProd" data-val="1000">1,000</button>
                    <button type="button" data-set="sccProd" data-val="5000" class="is-on">5,000</button>
                    <button type="button" data-set="sccProd" data-val="10000">10,000</button>
                </div>
                <div class="scc-unit-row scc-unit-row-fixed" id="sccProdUnits">
                    @foreach($fixedProd as $key => $label)
                        @if(isset($qtyUnits[$key]))
                            <button type="button" class="scc-unit-chip{{ $key==='kg' ? ' is-selected' : '' }}" data-prod-unit="{{ $key }}">{{ $label }}</button>
                        @endif
                    @endforeach
                </div>
                <select class="visually-hidden" id="sccProdUnit" name="prod_unit" required tabindex="-1" aria-hidden="true">
                    @foreach($fixedProd as $key => $label)
                        @if(isset($qtyUnits[$key]))
                            <option value="{{ $key }}" @selected($key==='kg')>{{ $label }}</option>
                        @endif
                    @endforeach
                </select>

                <div class="scc-stepper scc-gap-lg">
                    <button type="button" class="scc-stepper-btn" data-nudge="sccPrice" data-delta="-5" aria-label="Decrease">−</button>
                    <div class="scc-big-field scc-big-field-mid">
                        <label class="scc-label" for="sccPrice" data-i18n="calc.rate">Selling rate (Rs)</label>
                        <div class="scc-money-wrap scc-money-lg">
                            <span class="scc-currency">Rs</span>
                            <input class="scc-input scc-money scc-input-lg" id="sccPrice" name="price" type="number" min="0.01" step="1" value="40" inputmode="decimal" required>
                        </div>
                    </div>
                    <button type="button" class="scc-stepper-btn" data-nudge="sccPrice" data-delta="5" aria-label="Increase">+</button>
                </div>
                <div class="scc-unit-row scc-unit-row-fixed" id="sccPriceUnits">
                    @foreach($fixedPrice as $key => $label)
                        @if(isset($priceUnits[$key]))
                            <button type="button" class="scc-unit-chip{{ $key==='per_kg' ? ' is-selected' : '' }}" data-price-unit="{{ $key }}">{{ $label }}</button>
                        @endif
                    @endforeach
                </div>
                <select class="visually-hidden" id="sccPriceUnit" name="price_unit" required tabindex="-1" aria-hidden="true">
                    @foreach($fixedPrice as $key => $label)
                        @if(isset($priceUnits[$key]))
                            <option value="{{ $key }}" @selected($key==='per_kg')>{{ $label }}</option>
                        @endif
                    @endforeach
                </select>

                <div class="scc-waste">
                    <div class="scc-waste-head">
                        <label class="scc-label mb-0" for="sccWaste" data-i18n="calc.waste">Wastage estimate</label>
                        <strong id="sccWasteLabel">10%</strong>
                    </div>
                    <input class="scc-range" id="sccWaste" name="wastage" type="range" min="0" max="40" value="10">
                    <div class="scc-waste-presets">
                        <button type="button" data-waste="5">5%</button>
                        <button type="button" data-waste="10" class="is-on">10%</button>
                        <button type="button" data-waste="15">15%</button>
                        <button type="button" data-waste="20">20%</button>
                    </div>
                </div>
            </section>

            <div class="scc-nav">
                <button type="button" class="scc-btn scc-btn-ghost" id="sccBack" hidden>
                    <span data-i18n="calc.back">Back</span>
                </button>
                <button type="button" class="scc-btn scc-btn-next" id="sccNext">
                    <span data-i18n="calc.next">Next</span>
                </button>
                <button type="submit" class="scc-btn scc-btn-go" id="sccSubmit" hidden>
                    <span data-i18n="calc.submit">Make my bill</span>
                </button>
            </div>
            <button type="button" class="scc-reset" id="sccReset" data-i18n="calc.reset">Start over</button>

            <div class="scc-errors" id="sccErrors" hidden></div>
        </form>

        <aside class="scc-insights" id="sccInsights" aria-live="polite">
            <div class="scc-insights-head">
                <p class="scc-insights-kicker" data-i18n="calc.insightKicker">About your fasal</p>
                <h2 class="scc-insights-title" id="sccInsightTitle">—</h2>
                <p class="scc-insights-season" id="sccInsightSeason">—</p>
            </div>
            <div class="scc-insights-block">
                <h3 data-i18n="calc.insightTips">Practical tips</h3>
                <ul id="sccInsightTips"></ul>
            </div>
            <div class="scc-insights-watch">
                <h3 data-i18n="calc.insightWatch">Watch this season</h3>
                <p id="sccInsightWatch">—</p>
            </div>
            <p class="scc-insights-note" data-i18n="calc.insightNote">General farm notes — not a disease diagnosis.</p>
        </aside>
    </div>
</div>

{{-- Unique MarketLink Profit Bill --}}
<div class="scc-modal-backdrop" id="sccModal" hidden>
    <div class="scc-modal scc-bill" role="dialog" aria-modal="true" aria-labelledby="sccModalTitle">
        <button type="button" class="scc-modal-close" id="sccModalClose" aria-label="Close">×</button>
        <div id="sccModalBody"></div>
        <div class="scc-bill-actions">
            <button type="button" class="scc-bill-btn scc-bill-btn-primary" id="sccExportBtn"><i class="bi bi-download"></i> <span data-i18n="calc.export">Export</span></button>
            <button type="button" class="scc-bill-btn" id="sccPrintBtn"><i class="bi bi-printer"></i> <span data-i18n="calc.print80">Print 80mm</span></button>
            <button type="button" class="scc-bill-btn" id="sccAgainBtn"><i class="bi bi-arrow-repeat"></i> <span data-i18n="calc.again">Again</span></button>
        </div>
        <div class="scc-bill-wa">
            <label class="scc-label" for="sccWaPhone" data-i18n="calc.wa">WhatsApp number</label>
            <div class="scc-wa-row">
                <input type="tel" id="sccWaPhone" placeholder="03XXXXXXXXX" maxlength="15" inputmode="tel">
                <button type="button" class="scc-bill-btn scc-bill-btn-wa" id="sccWaBtn"><i class="bi bi-whatsapp"></i> <span data-i18n="calc.send">Send</span></button>
            </div>
            <p class="scc-note mb-0 mt-2" data-i18n="calc.waHint">Enter Pakistan number — message opens in WhatsApp.</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/crop-calculator.js') }}?v={{ @filemtime(public_path('js/crop-calculator.js')) }}"></script>
@endpush
