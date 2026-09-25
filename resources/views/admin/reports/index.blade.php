@extends('layouts.admin')
@section('title', 'Reports')
@section('content')
<div class="desk-head">
    <div>
        <h1 class="section-title mb-1">Reports</h1>
        <p class="muted mb-0">Pickup volume and stall performance in Pakistani Rupees.</p>
    </div>
</div>
<form class="desk-toolbar" method="GET">
    <input class="form-control" type="date" name="from" value="{{ $from->toDateString() }}">
    <input class="form-control" type="date" name="to" value="{{ $to->toDateString() }}">
    <button class="btn btn-ml">Run</button>
    <a class="btn btn-outline-ml" href="{{ route('admin.reports.export', request()->query()) }}">Export CSV</a>
</form>
<div class="row g-3 mb-4">
    <div class="col-md-6"><div class="stat-tile tone-orange"><span>Orders</span><strong>{{ $payload['orders'] }}</strong></div></div>
    <div class="col-md-6"><div class="stat-tile tone-forest"><span>Completed revenue</span><strong>{{ money($payload['revenue']) }}</strong></div></div>
</div>
<div class="row g-4">
    <div class="col-lg-5">
        <div class="card-ml p-3 mb-3">
            <h2 class="h6">Active farmers</h2>
            @foreach($payload['farmers'] as $farmer)
                <div class="d-flex justify-content-between border-bottom py-2"><span>{{ $farmer->stall_name }}</span><strong>{{ $farmer->orders_count }}</strong></div>
            @endforeach
        </div>
        <div class="card-ml p-3">
            <h2 class="h6">Popular products</h2>
            @foreach($payload['products'] as $product)
                <div class="d-flex justify-content-between border-bottom py-2"><span>{{ $product->name }}</span><strong>{{ (int) $product->sold }}</strong></div>
            @endforeach
        </div>
    </div>
    <div class="col-lg-7"><div class="card-ml p-3 chart-panel"><h2 class="h6">User growth</h2><canvas id="growth" height="120"></canvas></div></div>
</div>
@endsection
@push('scripts')
<script>new Chart(document.getElementById('growth'), {type:'line', options:{responsive:true, animation:{duration:180}, plugins:{legend:{display:false}}}, data:{labels:@json($payload['growth']->pluck('day')), datasets:[{data:@json($payload['growth']->pluck('total')), borderColor:'#1f6b45', tension:.35, fill:true, backgroundColor:'rgba(31,107,69,.12)'}]}});</script>
@endpush
