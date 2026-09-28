@extends('layouts.farmer')
@section('title', 'Reviews')
@section('content')
<div class="panel-easy-head">
    <div>
        <p class="panel-kicker mb-1" data-i18n="rev.kicker">Sales</p>
        <h1 class="section-title mb-1" data-i18n="rev.title">Reviews</h1>
        <p class="muted mb-0">Pickup feedback — reply in one tap.</p>
    </div>
</div>
@if($reviews->isNotEmpty())
    <div class="ml-review-grid ml-review-grid--panel">
        @foreach($reviews as $review)
            @include('partials.review-card', ['review' => $review, 'variant' => 'panel', 'showReplyForm' => true])
        @endforeach
    </div>
    <div class="mt-3">{{ $reviews->links() }}</div>
@else
    <div class="empty-state card-ml panel-card"><i class="bi bi-star"></i><p>No reviews yet. Completed pickups will show ratings here.</p></div>
@endif
@endsection
