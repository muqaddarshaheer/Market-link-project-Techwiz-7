@php
    $data = $data ?? [];
    $w = $data['water'] ?? [];
    $s = $data['soil'] ?? [];
    $t = $data['temperature'] ?? [];
    $g = $data['growth'] ?? [];
    $p = $data['problems'] ?? [];
@endphp
<div class="row g-2 py-2">
    <div class="col-md-6"><label class="form-label small">Water amount</label><input class="form-control form-control-sm" name="{{ $prefix }}water_amount" value="{{ $w['amount'] ?? '' }}"></div>
    <div class="col-md-6"><label class="form-label small">Water frequency</label><input class="form-control form-control-sm" name="{{ $prefix }}water_frequency" value="{{ $w['frequency'] ?? '' }}"></div>
    <div class="col-md-6"><label class="form-label small">Best watering time</label><input class="form-control form-control-sm" name="{{ $prefix }}water_best_time" value="{{ $w['best_time'] ?? '' }}"></div>
    <div class="col-md-6"><label class="form-label small">Overwatering warning</label><input class="form-control form-control-sm" name="{{ $prefix }}water_warning" value="{{ $w['warning'] ?? '' }}"></div>
    <div class="col-md-6"><label class="form-label small">Soil type</label><input class="form-control form-control-sm" name="{{ $prefix }}soil_type" value="{{ $s['type'] ?? '' }}"></div>
    <div class="col-md-6"><label class="form-label small">Soil requirements</label><input class="form-control form-control-sm" name="{{ $prefix }}soil_requirements" value="{{ $s['requirements'] ?? '' }}"></div>
    <div class="col-md-6"><label class="form-label small">Temperature range</label><input class="form-control form-control-sm" name="{{ $prefix }}temp_range" value="{{ $t['range'] ?? '' }}"></div>
    <div class="col-md-6"><label class="form-label small">Heat/cold warning</label><input class="form-control form-control-sm" name="{{ $prefix }}temp_warning" value="{{ $t['warning'] ?? '' }}"></div>
    <div class="col-md-4"><label class="form-label small">Growing period</label><input class="form-control form-control-sm" name="{{ $prefix }}growth_period" value="{{ $g['period'] ?? '' }}"></div>
    <div class="col-md-4"><label class="form-label small">Harvest period</label><input class="form-control form-control-sm" name="{{ $prefix }}growth_harvest" value="{{ $g['harvest'] ?? '' }}"></div>
    <div class="col-md-4"><label class="form-label small">Care</label><input class="form-control form-control-sm" name="{{ $prefix }}growth_care" value="{{ $g['care'] ?? '' }}"></div>
    <div class="col-md-4"><label class="form-label small">Pests</label><input class="form-control form-control-sm" name="{{ $prefix }}problems_pests" value="{{ $p['pests'] ?? '' }}"></div>
    <div class="col-md-4"><label class="form-label small">Diseases</label><input class="form-control form-control-sm" name="{{ $prefix }}problems_diseases" value="{{ $p['diseases'] ?? '' }}"></div>
    <div class="col-md-4"><label class="form-label small">Prevention</label><input class="form-control form-control-sm" name="{{ $prefix }}problems_prevention" value="{{ $p['prevention'] ?? '' }}"></div>
</div>
