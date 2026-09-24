@extends('layouts.app')
@section('title', 'Reviews')
@section('content')
<h1 class="section-title">Review moderation</h1>
@foreach($reviews as $review)
<div class="card-ml p-3 mb-2">
    @include('partials.star-rating', ['rating'=>$review->rating])
    <p>{{ $review->comment }}</p>
    <div class="small muted">{{ $review->customer->name }} · {{ $review->farmer->stall_name ?? '' }} · {{ $review->status }}</div>
    <form method="POST" action="{{ route('admin.reviews.moderate', $review) }}" class="d-flex gap-2">@csrf
        <select class="form-select" name="status"><option @selected($review->status==='approved')>approved</option><option @selected($review->status==='pending')>pending</option><option @selected($review->status==='rejected')>rejected</option></select>
        <button class="btn btn-outline-ml">Update</button>
    </form>
    <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}">@csrf @method('DELETE')<button class="btn btn-link text-danger">Delete</button></form>
</div>
@endforeach
{{ $reviews->links() }}
@endsection
