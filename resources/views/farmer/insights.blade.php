@extends('layouts.farmer')
@section('title', 'Sales')
@section('content')
<div class="panel-head mb-4">
    <div>
        <p class="panel-kicker mb-1" data-i18n="ins.kicker">Stall books</p>
        <h1 class="section-title mb-1" data-i18n="ins.title">Sales</h1>
        <p class="muted mb-0" data-i18n="ins.lead">Pickup sales by day — print an 80mm summary.</p>
    </div>
    <div class="panel-actions">
        <button type="button" class="btn btn-outline-ml btn-sm" id="salesPrintBtn">
            <i class="bi bi-printer"></i> <span data-i18n="ins.print">Print 80mm</span>
        </button>
        <a class="btn btn-ml btn-sm" href="{{ route('farmer.expenses.index') }}"><i class="bi bi-wallet2"></i> <span data-i18n="ins.expenses">Expenses</span></a>
    </div>
</div>

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

<div class="card-ml panel-card p-3">
    <h2 class="h5 mb-3" data-i18n="ins.best">Best sellers</h2>
    <ul class="mb-0">
        @forelse($top as $product)
            <li>{{ $product->name }} · {{ (int) $product->sold }} <span data-i18n="ins.sold">sold</span> · {{ $product->views_count }} <span data-i18n="ins.views">views</span></li>
        @empty
            <li class="muted" data-i18n="ins.emptyBest">No best-seller data yet.</li>
        @endforelse
    </ul>
</div>

<script type="application/json" id="salesPrintData">@json([
    'stall' => $farmer->stall_name,
    'orders' => $orderTotal,
    'revenue' => $revenueTotal,
    'rows' => $byDay->map(fn ($r) => [
        'day' => $r->day,
        'orders' => (int) $r->total,
        'revenue' => (float) $r->revenue,
    ])->values(),
])</script>
@endsection

@push('scripts')
<script>
(function () {
  var btn = document.getElementById('salesPrintBtn');
  var raw = document.getElementById('salesPrintData');
  if (!btn || !raw) return;
  btn.addEventListener('click', function () {
    var data;
    try { data = JSON.parse(raw.textContent || '{}'); } catch (e) { return; }
    var money = function (n) {
      return 'Rs. ' + (Number(n) || 0).toLocaleString('en-PK', { maximumFractionDigits: 0 });
    };
    var lines = (data.rows || []).map(function (r) {
      return '<div class="line"><span>' + r.day + ' (' + r.orders + ')</span><strong>' + money(r.revenue) + '</strong></div>';
    }).join('');
    var html = '<!DOCTYPE html><html><head><title>Sales slip</title><style>'
      + '@page{size:80mm auto;margin:2mm}'
      + 'body{font-family:monospace;font-size:12px;width:72mm;margin:0 auto;color:#000}'
      + 'h1{font-size:14px;margin:0 0 4px;text-align:center}'
      + 'p{margin:0 0 4px;text-align:center}'
      + '.line{display:flex;justify-content:space-between;gap:6px;border-bottom:1px dashed #999;padding:3px 0}'
      + '.tot{margin-top:8px;border-top:2px solid #000;padding-top:6px;font-weight:700}'
      + '</style></head><body>'
      + '<h1>MarketLink</h1>'
      + '<p>' + (data.stall || '') + '</p>'
      + '<p>Sales summary</p><hr>'
      + lines
      + '<div class="tot"><div class="line"><span>Orders</span><strong>' + (data.orders || 0) + '</strong></div>'
      + '<div class="line"><span>Revenue</span><strong>' + money(data.revenue) + '</strong></div></div>'
      + '<p style="margin-top:10px">Thank you</p>'
      + '<script>window.onload=function(){window.print();}</' + 'script>'
      + '</body></html>';
    var w = window.open('', '_blank', 'width=320,height=600');
    if (!w) { alert('Allow popups to print'); return; }
    w.document.open();
    w.document.write(html);
    w.document.close();
  });
})();
</script>
@endpush
