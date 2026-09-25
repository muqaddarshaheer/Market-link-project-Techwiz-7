@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')
<div class="row g-3 mb-4">
    @php
        $cards = [
            ['Customers', $metrics['customers'], route('admin.users.index'), 'bi-people'],
            ['Farmers', $metrics['farmers'], route('admin.farmers.index'), 'bi-shop'],
            ['Pending farmers', $metrics['pending'], route('admin.farmers.index', ['status' => 'pending']), 'bi-hourglass'],
            ['Approved farmers', $metrics['approved'], route('admin.farmers.index', ['status' => 'approved']), 'bi-patch-check'],
            ['Markets', $metrics['markets'], route('admin.markets.index'), 'bi-geo-alt'],
            ['Products', $metrics['products'], route('admin.products.index'), 'bi-basket'],
            ['Orders', $metrics['orders'], route('admin.orders.index'), 'bi-receipt'],
            ['Pending orders', $metrics['pending_orders'], route('admin.orders.index', ['status' => 'placed']), 'bi-hourglass-split'],
            ['Completed', $metrics['completed'], route('admin.orders.index', ['status' => 'completed']), 'bi-check2-circle'],
        ];
    @endphp
    @foreach($cards as [$label, $value, $url, $icon])
        <div class="col-6 col-md-4 col-xl-3">
            <a class="card-ml stat d-block text-decoration-none text-reset" href="{{ $url }}">
                <span class="muted"><i class="bi {{ $icon }}"></i> {{ $label }}</span>
                <strong>{{ $value }}</strong>
            </a>
        </div>
    @endforeach
</div>
<div class="card-ml p-3 mb-4">Completed pickup revenue <strong>${{ number_format($metrics['revenue'], 2) }}</strong>. Payment is collected at the stall.</div>
<div class="row g-4 mb-4">
    <div class="col-lg-6"><div class="card-ml p-3"><h2 class="h6">Orders over time</h2><canvas id="ordersChart" height="140"></canvas></div></div>
    <div class="col-lg-6"><div class="card-ml p-3"><h2 class="h6">Revenue over time</h2><canvas id="revChart" height="140"></canvas></div></div>
    <div class="col-lg-4"><div class="card-ml p-3"><h2 class="h6">Orders by status</h2><canvas id="statusChart" height="180"></canvas></div></div>
    <div class="col-lg-4"><div class="card-ml p-3"><h2 class="h6">Top farmers</h2><canvas id="farmChart" height="180"></canvas></div></div>
    <div class="col-lg-4"><div class="card-ml p-3"><h2 class="h6">Top products</h2><canvas id="productChart" height="180"></canvas></div></div>
</div>
<div class="row g-4">
    <div class="col-lg-7">
        <h2 class="h5">Recent orders</h2>
        <div class="table-responsive card-ml">
            <table class="table mb-0">
                <thead><tr><th>Order</th><th>Customer</th><th>Farmer</th><th>Market</th><th>Amount</th><th>Status</th><th></th></tr></thead>
                <tbody>
                @foreach($activity as $order)
                    <tr>
                        <td>{{ $order->order_number }}</td>
                        <td>{{ $order->buyerName() }}</td>
                        <td>{{ $order->farmer->stall_name }}</td>
                        <td>{{ $order->market->name ?? '' }}</td>
                        <td>${{ number_format($order->total_amount, 2) }}</td>
                        <td>@include('partials.order-status-badge', ['status' => $order->status])</td>
                        <td><a href="{{ route('admin.orders.show', $order) }}">View</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-5">
        <h2 class="h5">Recent farmer registrations</h2>
        @foreach($recentFarmers as $farmer)
            <div class="card-ml p-3 mb-2">
                <div class="d-flex justify-content-between gap-2">
                    <div><strong>{{ $farmer->stall_name }}</strong><div class="small muted">{{ $farmer->user->name }} · {{ $farmer->approval_status }} · {{ $farmer->created_at?->format('M j, Y') }}</div></div>
                    <a href="{{ route('admin.farmers.show', $farmer) }}">View</a>
                </div>
            </div>
        @endforeach
        <h2 class="h5 mt-3">Recent reviews</h2>
        @foreach($recentReviews as $review)
            <div class="card-ml p-3 mb-2">
                <strong>{{ $review->customer->name }}</strong>
                <div class="small muted">{{ $review->product->name ?? $review->farmer->stall_name ?? 'Stall' }} · {{ $review->rating }}/5 · {{ $review->status }}</div>
                <a href="{{ route('admin.reviews.index') }}">Moderate</a>
            </div>
        @endforeach
    </div>
</div>
@endsection
@push('scripts')
<script>
new Chart(document.getElementById('ordersChart'), {type:'line', data:{labels:@json($ordersOverTime->pluck('day')), datasets:[{label:'Orders', data:@json($ordersOverTime->pluck('total')), borderColor:'#1f7a4d'}]}});
new Chart(document.getElementById('revChart'), {type:'bar', data:{labels:@json($revenue->pluck('day')), datasets:[{label:'Revenue', data:@json($revenue->pluck('total')), backgroundColor:'#1f7a4d'}]}});
new Chart(document.getElementById('statusChart'), {type:'doughnut', data:{labels:@json($byStatus->keys()), datasets:[{data:@json($byStatus->values())}]}});
new Chart(document.getElementById('farmChart'), {type:'bar', data:{labels:@json($topFarmers->pluck('stall_name')), datasets:[{label:'Orders', data:@json($topFarmers->pluck('orders_count')), backgroundColor:'#e07a2f'}]}});
new Chart(document.getElementById('productChart'), {type:'bar', data:{labels:@json($topProducts->pluck('product_name')), datasets:[{label:'Units', data:@json($topProducts->pluck('qty')), backgroundColor:'#146843'}]}});
</script>
@endpush
