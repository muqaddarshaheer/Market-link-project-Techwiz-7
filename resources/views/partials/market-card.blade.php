@php use App\Support\ImageStore; @endphp
<a class="card-ml text-decoration-none text-reset d-block h-100" href="{{ route('markets.show', $market) }}">
    <div class="thumb">
        @if($market->image)<img src="{{ ImageStore::url($market->image) }}" alt="">@else<i class="bi bi-geo-alt"></i>@endif
    </div>
    <div class="p-3">
        <h3 class="h5">{{ $market->name }}</h3>
        <div class="small muted">{{ $market->city }} · {{ implode(', ', $market->operating_days ?? []) }}</div>
        <div class="small mt-1">{{ $market->hoursLabel() }}</div>
    </div>
</a>
