@extends('layouts.app')
@section('title', 'Insights')
@section('content')
<h1 class="section-title">Sales insights</h1>
<canvas id="rev" height="120"></canvas>
<h2 class="h5 mt-4">Best sellers</h2>
<ul>@foreach($top as $product)<li>{{ $product->name }} · {{ (int) $product->sold }} sold · {{ $product->views_count }} views</li>@endforeach</ul>
@endsection
@push('scripts')
<script>
new Chart(document.getElementById('rev'), {type:'line', data:{labels:@json($byDay->pluck('day')), datasets:[{label:'Revenue', data:@json($byDay->pluck('revenue')), borderColor:'#1f7a4d'}]}});
</script>
@endpush
