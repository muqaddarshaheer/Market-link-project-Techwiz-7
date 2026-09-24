@extends('layouts.app')
@section('title', 'Reviews')
@section('content')
<h1 class="section-title">Reviews</h1>
@foreach($reviews as $review)
<div class="card-ml p-3 mb-3">
    @include('partials.star-rating', ['rating'=>$review->rating])
    <p>{{ $review->comment }}</p>
    <div class="small muted">{{ $review->customer->name }} @if($review->product)· {{ $review->product->name }}@endif</div>
    @if($review->farmer_reply)<p class="timeline-note">{{ $review->farmer_reply }}</p>@endif
    <form method="POST" action="{{ route('farmer.reviews.reply', $review) }}">@csrf
        <textarea class="form-control mb-2" name="farmer_reply" required>{{ $review->farmer_reply }}</textarea>
        <button class="btn btn-ml btn-sm">Reply</button>
    </form>
</div>
@endforeach
{{ $reviews->links() }}
@endsection
