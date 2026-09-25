@php
    $slides = $slides ?? [
        ['img' => 'images/produce/farm.jpg', 'title' => 'Fresh from the field', 'text' => 'Reserve produce in Rs and pick up at the stall.'],
        ['img' => 'images/produce/market.jpg', 'title' => 'Local market days', 'text' => 'Browse growers, markets, and seasonal harvests.'],
        ['img' => 'images/produce/field.jpg', 'title' => 'Trusted stalls', 'text' => 'Approved farmers pack orders the morning of pickup.'],
    ];
    $carouselId = $carouselId ?? ('panelCarousel'.substr(md5(($slides[0]['title'] ?? 'ml').microtime()), 0, 8));
@endphp
<div class="panel-banner mb-4">
    <div id="{{ $carouselId }}" class="carousel slide panel-carousel" data-bs-ride="carousel" data-bs-interval="4500">
        <div class="carousel-indicators">
            @foreach($slides as $i => $slide)
                <button type="button" data-bs-target="#{{ $carouselId }}" data-bs-slide-to="{{ $i }}" @class(['active' => $i === 0]) aria-current="{{ $i === 0 ? 'true' : 'false' }}" aria-label="Slide {{ $i + 1 }}"></button>
            @endforeach
        </div>
        <div class="carousel-inner">
            @foreach($slides as $i => $slide)
                <div @class(['carousel-item', 'active' => $i === 0])>
                    <img src="{{ asset($slide['img']) }}" alt="" loading="{{ $i === 0 ? 'eager' : 'lazy' }}" width="1200" height="280" decoding="async">
                    <div class="panel-banner-copy">
                        <strong>{{ $slide['title'] }}</strong>
                        <span>{{ $slide['text'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#{{ $carouselId }}" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#{{ $carouselId }}" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</div>
