@extends('layouts.app')
@section('title', 'Write a review')
@section('content')
<div class="container py-5">
    <h1 class="display-font h3">Review order {{ $order->order_number }}</h1>
    <form method="POST" action="{{ route('customer.reviews.store', $order) }}" class="panel col-lg-6">
        @csrf
        <div class="mb-3">
            <label class="form-label">Product (optional focus)</label>
            <select name="product_id" class="form-select">
                @foreach($order->items as $item)
                    <option value="{{ $item->product_id }}">{{ $item->product_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Rating</label>
            <select name="rating" class="form-select" required>
                @for($i=5;$i>=1;$i--)
                    <option value="{{ $i }}">{{ $i }} star{{ $i>1?'s':'' }}</option>
                @endfor
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Comment</label>
            <textarea name="comment" class="form-control" rows="4">{{ old('comment') }}</textarea>
        </div>
        <button class="btn btn-primary">Submit review</button>
    </form>
</div>
@endsection
