@extends('layouts.app')
@section('title', 'Farmer dashboard')
@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <h1 class="display-font">{{ $profile->stall_name }}</h1>
            <span class="badge {{ $profile->approval_status==='approved'?'bg-success':($profile->approval_status==='pending'?'bg-warning text-dark':'bg-danger') }}">{{ $profile->approval_status }}</span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('farmer.profile') }}" class="btn btn-outline-primary">Edit profile</a>
            @if($profile->isApproved())
                <a href="{{ route('farmer.products.create') }}" class="btn btn-primary">Add product</a>
            @endif
        </div>
    </div>

    @if(!$profile->isApproved())
        <div class="alert alert-warning">Your stall is awaiting admin approval. You can edit your profile meanwhile.</div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="stat-tile"><div class="small text-muted">Revenue</div><div class="value">${{ number_format($revenue,2) }}</div></div></div>
        <div class="col-md-3"><div class="stat-tile"><div class="small text-muted">Placed</div><div class="value">{{ $orderStats['placed'] ?? 0 }}</div></div></div>
        <div class="col-md-3"><div class="stat-tile"><div class="small text-muted">Ready</div><div class="value">{{ $orderStats['ready_for_pickup'] ?? 0 }}</div></div></div>
        <div class="col-md-3"><div class="stat-tile"><div class="small text-muted">Completed</div><div class="value">{{ $orderStats['completed'] ?? 0 }}</div></div></div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="panel mb-4">
                <h5>Sales (14 days)</h5>
                <canvas id="salesChart" height="120"></canvas>
            </div>
            <div class="panel">
                <div class="d-flex justify-content-between mb-2"><h5>Recent orders</h5>
                    @if($profile->isApproved())<a href="{{ route('farmer.orders.index') }}">Manage</a>@endif
                </div>
                @foreach($recentOrders as $order)
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <div>{{ $order->order_number }} · {{ $order->customer->name }}</div>
                        <span class="badge {{ $order->statusBadgeClass() }}">{{ $order->statusLabel() }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="col-lg-4">
            <div class="panel mb-3">
                <h5>Low stock</h5>
                @forelse($lowStock as $p)
                    <div class="d-flex justify-content-between"><span>{{ $p->name }}</span><strong>{{ $p->stock_quantity }}</strong></div>
                @empty
                    <p class="text-muted mb-0">All good.</p>
                @endforelse
            </div>
            <div class="panel">
                <h5>Top products</h5>
                @foreach($topProducts as $p)
                    <div class="small mb-1">{{ $p->name }} <span class="text-muted">({{ $p->order_items_count }} orders)</span></div>
                @endforeach
                @if($profile->isApproved())
                    <a href="{{ route('farmer.insights') }}" class="btn btn-sm btn-outline-primary mt-2">Insights</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
new Chart(document.getElementById('salesChart'), {
    type: 'line',
    data: {
        labels: @json($salesByDay->pluck('day')),
        datasets: [{ label: 'Revenue', data: @json($salesByDay->pluck('total')), borderColor: '#2d6a4f', tension: .3, fill: false }]
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});
</script>
@endpush
