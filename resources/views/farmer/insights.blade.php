@extends('layouts.app')
@section('title', 'Insights')
@section('content')
<div class="container py-5">
    <h1 class="display-font mb-4">Product performance</h1>
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="panel">
                <canvas id="monthlyChart" height="140"></canvas>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="panel table-responsive">
                <table class="table mb-0">
                    <thead><tr><th>Product</th><th>Units</th><th>Orders</th></tr></thead>
                    <tbody>
                    @foreach($productPerformance as $p)
                        <tr><td>{{ $p->name }}</td><td>{{ $p->units_sold ?? 0 }}</td><td>{{ $p->order_items_count }}</td></tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
new Chart(document.getElementById('monthlyChart'), {
    type: 'bar',
    data: {
        labels: @json($monthly->pluck('month')),
        datasets: [{ label: 'Revenue', data: @json($monthly->pluck('total')), backgroundColor: '#40916c' }]
    }
});
</script>
@endpush
