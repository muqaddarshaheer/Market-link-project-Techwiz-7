@php use App\Support\ImageStore; @endphp
<a class="card-ml text-decoration-none text-reset d-block h-100" href="{{ route('markets.show', $market) }}">
    <div class="thumb">
        <img src="{{ ImageStore::picture($market->image, $market->name) }}" alt="{{ $market->name }}" width="640" height="420" loading="lazy" decoding="async">
        <div class="thumb-label">{{ $market->name }}</div>
    </div>
    <div class="p-3">
        <h3 class="h5">{{ $market->name }}</h3>
        <div class="small muted">{{ $market->city }} · {{ implode(', ', $market->operating_days ?? []) }}</div>
        <div class="small mt-1">{{ $market->hoursLabel() }}</div>
    </div>
</a>
