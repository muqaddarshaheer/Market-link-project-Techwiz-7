@extends('layouts.app')
@section('title', 'Farmers')
@section('content')
<div class="container py-5">
    <h1 class="display-font">Local farmers</h1>
    <p class="section-sub">Approved stalls you can pre-order from.</p>
    <form class="row g-2 mb-4" method="GET">
        <div class="col-md-8"><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Search stall or farmer"></div>
        <div class="col-md-4"><button class="btn btn-primary w-100">Search</button></div>
    </form>
    <div id="farmersMap" class="map-box mb-4"></div>
    <div class="row g-4">
        @forelse($farmers as $farmer)
            <div class="col-md-6 col-lg-4">
                <div class="farmer-card p-3">
                    <div class="d-flex gap-3 align-items-center mb-2">
                        <img src="{{ $farmer->logoUrl() }}" class="rounded-circle" style="width:64px;height:64px;object-fit:cover" alt="">
                        <div>
                            <h5 class="mb-0">{{ $farmer->stall_name }}</h5>
                            <small class="text-muted">{{ number_format($farmer->avg_rating ?? 0, 1) }} ★ · {{ $farmer->products_count }} products</small>
                        </div>
                    </div>
                    <p class="small">{{ Str::limit($farmer->business_description, 100) }}</p>
                    <a href="{{ route('farmers.show', $farmer) }}" class="btn btn-sm btn-primary">Visit stall</a>
                </div>
            </div>
        @empty
            <div class="empty-state">No farmers found.</div>
        @endforelse
    </div>
    <div class="mt-4">{{ $farmers->links() }}</div>
</div>
@endsection
@push('scripts')
<script>initMap('farmersMap', @json($mapFarmers));</script>
@endpush
