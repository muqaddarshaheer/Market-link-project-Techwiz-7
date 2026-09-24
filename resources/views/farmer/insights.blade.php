@extends('layouts.farmer')
@section('title', 'Insights')
@section('content')
<h1 class="section-title">Sales insights</h1>
<form class="d-flex gap-2 mb-3" method="GET">
    <select class="form-select" name="range" style="max-width:180px"><option value="today">Today</option><option value="7" @selected(request('range','7')==='7')>7 days</option><option value="30" @selected(request('range')==='30')>30 days</option></select>
    <input class="form-control" type="date" name="from" value="{{ request('from') }}">
    <input class="form-control" type="date" name="to" value="{{ request('to') }}">
    <button class="btn btn-ml">Apply</button>
</form>
<canvas id="orders" height="110"></canvas>
<canvas id="rev" height="110" class="mt-3"></canvas>
<h2 class="h5 mt-4">Best sellers</h2>
<ul>@foreach($top as $product)<li>{{ $product->name }} · {{ (int) $product->sold }} sold · {{ $product->views_count }} views</li>@endforeach</ul>
@endsection
@push('scripts')
<script>
new Chart(document.getElementById('orders'), {type:'bar', data:{labels:@json($byDay->pluck('day')), datasets:[{label:'Orders', data:@json($byDay->pluck('total')), backgroundColor:'#e07a2f'}]}});
new Chart(document.getElementById('rev'), {type:'line', data:{labels:@json($byDay->pluck('day')), datasets:[{label:'Revenue', data:@json($byDay->pluck('revenue')), borderColor:'#1f7a4d'}]}});
</script>
@endpush
