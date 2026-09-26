@extends('layouts.farmer')
@section('title', 'Insights')
@section('content')
<h1 class="section-title" data-i18n="ins.title">Sales insights</h1>
<form class="d-flex flex-wrap gap-2 mb-3" method="GET">
    <select class="form-select" name="range" style="max-width:180px">
        <option value="today" @selected(request('range')==='today') data-i18n="ins.today">Today</option>
        <option value="7" @selected(request('range','7')==='7') data-i18n="ins.7">7 days</option>
        <option value="30" @selected(request('range')==='30') data-i18n="ins.30">30 days</option>
    </select>
    <input class="form-control" type="date" name="from" value="{{ request('from') }}" style="max-width:180px">
    <input class="form-control" type="date" name="to" value="{{ request('to') }}" style="max-width:180px">
    <button class="btn btn-ml" data-i18n="ins.apply">Apply</button>
</form>

@php
    $orderTotal = (int) $byDay->sum('total');
    $revenueTotal = (float) $byDay->sum('revenue');
@endphp
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card-ml panel-card p-3">
            <div class="small muted mb-1" data-i18n="ins.ordersRange">Orders (selected range)</div>
            <div class="fs-3 fw-bold text-success">{{ $orderTotal }}</div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card-ml panel-card p-3">
            <div class="small muted mb-1" data-i18n="ins.revenueRange">Revenue (selected range)</div>
            <div class="fs-3 fw-bold">{{ money($revenueTotal) }}</div>
        </div>
    </div>
</div>

@if($byDay->isNotEmpty())
    <div class="card-ml panel-card p-3 mb-4">
        <h2 class="h6 mb-3" data-i18n="ins.dayByDay">Day by day</h2>
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th data-i18n="ins.day">Day</th>
                        <th data-i18n="ins.orders">Orders</th>
                        <th data-i18n="ins.revenue">Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($byDay as $row)
                        <tr>
                            <td>{{ $row->day }}</td>
                            <td>{{ (int) $row->total }}</td>
                            <td>{{ money((float) $row->revenue) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@else
    <div class="alert alert-light border mb-4" data-i18n="ins.emptySales">No sales data in this range yet.</div>
@endif

<h2 class="h5 mt-2" data-i18n="ins.best">Best sellers</h2>
<ul class="mb-0">
    @forelse($top as $product)
        <li>{{ $product->name }} · {{ (int) $product->sold }} <span data-i18n="ins.sold">sold</span> · {{ $product->views_count }} <span data-i18n="ins.views">views</span></li>
    @empty
        <li class="muted" data-i18n="ins.emptyBest">No best-seller data yet.</li>
    @endforelse
</ul>
@endsection
