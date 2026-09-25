@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')
<div class="panel-head mb-4">
    <div>
        <p class="panel-kicker mb-1">Operations</p>
        <h1 class="section-title mb-1">Admin dashboard</h1>
        <p class="muted mb-0">Pickup orders, stall approvals, and market activity — all in Rs, paid at the stall.</p>
    </div>
    <div class="panel-actions">
        <a class="btn btn-outline-ml btn-sm" href="{{ route('admin.farmers.index', ['status' => 'pending']) }}"><i class="bi bi-hourglass"></i> Pending farmers</a>
        <a class="btn btn-ml btn-sm" href="{{ route('admin.orders.index', ['status' => 'placed']) }}"><i class="bi bi-receipt"></i> Open orders</a>
    </div>
</div>

<div class="row g-3 mb-4">
    @php
        $cards = [
            ['Customers', $metrics['customers'], route('admin.users.index'), 'bi-people', 'tone-blue'],
            ['Farmers', $metrics['farmers'], route('admin.farmers.index'), 'bi-shop', 'tone-green'],
            ['Pending farmers', $metrics['pending'], route('admin.farmers.index', ['status' => 'pending']), 'bi-hourglass', 'tone-amber'],
            ['Approved farmers', $metrics['approved'], route('admin.farmers.index', ['status' => 'approved']), 'bi-patch-check', 'tone-teal'],
            ['Markets', $metrics['markets'], route('admin.markets.index'), 'bi-geo-alt', 'tone-slate'],
            ['Products', $metrics['products'], route('admin.products.index'), 'bi-basket', 'tone-lime'],
            ['Orders', $metrics['orders'], route('admin.orders.index'), 'bi-receipt', 'tone-orange'],
            ['Pending orders', $metrics['pending_orders'], route('admin.orders.index', ['status' => 'placed']), 'bi-hourglass-split', 'tone-rose'],
            ['Completed', $metrics['completed'], route('admin.orders.index', ['status' => 'completed']), 'bi-check2-circle', 'tone-forest'],
        ];
    @endphp
    @foreach($cards as [$label, $value, $url, $icon, $tone])
        <div class="col-6 col-md-4 col-xl-3">
            <a class="stat-tile {{ $tone }} d-block text-decoration-none" href="{{ $url }}">
                <span><i class="bi {{ $icon }}"></i> {{ $label }}</span>
                <strong>{{ $value }}</strong>
            </a>
        </div>
    @endforeach
</div>

<div class="panel-revenue card-ml panel-card p-3 mb-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
    <div>
        <div class="small text-uppercase fw-bold muted">Completed pickup revenue</div>
        <div class="fs-4 fw-bold text-success-emphasis">{{ money($metrics['revenue']) }}</div>
    </div>
    <span class="badge text-bg-light border">Paid in person · Rs</span>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6"><div class="card-ml panel-card p-3 chart-panel"><h2 class="h6 mb-3">Orders over time</h2><canvas id="ordersChart" height="140"></canvas></div></div>
    <div class="col-lg-6"><div class="card-ml panel-card p-3 chart-panel"><h2 class="h6 mb-3">Revenue (Rs)</h2><canvas id="revChart" height="140"></canvas></div></div>
    <div class="col-lg-4"><div class="card-ml panel-card p-3 chart-panel"><h2 class="h6 mb-3">Orders by status</h2><canvas id="statusChart" height="180"></canvas></div></div>
    <div class="col-lg-4"><div class="card-ml panel-card p-3 chart-panel"><h2 class="h6 mb-3">Top farmers</h2><canvas id="farmChart" height="180"></canvas></div></div>
    <div class="col-lg-4"><div class="card-ml panel-card p-3 chart-panel"><h2 class="h6 mb-3">Top products</h2><canvas id="productChart" height="180"></canvas></div></div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h2 class="h5 mb-0">Recent orders</h2>
            <a class="small fw-bold" href="{{ route('admin.orders.index') }}">View all</a>
        </div>
        <div class="table-responsive card-ml panel-card">
            <table class="table mb-0 align-middle">
                <thead><tr><th>Order</th><th>Customer</th><th>Farmer</th><th>Market</th><th>Amount</th><th>Status</th><th></th></tr></thead>
                <tbody>
                @forelse($activity as $order)
                    <tr>
                        <td class="fw-semibold">{{ $order->order_number }}</td>
                        <td>{{ $order->buyerName() }}</td>
                        <td>{{ $order->farmer->stall_name }}</td>
                        <td>{{ $order->market->name ?? '—' }}</td>
                        <td>{{ money($order->total_amount) }}</td>
                        <td>@include('partials.order-status-badge', ['status' => $order->status])</td>
                        <td><a href="{{ route('admin.orders.show', $order) }}">View</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center muted py-4">No orders yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h2 class="h5 mb-0">Recent farmers</h2>
            <a class="small fw-bold" href="{{ route('admin.farmers.index') }}">Manage</a>
        </div>
        @forelse($recentFarmers as $farmer)
            <a class="panel-list-item card-ml panel-card p-3 mb-2 d-block text-decoration-none text-reset" href="{{ route('admin.farmers.show', $farmer) }}">
                <div class="d-flex justify-content-between gap-2">
                    <strong>{{ $farmer->stall_name }}</strong>
                    <span class="badge text-bg-light border text-capitalize">{{ $farmer->approval_status }}</span>
                </div>
                <div class="small muted mt-1">{{ $farmer->user->name }} · {{ $farmer->user->phone ?? 'No phone' }}</div>
            </a>
        @empty
            <div class="empty-state card-ml panel-card"><i class="bi bi-shop"></i><p>No farmer applications yet.</p></div>
        @endforelse
    </div>
</div>
@endsection
@push('scripts')
<script>
const chartOpts = { responsive: true, animation: { duration: 180 }, plugins: { legend: { display: false } } };
new Chart(document.getElementById('ordersChart'), {type:'line', options: chartOpts, data:{labels:@json($ordersOverTime->pluck('day')), datasets:[{label:'Orders', data:@json($ordersOverTime->pluck('total')), borderColor:'#1f6b45', tension:.35, fill:false}]}});
new Chart(document.getElementById('revChart'), {type:'bar', options: chartOpts, data:{labels:@json($revenue->pluck('day')), datasets:[{label:'Rs', data:@json($revenue->pluck('total')), backgroundColor:'#c9a227'}]}});
new Chart(document.getElementById('statusChart'), {type:'doughnut', options:{responsive:true, animation:{duration:180}}, data:{labels:@json($byStatus->keys()), datasets:[{data:@json($byStatus->values()), backgroundColor:['#1f6b45','#c9a227','#3d8bfd','#e07a2f','#7a8b82','#b4473a']}]}});
new Chart(document.getElementById('farmChart'), {type:'bar', options: chartOpts, data:{labels:@json($topFarmers->pluck('stall_name')), datasets:[{label:'Orders', data:@json($topFarmers->pluck('orders_count')), backgroundColor:'#2f9d62'}]}});
new Chart(document.getElementById('productChart'), {type:'bar', options: chartOpts, data:{labels:@json($topProducts->pluck('product_name')), datasets:[{label:'Units', data:@json($topProducts->pluck('qty')), backgroundColor:'#0e3b2e'}]}});
</script>
@endpush
