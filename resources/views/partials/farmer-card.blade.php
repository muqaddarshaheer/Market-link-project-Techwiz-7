@php use App\Support\ImageStore; @endphp
<a class="card-ml farmer-card text-decoration-none text-reset d-block h-100" href="{{ route('farmers.show', $farmer) }}">
    <div class="farmer-card-photo">
        <img src="{{ ImageStore::picture($farmer->logo, $farmer->stall_name) }}" alt="{{ $farmer->stall_name }}" width="640" height="280" loading="lazy" decoding="async">
    </div>
    <div class="p-3">
        <h3 class="h5 mb-1">{{ $farmer->stall_name }}</h3>
        <div class="small muted mb-1">{{ implode(', ', $farmer->operating_days ?? []) }}</div>
        @if($farmer->user->phone ?? null)
            <div class="farmer-dir-phone small mb-1"><i class="bi bi-telephone-fill" aria-hidden="true"></i> {{ $farmer->user->phone }}</div>
        @endif
        <div class="small">{{ $farmer->products_count ?? $farmer->products->count() }} products</div>
    </div>
</a>
