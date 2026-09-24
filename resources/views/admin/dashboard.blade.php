@extends('layouts.app')
@section('title', 'Admin')
@section('content')
<h1 class="section-title">Platform overview</h1>
<div class="row g-3 mb-4">
    @foreach(['Farmers'=>$metrics['farmers'],'Approved'=>$metrics['approved'],'Pending'=>$metrics['pending'],'Customers'=>$metrics['customers'],'Markets'=>$metrics['markets'],'Orders'=>$metrics['orders']] as $label=>$value)
        <div class="col-6 col-md-4 col-lg-2"><div class="card-ml stat"><span class="muted">{{ $label }}</span><strong>{{ $value }}</strong></div></div>
    @endforeach
</div>
<p>Completed pickup revenue: <strong>${{ number_format($metrics['revenue'], 2) }}</strong></p>
<div class="row g-4">
    <div class="col-lg-7"><canvas id="revChart" height="140"></canvas></div>
    <div class="col-lg-5"><canvas id="farmChart" height="140"></canvas></div>
</div>
<h2 class="h5 mt-4">Orders by status</h2>
<div class="d-flex gap-2 flex-wrap mb-3">@foreach($byStatus as $status=>$total)<span class="badge badge-soft">{{ $status }}: {{ $total }}</span>@endforeach</div>
<h2 class="h5">Recent orders</h2>
@foreach($activity as $order)
    <div class="card-ml p-2 mb-2">{{ $order->order_number }} · {{ $order->customer->name }} · {{ $order->farmer->stall_name }} · {{ $order->status }}</div>
@endforeach
<canvas id="growthChart" height="80" class="mt-3"></canvas>
@endsection
@push('scripts')
<script>
new Chart(document.getElementById('revChart'), {type:'bar', data:{labels:@json($revenue->pluck('day')), datasets:[{label:'Revenue', data:@json($revenue->pluck('total')), backgroundColor:'#1f7a4d'}]}});
new Chart(document.getElementById('farmChart'), {type:'doughnut', data:{labels:@json($topFarmers->pluck('stall_name')), datasets:[{data:@json($topFarmers->pluck('orders_count'))}]}});
new Chart(document.getElementById('growthChart'), {type:'line', data:{labels:@json($growth->pluck('day')), datasets:[{label:'New users', data:@json($growth->pluck('total')), borderColor:'#e09a12'}]}});
</script>
@endpush
