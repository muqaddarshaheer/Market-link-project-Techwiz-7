@php use App\Support\ImageStore; @endphp
<a class="card-ml market-card text-decoration-none text-reset d-block h-100" href="{{ route('markets.show', $market) }}">
    <div class="thumb">
        <img src="{{ ImageStore::picture($market->image, $market->name) }}" alt="{{ $market->name }}" width="640" height="420" loading="lazy" decoding="async">
    </div>
    <div class="market-card-body">
        <h3 class="market-card-title">{{ $market->name }}</h3>
        <p class="market-card-meta">{{ $market->city }} · {{ implode(', ', $market->operating_days ?? []) }}</p>
        <span class="market-card-hours">{{ $market->hoursLabel() }}</span>
    </div>
</a>
