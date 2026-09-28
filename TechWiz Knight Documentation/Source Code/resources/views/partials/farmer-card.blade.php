@php use App\Support\ImageStore; @endphp
<a class="card-ml farmer-card text-decoration-none text-reset d-block h-100" href="{{ route('farmers.show', $farmer) }}">
    <div class="farmer-card-photo">
        <img src="{{ ImageStore::picture($farmer->logo, $farmer->stall_name) }}" alt="{{ $farmer->stall_name }}" width="640" height="280" loading="lazy" decoding="async">
    </div>
    <div class="farmer-card-body">
        <h3>{{ $farmer->stall_name }}</h3>
        <p class="farmer-card-days">{{ implode(', ', $farmer->operating_days ?? []) ?: 'Market days TBA' }}</p>
        @if($farmer->user->phone ?? null)
            <p class="farmer-card-phone"><i class="bi bi-telephone-fill" aria-hidden="true"></i> {{ $farmer->user->phone }}</p>
        @endif
        <p class="farmer-card-count">{{ $farmer->products_count ?? $farmer->products->count() }} products</p>
    </div>
</a>
