@extends('layouts.app')
@section('title', 'Moderate reviews')
@section('content')
<div class="container py-4">
    <h1 class="h3 display-font mb-3">Reviews</h1>
    <form method="GET" class="mb-3">
        <select name="status" class="form-select w-auto" onchange="this.form.submit()">
            <option value="">All</option>
            @foreach(['pending','approved','rejected'] as $s)<option value="{{ $s }}" @selected(request('status')==$s)>{{ $s }}</option>@endforeach
        </select>
    </form>
    @foreach($reviews as $review)
        <div class="panel mb-2">
            <div class="d-flex justify-content-between">
                <div>
                    <div class="star-rating">{{ str_repeat('★',$review->rating) }}</div>
                    <p class="mb-1">{{ $review->comment }}</p>
                    <small class="text-muted">{{ $review->customer->name }} · {{ $review->farmer->stall_name ?? '' }} · {{ $review->status }}</small>
                </div>
                <form method="POST" action="{{ route('admin.reviews.moderate', $review) }}" class="d-flex gap-1">
                    @csrf
                    <select name="status" class="form-select form-select-sm">
                        @foreach(['pending','approved','rejected'] as $s)<option value="{{ $s }}" @selected($review->status==$s)>{{ $s }}</option>@endforeach
                    </select>
                    <button class="btn btn-sm btn-primary">Save</button>
                </form>
            </div>
        </div>
    @endforeach
    {{ $reviews->links() }}
</div>
@endsection
