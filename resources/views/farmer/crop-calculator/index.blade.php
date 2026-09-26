@extends('layouts.farmer')
@section('title', 'Crop Calculator')

@section('content')
<link rel="stylesheet" href="{{ asset('css/crop-calculator.css') }}?v={{ @filemtime(public_path('css/crop-calculator.css')) }}">

<div class="scc-app" id="sccApp"
     data-calc-url="{{ $calculateUrl }}"
     data-csrf="{{ csrf_token() }}"
     data-insights='@json($cropInsights)'>

    <header class="scc-hero">
        <div class="scc-hero-text">
            <p class="scc-kicker" data-i18n="calc.badge">Crop Profit Calculator</p>
            <h1 class="scc-title" data-i18n="calc.title">Plan your harvest numbers</h1>
            <p class="scc-lead" data-i18n="calc.lead">Land, cost, sale — estimate only. Crop tips update on the side.</p>
        </div>
        <div class="scc-hero-photo">
            <img src="{{ asset('images/farmers/banner-farmer.jpg') }}" alt="Farmer" width="160" height="160" loading="eager">
        </div>
    </header>

    <div class="scc-layout">
        <form id="sccForm" class="scc-form" novalidate>
            <section class="scc-card">
                <div class="scc-step">1</div>
                <div class="scc-card-body">
                    <h2 class="scc-h" data-i18n="calc.step1">Which crop?</h2>
                    <label class="scc-label" for="sccCrop" data-i18n="calc.crop">Crop</label>
                    <select class="scc-input" id="sccCrop" name="crop" required>
                        @foreach($crops as $key => $crop)
                            <option value="{{ $key }}">{{ $crop['icon'] ?? '' }} {{ $crop['ur'] ?? '' }} / {{ $crop['en'] ?? $key }}</option>
                        @endforeach
                    </select>
                    <div id="sccCropOtherWrap" class="scc-other" hidden>
                        <label class="scc-label" for="sccCropOther" data-i18n="calc.cropOther">Crop name</label>
                        <input class="scc-input" id="sccCropOther" name="crop_other" type="text" maxlength="80" placeholder="Apni fasal ka naam">
                    </div>
                </div>
            </section>

            <section class="scc-card">
                <div class="scc-step">2</div>
                <div class="scc-card-body">
                    <h2 class="scc-h" data-i18n="calc.step2">How much land?</h2>
                    <div class="scc-row">
                        <div>
                            <label class="scc-label" for="sccLand" data-i18n="calc.land">Land amount</label>
                            <input class="scc-input" id="sccLand" name="land_amount" type="number" min="0.01" step="0.01" value="2" required>
                        </div>
                        <div>
                            <label class="scc-label" for="sccLandUnit" data-i18n="calc.unit">Unit</label>
                            <select class="scc-input" id="sccLandUnit" name="land_unit" required>
                                @foreach($landUnits as $key => $unit)
                                    <option value="{{ $key }}" @selected($key==='kanal')>{{ $unit['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </section>

            <section class="scc-card">
                <div class="scc-step">3</div>
                <div class="scc-card-body">
                    <h2 class="scc-h" data-i18n="calc.step3">Total cost (Rs)</h2>
                    <p class="scc-note" data-i18n="calc.costHint">Seed + water + labor + transport — one total.</p>
                    <label class="scc-label" for="sccBudget" data-i18n="calc.cost">Total estimated cost</label>
                    <div class="scc-money-wrap">
                        <span class="scc-currency">Rs</span>
                        <input class="scc-input scc-money" id="sccBudget" name="budget" type="number" min="0" step="1" value="50000" required>
                    </div>
                    <input type="hidden" name="seed_qty" value="0">
                    <input type="hidden" name="seed_unit" value="kg">
                    <input type="hidden" name="seed_unit_cost" value="0">
                    <input type="hidden" name="fertilizer" value="0">
                    <input type="hidden" name="water" value="0">
                    <input type="hidden" name="labor" value="0">
                    <input type="hidden" name="transport" value="0">
                    <input type="hidden" name="other_cost" id="sccOtherCost" value="50000">
                </div>
            </section>

            <section class="scc-card">
                <div class="scc-step">4</div>
                <div class="scc-card-body">
                    <h2 class="scc-h" data-i18n="calc.step4">Expected sale</h2>
                    <div class="scc-row">
                        <div>
                            <label class="scc-label" for="sccProd" data-i18n="calc.prod">Expected harvest</label>
                            <input class="scc-input" id="sccProd" name="prod_qty" type="number" min="0.01" step="0.01" value="5000" required>
                        </div>
                        <div>
                            <label class="scc-label" for="sccProdUnit" data-i18n="calc.unit">Unit</label>
                            <select class="scc-input" id="sccProdUnit" name="prod_unit" required>
                                @foreach($qtyUnits as $key => $unit)
                                    @if(in_array($key, ['kg','maund','ton','crate','plant','other'], true))
                                        <option value="{{ $key }}" @selected($key==='kg')>{{ $unit['label'] }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="scc-row scc-gap">
                        <div>
                            <label class="scc-label" for="sccPrice" data-i18n="calc.rate">Selling rate (Rs)</label>
                            <input class="scc-input" id="sccPrice" name="price" type="number" min="0.01" step="0.01" value="40" required>
                        </div>
                        <div>
                            <label class="scc-label" for="sccPriceUnit" data-i18n="calc.unit">Unit</label>
                            <select class="scc-input" id="sccPriceUnit" name="price_unit" required>
                                @foreach($priceUnits as $key => $unit)
                                    <option value="{{ $key }}" @selected($key==='per_kg')>{{ $unit['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="scc-waste">
                        <div class="scc-waste-head">
                            <label class="scc-label mb-0" for="sccWaste" data-i18n="calc.waste">Wastage estimate</label>
                            <strong id="sccWasteLabel">10%</strong>
                        </div>
                        <input class="scc-range" id="sccWaste" name="wastage" type="range" min="0" max="40" value="10">
                    </div>
                </div>
            </section>

            <div class="scc-actions">
                <button type="submit" class="scc-submit" id="sccSubmit">
                    <span data-i18n="calc.submit">Calculate profit</span>
                </button>
            </div>

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

<div class="scc-modal-backdrop" id="sccModal" hidden>
    <div class="scc-modal" role="dialog" aria-modal="true" aria-labelledby="sccModalTitle">
        <div class="scc-modal-head">
            <div>
                <h2 id="sccModalTitle" data-i18n="calc.summary">Summary</h2>
                <div class="small muted" data-i18n="calc.estimateOnly">Estimate only — not a guarantee</div>
            </div>
            <button type="button" class="scc-modal-close" id="sccModalClose" aria-label="Close">×</button>
        </div>
        <div id="sccModalBody"></div>
        <div class="scc-modal-actions">
            <button type="button" class="btn btn-ml" id="sccExportBtn"><i class="bi bi-download"></i> <span data-i18n="calc.export">Export</span></button>
            <button type="button" class="btn btn-outline-ml" id="sccPrintBtn"><i class="bi bi-printer"></i> <span data-i18n="calc.print">Print</span></button>
        </div>
        <div class="mt-2">
            <label class="scc-label" for="sccWaPhone" data-i18n="calc.wa">WhatsApp number</label>
            <div class="scc-wa-row">
                <input type="tel" id="sccWaPhone" placeholder="03XXXXXXXXX" maxlength="15" inputmode="tel">
                <button type="button" class="btn btn-ml" id="sccWaBtn"><i class="bi bi-whatsapp"></i> <span data-i18n="calc.send">Send</span></button>
            </div>
            <p class="scc-note mb-0 mt-2" data-i18n="calc.waHint">Enter Pakistan number — message opens in WhatsApp.</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/crop-calculator.js') }}?v={{ @filemtime(public_path('js/crop-calculator.js')) }}"></script>
<script>
(function () {
  var budget = document.getElementById('sccBudget');
  var other = document.getElementById('sccOtherCost');
  if (!budget || !other) return;
  function sync() { other.value = budget.value || 0; }
  budget.addEventListener('input', sync);
  sync();
})();
</script>
@endpush
