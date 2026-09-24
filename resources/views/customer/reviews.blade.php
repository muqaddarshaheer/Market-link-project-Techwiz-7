@extends('layouts.customer')
@section('title', 'Your reviews')
@section('content')
<h1 class="section-title">Your reviews</h1>
@forelse($reviews as $review)
    <article class="card-ml p-3 mb-2">
        @include('partials.star-rating', ['rating' => $review->rating])
        <div class="mt-2">{{ $review->comment }}</div>
        <div class="small muted">{{ $review->product->name ?? $review->farmer->stall_name ?? 'Order' }} · {{ $review->status }}</div>
    </article>
@empty
    <p class="muted">Reviews appear here after you rate a completed pickup.</p>
@endforelse
{{ $reviews->links() }}
@endsection
