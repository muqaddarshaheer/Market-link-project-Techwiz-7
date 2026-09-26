@extends('layouts.admin')
@section('title', 'Reviews')
@section('content')
<div class="panel-head mb-4">
    <div>
        <p class="panel-kicker mb-1">Trust</p>
        <h1 class="section-title mb-1">Review moderation</h1>
        <p class="muted mb-0">Approve helpful feedback and keep stall reputations fair.</p>
    </div>
</div>
@forelse($reviews as $review)
<article class="review-admin-card card-ml panel-card p-4 mb-3">
    <div class="d-flex justify-content-between gap-3 flex-wrap mb-2">
        <div>
            @include('partials.star-rating', ['rating'=>$review->rating])
            <h2 class="h6 mb-1 mt-2">{{ $review->farmer->stall_name ?? 'Stall' }}@if($review->product) · {{ $review->product->name }}@endif</h2>
            <div class="small muted">{{ $review->customer->name ?? 'Customer' }} · {{ $review->created_at?->diffForHumans() }}</div>
        </div>
        <span class="badge badge-soft text-capitalize">{{ $review->status }}</span>
    </div>
    <p class="review-admin-body mb-3">{{ $review->comment }}</p>
    @if($review->farmer_reply)
        <div class="review-admin-reply mb-3"><strong>Farmer reply:</strong> {{ $review->farmer_reply }}</div>
    @endif
    <div class="d-flex gap-2 flex-wrap align-items-center">
        <form method="POST" action="{{ route('admin.reviews.moderate', $review) }}" class="d-flex gap-2 flex-wrap">
            @csrf
            <select class="form-select" name="status" style="max-width:160px">
                <option @selected($review->status==='approved')>approved</option>
                <option @selected($review->status==='pending')>pending</option>
                <option @selected($review->status==='rejected')>rejected</option>
            </select>
            <button class="btn btn-ml btn-sm" type="submit">Update</button>
        </form>
        <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" onsubmit="return confirm('Delete this review?')">
            @csrf @method('DELETE')
            <button class="btn btn-outline-danger btn-sm" type="submit">Delete</button>
        </form>
    </div>
</article>
@empty
    <div class="empty-state card-ml panel-card"><i class="bi bi-chat-quote"></i><p>No reviews yet.</p></div>
@endforelse
{{ $reviews->links() }}
@endsection
