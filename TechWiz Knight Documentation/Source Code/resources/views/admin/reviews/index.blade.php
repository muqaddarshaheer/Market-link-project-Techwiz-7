@extends('layouts.admin')
@section('title', 'Reviews')
@section('content')
<div class="panel-easy-head">
    <div>
        <p class="panel-kicker mb-1">Trust</p>
        <h1 class="section-title mb-1">Review moderation</h1>
        <p class="muted mb-0">Approve helpful feedback — keep stall ratings fair.</p>
    </div>
</div>
@if($reviews->isNotEmpty())
    <div class="ml-review-grid ml-review-grid--admin">
        @foreach($reviews as $review)
            @include('partials.review-card', [
                'review' => $review,
                'variant' => 'admin',
                'showStatus' => true,
                'showModeration' => true,
            ])
        @endforeach
    </div>
    <div class="mt-3">{{ $reviews->links() }}</div>
@else
    <div class="empty-state card-ml panel-card"><i class="bi bi-chat-quote"></i><p>No reviews yet.</p></div>
@endif
@endsection
