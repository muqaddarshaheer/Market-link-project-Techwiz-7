@extends('layouts.customer')
@section('title', 'Your reviews')
@section('content')
<div class="panel-head mb-4">
    <div>
        <p class="panel-kicker mb-1">Feedback</p>
        <h1 class="section-title mb-0">Your reviews</h1>
    </div>
</div>
@forelse($reviews as $review)
    <article class="panel-list-item card-ml panel-card p-3 mb-2">
        @include('partials.star-rating', ['rating' => $review->rating])
        <div class="mt-2">{{ $review->comment }}</div>
        <div class="small muted">{{ $review->product->name ?? $review->farmer->stall_name ?? 'Order' }} · {{ $review->status }}</div>
    </article>
@empty
    <div class="empty-state card-ml panel-card"><i class="bi bi-star"></i><p>Reviews appear here after you rate a completed pickup.</p></div>
@endforelse
{{ $reviews->links() }}
@endsection
