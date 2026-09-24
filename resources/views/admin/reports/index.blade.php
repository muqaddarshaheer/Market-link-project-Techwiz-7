@extends('layouts.app')
@section('title', 'Reports')
@section('content')
<div class="container py-4">
    <h1 class="h3 display-font mb-3">Reports</h1>
    <div class="d-flex flex-wrap gap-2 mb-4">
        <a class="btn btn-outline-primary" href="{{ route('admin.reports.export', ['type'=>'orders']) }}">Export orders CSV</a>
        <a class="btn btn-outline-primary" href="{{ route('admin.reports.export', ['type'=>'farmers']) }}">Export farmers CSV</a>
        <a class="btn btn-outline-primary" href="{{ route('admin.reports.export', ['type'=>'products']) }}">Export products CSV</a>
        <a class="btn btn-outline-primary" href="{{ route('admin.reports.export', ['type'=>'growth']) }}">Export growth CSV</a>
    </div>
    <div class="row g-4">
        <div class="col-md-4"><div class="stat-tile"><div class="small text-muted">Orders</div><div class="value">{{ $orderReport['total'] }}</div><div>Completed {{ $orderReport['completed'] }} · Rev ${{ number_format($orderReport['revenue'],2) }}</div></div></div>
        <div class="col-md-8"><div class="panel"><h5>Growth</h5><canvas id="growthChart" height="100"></canvas></div></div>
        <div class="col-md-6">
            <div class="panel"><h5>Top farmers</h5>
                @foreach($farmerReport as $f)<div class="d-flex justify-content-between border-bottom py-1"><span>{{ $f->stall_name }}</span><span>{{ $f->orders_count }} orders</span></div>@endforeach
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel"><h5>Top products</h5>
                @foreach($productReport as $p)<div class="d-flex justify-content-between border-bottom py-1"><span>{{ $p->name }}</span><span>{{ $p->order_items_count }}</span></div>@endforeach
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>new Chart(document.getElementById('growthChart'),{type:'bar',data:{labels:@json($growth->pluck('month')),datasets:[{label:'Users',data:@json($growth->pluck('total')),backgroundColor:'#52b788'}]}});</script>
@endpush
