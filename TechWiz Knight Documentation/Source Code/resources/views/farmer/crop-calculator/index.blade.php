@extends('layouts.farmer')
@section('title', 'Crop Calculator')

@section('content')
<link rel="stylesheet" href="{{ asset('css/crop-calculator.css') }}?v={{ @filemtime(public_path('css/crop-calculator.css')) }}">

@php
    $landOpts = [
        'kanal' => 'Kanal',
        'acre' => 'Acre',
        'marla_272' => 'Marla',
        'bigha_punjab' => 'Bigha',
    ];
    $prodOpts = [
        'kg' => 'Kg',
        'maund' => 'Maund',
        'ton' => 'Ton',
    ];
    $priceOpts = [
        'per_kg' => 'Per Kg',
        'per_maund' => 'Per Maund',
        'per_ton' => 'Per Ton',
    ];
@endphp

<div class="panel-head mb-4">
    <div>
        <p class="panel-kicker mb-1" data-i18n="calc.badge">Crop Calculator</p>
        <h1 class="section-title mb-1" data-i18n="calc.title">Profit calculator</h1>
        <p class="muted mb-0" data-i18n="calc.lead">Enter numbers → tap Calculate → see profit or loss.</p>
    </div>
    <div class="panel-actions d-flex flex-wrap gap-2">
        <button type="button" class="btn btn-ml btn-sm" id="sccAssistBtn" title="Listen to help">
            <i class="bi bi-soundwave" aria-hidden="true"></i>
            <span data-i18n="calc.assistant">Assistant</span>
        </button>
        <button type="button" class="btn btn-outline-ml btn-sm" id="sccUrduBtn" aria-pressed="false" title="اردو میں دیکھیں">
            <i class="bi bi-translate" aria-hidden="true"></i>
            <span id="sccUrduLabel">اردو</span>
        </button>
    </div>
</div>

<div id="sccApp"
     data-calc-url="{{ $calculateUrl }}"
     data-csrf="{{ csrf_token() }}"
     data-speak-url="{{ route('farmer.weather.speak') }}">
    <p class="scc-assist-hint small muted mb-3" id="sccAssistHint" data-en="Assistant will read how to use this calculator." data-ur="اسسٹنٹ کیلکولیٹر استعمال کرنے کا طریقہ سنائے گا۔">Assistant will read how to use this calculator.</p>
    <form id="sccForm" class="card-ml panel-card p-3 p-md-4 scc-form" novalidate>
        <div class="scc-form-head mb-3">
            <h2 class="h5 mb-1" data-i18n="calc.formTitle">Crop numbers</h2>
            <p class="small muted mb-0" data-i18n="calc.formHint">Fill once — Calculate opens profit / loss.</p>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="sccCrop" data-i18n="calc.crop">Crop</label>
                <select class="form-select" id="sccCrop" name="crop" required>
                    @foreach($crops as $key => $crop)
                        <option value="{{ $key }}" @selected($loop->first)
                            data-en="{{ ($crop['icon'] ?? '').' '.($crop['en'] ?? $key) }}"
                            data-ur="{{ ($crop['icon'] ?? '').' '.($crop['ur'] ?? $crop['en'] ?? $key) }}">
                            {{ ($crop['icon'] ?? '') }} {{ $crop['en'] ?? $key }} @if(!empty($crop['ur'])) / {{ $crop['ur'] }} @endif
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6" id="sccCropOtherWrap" hidden>
                <label class="form-label" for="sccCropOther" data-i18n="calc.cropOther">Crop name</label>
                <input class="form-control" id="sccCropOther" name="crop_other" type="text" maxlength="80"
                       placeholder="Your crop name" data-ph-en="Your crop name" data-ph-ur="اپنی فصل کا نام">
            </div>

            <div class="col-6 col-md-4">
                <label class="form-label" for="sccLand" data-i18n="calc.land">Land</label>
                <input class="form-control" id="sccLand" name="land_amount" type="number" min="0.01" step="0.01" value="2" required>
            </div>
            <div class="col-6 col-md-4">
                <label class="form-label" for="sccLandUnit" data-i18n="calc.landUnit">Land unit</label>
                <select class="form-select" id="sccLandUnit" name="land_unit" required>
                    @foreach($landOpts as $key => $label)
                        @if(isset($landUnits[$key]))
                            @php
                                $landUr = match($key) {
                                    'kanal' => 'کنال',
                                    'acre' => 'ایکڑ',
                                    'marla_272' => 'مرلہ',
                                    'bigha_punjab' => 'بیگھا',
                                    default => $label,
                                };
                            @endphp
                            <option value="{{ $key }}" @selected($key === 'kanal') data-en="{{ $label }}" data-ur="{{ $landUr }}">{{ $label }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="sccBudget" data-i18n="calc.cost">Total cost (Rs)</label>
                <input class="form-control" id="sccBudget" name="budget" type="number" min="0" step="1000" value="50000" required>
            </div>

            <div class="col-6 col-md-4">
                <label class="form-label" for="sccProd" data-i18n="calc.prod">Expected harvest</label>
                <input class="form-control" id="sccProd" name="prod_qty" type="number" min="0.01" step="1" value="5000" required>
            </div>
            <div class="col-6 col-md-4">
                <label class="form-label" for="sccProdUnit" data-i18n="calc.prodUnit">Harvest unit</label>
                <select class="form-select" id="sccProdUnit" name="prod_unit" required>
                    @foreach($prodOpts as $key => $label)
                        @if(isset($qtyUnits[$key]))
                            @php
                                $prodUr = match($key) {
                                    'kg' => 'کلو',
                                    'maund' => 'من',
                                    'ton' => 'ٹن',
                                    default => $label,
                                };
                            @endphp
                            <option value="{{ $key }}" @selected($key === 'kg') data-en="{{ $label }}" data-ur="{{ $prodUr }}">{{ $label }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="sccWaste" data-i18n="calc.waste">Wastage %</label>
                <input class="form-control" id="sccWaste" name="wastage" type="number" min="0" max="50" step="1" value="10">
            </div>

            <div class="col-6 col-md-6">
                <label class="form-label" for="sccPrice" data-i18n="calc.rate">Selling rate (Rs)</label>
                <input class="form-control" id="sccPrice" name="price" type="number" min="0.01" step="1" value="40" required>
            </div>
            <div class="col-6 col-md-6">
                <label class="form-label" for="sccPriceUnit" data-i18n="calc.priceUnit">Rate unit</label>
                <select class="form-select" id="sccPriceUnit" name="price_unit" required>
                    @foreach($priceOpts as $key => $label)
                        @if(isset($priceUnits[$key]))
                            @php
                                $priceUr = match($key) {
                                    'per_kg' => 'فی کلو',
                                    'per_maund' => 'فی من',
                                    'per_ton' => 'فی ٹن',
                                    default => $label,
                                };
                            @endphp
                            <option value="{{ $key }}" @selected($key === 'per_kg') data-en="{{ $label }}" data-ur="{{ $priceUr }}">{{ $label }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>

        <input type="hidden" name="seed_qty" value="0">
        <input type="hidden" name="seed_unit" value="kg">
        <input type="hidden" name="seed_unit_cost" value="0">
        <input type="hidden" name="fertilizer" value="0">
        <input type="hidden" name="water" value="0">
        <input type="hidden" name="labor" value="0">
        <input type="hidden" name="transport" value="0">
        <input type="hidden" name="other_cost" id="sccOtherCost" value="50000">

        <div class="scc-errors alert alert-danger mt-3 mb-0" id="sccErrors" hidden></div>

        <div class="d-flex flex-wrap gap-2 mt-4">
            <button type="submit" class="btn btn-ml" id="sccSubmit">
                <i class="bi bi-calculator"></i> <span data-i18n="calc.submit">Calculate</span>
            </button>
            <button type="reset" class="btn btn-outline-ml" id="sccReset" data-i18n="calc.reset">Reset</button>
        </div>
    </form>
</div>

<div class="scc-modal-backdrop" id="sccModal" hidden>
    <div class="scc-modal" role="dialog" aria-modal="true" aria-labelledby="sccModalTitle">
        <div class="scc-modal-head">
            <h2 id="sccModalTitle" data-i18n="calc.ledger">Result</h2>
            <button type="button" class="scc-modal-close" id="sccModalClose" aria-label="Close">×</button>
        </div>
        <div class="scc-summary-big" id="sccResultHero">
            <span id="sccResultLabel">Profit</span>
            <span class="amt" id="sccResultAmt">Rs. 0</span>
        </div>
        <div class="scc-modal-kv" id="sccResultList"></div>
        <div class="scc-modal-actions">
            <button type="button" class="btn btn-outline-ml" id="sccPrintBtn">
                <i class="bi bi-printer"></i> <span data-i18n="calc.print">Print 80mm</span>
            </button>
            <button type="button" class="btn btn-ml" id="sccModalOk" data-i18n="calc.ok">OK</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/crop-calculator.js') }}?v={{ @filemtime(public_path('js/crop-calculator.js')) }}"></script>
@endpush
