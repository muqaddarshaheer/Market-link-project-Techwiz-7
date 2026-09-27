@extends('layouts.farmer')
@section('title', 'My Land')
@section('content')
<div class="panel-head mb-4">
    <div>
        <p class="panel-kicker mb-1" data-i18n="lands.kicker">Field book</p>
        <h1 class="section-title mb-1" data-i18n="lands.title">My Land</h1>
        <p class="muted mb-0" data-i18n="lands.lead">See which plot has which crop — stage, harvest window, and today’s care tip.</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<div class="card-ml panel-card p-3 p-md-4 mb-4">
    <h2 class="h5 mb-3" data-i18n="lands.add">Add land / plot</h2>
    <form method="POST" action="{{ route('farmer.lands.store') }}" class="row g-3">
        @csrf
        <div class="col-md-4">
            <label class="form-label" for="name" data-i18n="lands.name">Plot name</label>
            <input class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="e.g. Front field / پشتہ والا" required maxlength="120">
        </div>
        <div class="col-md-2">
            <label class="form-label" for="area_amount" data-i18n="lands.area">Area</label>
            <input class="form-control" id="area_amount" name="area_amount" type="number" step="0.01" min="0.01" value="{{ old('area_amount') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label" for="area_unit" data-i18n="lands.unit">Unit</label>
            <select class="form-select" id="area_unit" name="area_unit">
                @foreach($units as $u)
                    <option value="{{ $u }}" @selected(old('area_unit', 'kanal') === $u)>{{ ucfirst($u) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label" for="soil_type" data-i18n="lands.soil">Soil</label>
            <select class="form-select" id="soil_type" name="soil_type">
                <option value="">—</option>
                @foreach($soils as $s)
                    <option value="{{ $s }}" @selected(old('soil_type') === $s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label" for="crop_key" data-i18n="lands.crop">Crop planted</label>
            <select class="form-select" id="crop_key" name="crop_key">
                <option value="">Empty / none</option>
                @foreach($crops as $key => $meta)
                    @continue($key === 'other')
                    <option value="{{ $key }}" @selected(old('crop_key') === $key)>{{ $meta['en'] ?? $key }} ({{ $meta['ur'] ?? '' }})</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label" for="crop_name" data-i18n="lands.cropCustom">Crop name (optional)</label>
            <input class="form-control" id="crop_name" name="crop_name" value="{{ old('crop_name') }}" maxlength="120" placeholder="If not in list">
        </div>
        <div class="col-md-4">
            <label class="form-label" for="stage" data-i18n="lands.stage">Stage</label>
            <select class="form-select" id="stage" name="stage" required>
                @foreach($stages as $key => $label)
                    <option value="{{ $key }}" @selected(old('stage', 'empty') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label" for="planted_on" data-i18n="lands.planted">Planted on</label>
            <input class="form-control" id="planted_on" name="planted_on" type="date" value="{{ old('planted_on') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label" for="expected_harvest" data-i18n="lands.harvest">Expected harvest</label>
            <input class="form-control" id="expected_harvest" name="expected_harvest" type="date" value="{{ old('expected_harvest') }}">
            <div class="form-text" data-i18n="lands.harvestHint">Leave blank — we suggest from crop + planted date.</div>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="notes" data-i18n="lands.notes">Notes</label>
            <input class="form-control" id="notes" name="notes" value="{{ old('notes') }}" maxlength="1000" placeholder="Irrigation, fertilizer, issues…">
        </div>
        <div class="col-12">
            <button class="btn btn-ml" type="submit"><i class="bi bi-plus-lg"></i> <span data-i18n="lands.save">Save land</span></button>
        </div>
    </form>
</div>

@if($lands->isEmpty())
    <div class="empty-state card-ml panel-card py-5 text-center">
        <i class="bi bi-geo-alt fs-2 d-block mb-2"></i>
        <p class="mb-0" data-i18n="lands.empty">Abhi koi zameen nahi — upar se pehli plot add karein.</p>
    </div>
@else
    <div class="row g-3">
        @foreach($lands as $land)
            @php
                $daysH = $land->daysToHarvest();
                $daysP = $land->daysPlanted();
            @endphp
            <div class="col-lg-6">
                <div class="land-card card-ml panel-card p-3 h-100">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                        <div>
                            <h2 class="h5 mb-0">{{ $land->name }}</h2>
                            <p class="small muted mb-0">{{ $land->areaLabel() }}@if($land->soil_type) · {{ ucfirst($land->soil_type) }} soil @endif</p>
                        </div>
                        <span class="land-stage land-stage-{{ $land->stage }}">{{ $land->stageLabel() }}</span>
                    </div>

                    <div class="land-crop mb-3">
                        @if($land->crop_name)
                            <strong class="d-block">{{ $land->crop_name }}</strong>
                            <span class="small muted">
                                @if($daysP !== null) {{ $daysP }} days in ground @endif
                                @if($daysH !== null)
                                    ·
                                    @if($daysH > 0) harvest in ~{{ $daysH }} days
                                    @elseif($daysH === 0) harvest window today
                                    @else harvest overdue by {{ abs($daysH) }} days
                                    @endif
                                @endif
                            </span>
                        @else
                            <span class="muted" data-i18n="lands.noCrop">No crop — empty plot</span>
                        @endif
                    </div>

                    <div class="land-tip mb-3">
                        <span class="land-tip-label"><i class="bi bi-lightbulb"></i> Care tip</span>
                        <p class="mb-1 small">{{ $land->careTip() }}</p>
                        <p class="mb-0 small muted" dir="rtl">{{ $land->careTipUr() }}</p>
                    </div>

                    <details class="land-edit">
                        <summary class="btn btn-outline-ml btn-sm mb-2">Edit</summary>
                        <form method="POST" action="{{ route('farmer.lands.update', $land) }}" class="row g-2 mt-1">
                            @csrf
                            @method('PUT')
                            <div class="col-6">
                                <input class="form-control form-control-sm" name="name" value="{{ $land->name }}" required maxlength="120">
                            </div>
                            <div class="col-3">
                                <input class="form-control form-control-sm" name="area_amount" type="number" step="0.01" min="0.01" value="{{ $land->area_amount }}">
                            </div>
                            <div class="col-3">
                                <select class="form-select form-select-sm" name="area_unit">
                                    @foreach($units as $u)
                                        <option value="{{ $u }}" @selected($land->area_unit === $u)>{{ ucfirst($u) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <select class="form-select form-select-sm" name="crop_key">
                                    <option value="">Empty / none</option>
                                    @foreach($crops as $key => $meta)
                                        @continue($key === 'other')
                                        <option value="{{ $key }}" @selected($land->crop_key === $key)>{{ $meta['en'] ?? $key }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <input class="form-control form-control-sm" name="crop_name" value="{{ $land->crop_name }}" maxlength="120" placeholder="Crop name">
                            </div>
                            <div class="col-4">
                                <select class="form-select form-select-sm" name="stage" required>
                                    @foreach($stages as $key => $label)
                                        <option value="{{ $key }}" @selected($land->stage === $key)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-4">
                                <input class="form-control form-control-sm" name="planted_on" type="date" value="{{ optional($land->planted_on)->format('Y-m-d') }}">
                            </div>
                            <div class="col-4">
                                <input class="form-control form-control-sm" name="expected_harvest" type="date" value="{{ optional($land->expected_harvest)->format('Y-m-d') }}">
                            </div>
                            <div class="col-6">
                                <select class="form-select form-select-sm" name="soil_type">
                                    <option value="">Soil —</option>
                                    @foreach($soils as $s)
                                        <option value="{{ $s }}" @selected($land->soil_type === $s)>{{ ucfirst($s) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <input class="form-control form-control-sm" name="notes" value="{{ $land->notes }}" maxlength="1000" placeholder="Notes">
                            </div>
                            <div class="col-12 d-flex gap-2">
                                <button class="btn btn-ml btn-sm" type="submit">Update</button>
                            </div>
                        </form>
                        <form method="POST" action="{{ route('farmer.lands.destroy', $land) }}" class="mt-2" onsubmit="return confirm('Delete this land entry?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm" type="submit">Delete</button>
                        </form>
                    </details>
                </div>
            </div>
        @endforeach
    </div>
@endif

<style>
.land-stage { font-size: .75rem; font-weight: 600; padding: .35rem .65rem; border-radius: 999px; background: #eef7f1; color: #1f6b45; white-space: nowrap; }
.land-stage-empty { background: #f1f3f5; color: #495057; }
.land-stage-ready { background: #fff3cd; color: #856404; }
.land-stage-harvested { background: #e7f1ff; color: #0b5ed7; }
.land-tip { border-left: 3px solid var(--ml-green, #1f6b45); padding-left: .75rem; background: linear-gradient(90deg, #f3faf5, transparent); border-radius: 0 8px 8px 0; padding-block: .5rem; }
.land-tip-label { display: inline-flex; align-items: center; gap: .35rem; font-size: .7rem; text-transform: uppercase; letter-spacing: .04em; color: #1f6b45; font-weight: 700; margin-bottom: .25rem; }
.land-edit summary { list-style: none; cursor: pointer; }
.land-edit summary::-webkit-details-marker { display: none; }
[data-theme="dark"] .land-tip { background: linear-gradient(90deg, #18211c, transparent); }
[data-theme="dark"] .land-stage { background: #1c3328; color: #9fd4ad; }
[data-theme="dark"] .land-stage-empty { background: #2a2f34; color: #adb5bd; }
</style>
@endsection
