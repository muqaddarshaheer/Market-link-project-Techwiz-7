@php $rounded = (int) round($rating ?? 0); @endphp
<div class="star" aria-label="Rating {{ $rating ?? 0 }} out of 5">
    @for($i = 1; $i <= 5; $i++)
        <i class="bi {{ $i <= $rounded ? 'bi-star-fill' : 'bi-star' }}"></i>
    @endfor
    @if(isset($count))<span class="small muted">{{ $count }}</span>@endif
</div>
