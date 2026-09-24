@php use App\Support\ImageStore; @endphp
<a class="card-ml text-decoration-none text-reset d-block h-100" href="{{ route('farmers.show', $farmer) }}">
    <div class="p-3 d-flex gap-3 align-items-center">
        <img class="avatar" src="{{ ImageStore::picture($farmer->logo, $farmer->stall_name) }}" alt="{{ $farmer->stall_name }}" width="72" height="72" loading="lazy" decoding="async">
        <div>
            <h3 class="h5 mb-0">{{ $farmer->stall_name }}</h3>
            <div class="small muted">{{ implode(', ', $farmer->operating_days ?? []) }}</div>
            <div class="small">{{ $farmer->products_count ?? $farmer->products->count() }} products</div>
        </div>
    </div>
</a>
