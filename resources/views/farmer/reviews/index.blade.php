@extends('layouts.app')
@section('title', 'Reviews')
@section('content')
<div class="container py-5">
    <h1 class="display-font h3 mb-4">Customer reviews</h1>
    @foreach($reviews as $review)
        <div class="panel mb-3">
            <div class="star-rating">{{ str_repeat('★', $review->rating) }}</div>
            <p>{{ $review->comment }}</p>
            <small class="text-muted">{{ $review->customer->name }} on {{ $review->product->name ?? 'order' }}</small>
            @if($review->farmer_reply)
                <div class="mt-2 p-2 rounded" style="background:var(--ml-cream)">{{ $review->farmer_reply }}</div>
            @else
                <form method="POST" action="{{ route('farmer.reviews.reply', $review) }}" class="mt-2">
                    @csrf
                    <div class="input-group"><input class="form-control" name="farmer_reply" placeholder="Reply..." required><button class="btn btn-outline-primary">Reply</button></div>
                </form>
            @endif
        </div>
    @endforeach
    {{ $reviews->links() }}
</div>
@endsection
