@extends('layouts.app')
@section('title', 'Admin dashboard')
@section('content')
<div class="container-fluid px-4 py-4">
    <h1 class="display-font mb-3">Admin dashboard</h1>
    <div class="row g-3 mb-4">
        @foreach(['users'=>'Users','farmers'=>'Farmers','pending_farmers'=>'Pending','orders'=>'Orders','revenue'=>'Revenue','products'=>'Products'] as $key=>$label)
            <div class="col-6 col-md-2">
                <div class="stat-tile">
                    <div class="small text-muted">{{ $label }}</div>
                    <div class="value">{{ $key==='revenue' ? '$'.number_format($stats[$key],0) : $stats[$key] }}</div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="panel mb-4"><h5>Revenue (14 days)</h5><canvas id="revChart" height="110"></canvas></div>
            <div class="panel">
                <h5>Recent orders</h5>
                @foreach($recentOrders as $o)
                    <div class="d-flex justify-content-between border-bottom py-2 small">
                        <span>{{ $o->order_number }} · {{ $o->customer->name }}</span>
                        <span class="badge {{ $o->statusBadgeClass() }}">{{ $o->statusLabel() }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="col-lg-4">
            <div class="panel mb-3">
                <h5>Orders by status</h5>
                <canvas id="statusChart"></canvas>
            </div>
            <div class="panel">
                <h5>Pending farmers</h5>
                @forelse($pendingFarmers as $f)
                    <div class="d-flex justify-content-between align-items-center py-1">
                        <span>{{ $f->stall_name }}</span>
                        <a href="{{ route('admin.farmers.index', ['approval_status'=>'pending']) }}" class="small">Review</a>
                    </div>
                @empty
                    <p class="text-muted mb-0">None</p>
                @endforelse
            </div>
            <div class="list-group mt-3">
                <a class="list-group-item" href="{{ route('admin.users.index') }}">Users</a>
                <a class="list-group-item" href="{{ route('admin.markets.index') }}">Markets</a>
                <a class="list-group-item" href="{{ route('admin.reports.index') }}">Reports & CSV</a>
                <a class="list-group-item" href="{{ route('admin.announcements.index') }}">Announcements</a>
                <a class="list-group-item" href="{{ route('admin.faqs.index') }}">Chatbot FAQs</a>
                <a class="list-group-item" href="{{ route('admin.settings.index') }}">Settings</a>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
new Chart(document.getElementById('revChart'),{type:'line',data:{labels:@json($revenueByDay->pluck('day')),datasets:[{data:@json($revenueByDay->pluck('total')),borderColor:'#2d6a4f',tension:.3}]},options:{plugins:{legend:{display:false}}}});
new Chart(document.getElementById('statusChart'),{type:'doughnut',data:{labels:@json($ordersByStatus->keys()),datasets:[{data:@json($ordersByStatus->values()),backgroundColor:['#52b788','#40916c','#dc3545','#e9c46a','#2d6a4f','#6c757d']}]}});
</script>
@endpush
