@extends('layouts.app')
@section('title', 'Reports')
@section('content')
<h1 class="section-title">Reports</h1>
<form class="row g-2 mb-3" method="GET">
    <div class="col-md-3"><input class="form-control" type="date" name="from" value="{{ $from->toDateString() }}"></div>
    <div class="col-md-3"><input class="form-control" type="date" name="to" value="{{ $to->toDateString() }}"></div>
    <div class="col-md-3"><button class="btn btn-ml">Run</button></div>
    <div class="col-md-3"><a class="btn btn-outline-ml" href="{{ route('admin.reports.export', request()->query()) }}">Export CSV</a></div>
</form>
<div class="row g-3 mb-3">
    <div class="col-md-6"><div class="card-ml stat"><span class="muted">Orders</span><strong>{{ $payload['orders'] }}</strong></div></div>
    <div class="col-md-6"><div class="card-ml stat"><span class="muted">Completed revenue</span><strong>${{ number_format($payload['revenue'], 2) }}</strong></div></div>
</div>
<h2 class="h6">Active farmers</h2>
<ul>@foreach($payload['farmers'] as $farmer)<li>{{ $farmer->stall_name }} · {{ $farmer->orders_count }} orders</li>@endforeach</ul>
<h2 class="h6">Popular products</h2>
<ul>@foreach($payload['products'] as $product)<li>{{ $product->name }} · {{ (int) $product->sold }} sold</li>@endforeach</ul>
<canvas id="growth" height="90"></canvas>
@endsection
@push('scripts')
<script>new Chart(document.getElementById('growth'), {type:'line', data:{labels:@json($payload['growth']->pluck('day')), datasets:[{label:'User growth', data:@json($payload['growth']->pluck('total')), borderColor:'#1f7a4d'}]}});</script>
@endpush
