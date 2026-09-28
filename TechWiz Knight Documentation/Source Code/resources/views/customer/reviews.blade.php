@extends('layouts.customer')
@section('title', 'Your reviews')
@section('content')
<div class="panel-easy-head">
    <div>
        <p class="panel-kicker mb-1">Feedback</p>
        <h1 class="section-title mb-0">Your reviews</h1>
    </div>
</div>
@if($reviews->isNotEmpty())
    <div class="ml-review-grid ml-review-grid--panel">
        @foreach($reviews as $review)
            @include('partials.review-card', ['review' => $review, 'variant' => 'panel', 'showStatus' => true])
        @endforeach
    </div>
    <div class="mt-3">{{ $reviews->links() }}</div>
@else
    <div class="empty-state card-ml panel-card"><i class="bi bi-star"></i><p>Reviews appear here after you rate a completed pickup.</p></div>
@endif
@endsection
