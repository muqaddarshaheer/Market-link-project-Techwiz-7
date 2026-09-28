@extends('layouts.farmer')
@section('title', 'Expenses')
@section('content')
<div class="panel-head mb-4">
    <div>
        <p class="panel-kicker mb-1" data-i18n="exp.kicker">Stall books</p>
        <h1 class="section-title mb-1" data-i18n="exp.title">Expenses</h1>
        <p class="muted mb-0" data-i18n="exp.lead">Track farm costs and print an 80mm slip.</p>
    </div>
    <div class="panel-actions">
        <button type="button" class="btn btn-outline-ml btn-sm" id="expPrintBtn">
            <i class="bi bi-printer"></i> <span data-i18n="exp.print">Print 80mm</span>
        </button>
        <a class="btn btn-ml btn-sm" href="{{ route('farmer.sales') }}"><i class="bi bi-graph-up"></i> <span data-i18n="exp.sales">Sales</span></a>
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

<form method="GET" class="d-flex flex-wrap gap-2 mb-3">
    <input class="form-control" type="month" name="month" value="{{ $month }}" style="max-width:200px">
    <button class="btn btn-ml btn-sm" data-i18n="exp.apply">Apply</button>
</form>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card-ml panel-card p-3 exp-stat">
            <div class="small muted mb-1" data-i18n="exp.total">Expenses</div>
            <div class="fs-4 fw-bold">{{ money($total) }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-ml panel-card p-3 exp-stat">
            <div class="small muted mb-1" data-i18n="exp.salesMonth">Sales (completed)</div>
            <div class="fs-4 fw-bold text-success">{{ money($sales) }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-ml panel-card p-3 exp-stat">
            <div class="small muted mb-1" data-i18n="exp.net">Net (sales − expenses)</div>
            <div class="fs-4 fw-bold {{ $net >= 0 ? 'text-success' : 'text-danger' }}">{{ money($net) }}</div>
        </div>
    </div>
</div>

<div class="card-ml panel-card p-3 p-md-4 mb-4">
    <h2 class="h5 mb-3" data-i18n="exp.add">Add expense</h2>
    <form method="POST" action="{{ route('farmer.expenses.store') }}" class="row g-3">
        @csrf
        <div class="col-md-4">
            <label class="form-label" for="title" data-i18n="exp.name">Title</label>
            <input class="form-control" id="title" name="title" value="{{ old('title') }}" required maxlength="160" placeholder="e.g. Urea / Labor">
        </div>
        <div class="col-md-3">
            <label class="form-label" for="category" data-i18n="exp.category">Category</label>
            <select class="form-select" id="category" name="category" required>
                @foreach($categories as $key => $label)
                    <option value="{{ $key }}" @selected(old('category', 'other') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label" for="amount" data-i18n="exp.amount">Amount (Rs)</label>
            <input class="form-control" id="amount" name="amount" type="number" min="0.01" step="0.01" value="{{ old('amount') }}" required>
        </div>
        <div class="col-md-3">
            <label class="form-label" for="expense_date" data-i18n="exp.date">Date</label>
            <input class="form-control" id="expense_date" name="expense_date" type="date" value="{{ old('expense_date', now()->toDateString()) }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label" for="crop_name" data-i18n="exp.crop">Crop (optional)</label>
            <input class="form-control" id="crop_name" name="crop_name" value="{{ old('crop_name') }}" maxlength="120">
        </div>
        <div class="col-md-6">
            <label class="form-label" for="notes" data-i18n="exp.notes">Notes</label>
            <input class="form-control" id="notes" name="notes" value="{{ old('notes') }}" maxlength="500">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button class="btn btn-ml w-100" type="submit" data-i18n="exp.save">Save</button>
        </div>
    </form>
</div>

<div class="card-ml panel-card p-3 p-md-4" id="expLedger">
    <h2 class="h5 mb-3" data-i18n="exp.list">This month</h2>
    @forelse($expenses as $expense)
        <div class="exp-row d-flex flex-wrap align-items-center gap-2 py-2 border-bottom">
            <div class="flex-grow-1 min-w-0">
                <strong class="d-block text-truncate">{{ $expense->title }}</strong>
                <span class="small muted">{{ $expense->expense_date->format('d M Y') }} · {{ $expense->categoryLabel() }}@if($expense->crop_name) · {{ $expense->crop_name }}@endif</span>
            </div>
            <strong class="text-nowrap">{{ money($expense->amount) }}</strong>
            <form method="POST" action="{{ route('farmer.expenses.destroy', $expense) }}" onsubmit="return confirm('Delete this expense?')">
                @csrf @method('DELETE')
                <button class="btn btn-outline-ml btn-sm" type="submit" aria-label="Delete"><i class="bi bi-trash"></i></button>
            </form>
        </div>
    @empty
        <div class="dash-empty text-center py-4">
            <i class="bi bi-wallet2"></i>
            <p class="mb-0 muted" data-i18n="exp.empty">No expenses this month yet.</p>
        </div>
    @endforelse
</div>

@php
    $expPrintData = [
        'stall' => $farmer->stall_name,
        'month' => $month,
        'total' => $total,
        'sales' => $sales,
        'net' => $net,
        'rows' => $expenses->map(function ($e) {
            return [
                'title' => $e->title,
                'date' => $e->expense_date->format('d/m/Y'),
                'cat' => $e->categoryLabel(),
                'amount' => (float) $e->amount,
            ];
        })->values()->all(),
    ];
@endphp
<script type="application/json" id="expPrintData">@json($expPrintData)</script>
@endsection

@push('scripts')
<script>
(function () {
  var btn = document.getElementById('expPrintBtn');
  var raw = document.getElementById('expPrintData');
  if (!btn || !raw) return;
  btn.addEventListener('click', function () {
    var data;
    try { data = JSON.parse(raw.textContent || '{}'); } catch (e) { return; }
    var money = function (n) {
      return 'Rs. ' + (Number(n) || 0).toLocaleString('en-PK', { maximumFractionDigits: 0 });
    };
    var lines = (data.rows || []).map(function (r) {
      return '<div class="line"><span>' + r.date + ' ' + r.title + '</span><strong>' + money(r.amount) + '</strong></div>';
    }).join('');
    var html = '<!DOCTYPE html><html><head><title>Expense slip</title><style>'
      + '@page{size:80mm auto;margin:2mm}'
      + 'body{font-family:monospace;font-size:12px;width:72mm;margin:0 auto;color:#000}'
      + 'h1{font-size:14px;margin:0 0 4px;text-align:center}'
      + 'p{margin:0 0 4px;text-align:center}'
      + '.line{display:flex;justify-content:space-between;gap:6px;border-bottom:1px dashed #999;padding:3px 0}'
      + '.tot{margin-top:8px;border-top:2px solid #000;padding-top:6px;font-weight:700}'
      + '</style></head><body>'
      + '<h1>MarketLink</h1>'
      + '<p>' + (data.stall || '') + '</p>'
      + '<p>Expenses · ' + (data.month || '') + '</p>'
      + '<hr>'
      + lines
      + '<div class="tot"><div class="line"><span>Total expense</span><strong>' + money(data.total) + '</strong></div>'
      + '<div class="line"><span>Sales</span><strong>' + money(data.sales) + '</strong></div>'
      + '<div class="line"><span>Net</span><strong>' + money(data.net) + '</strong></div></div>'
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
