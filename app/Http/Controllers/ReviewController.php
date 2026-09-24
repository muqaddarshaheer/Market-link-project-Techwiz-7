<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Favorite;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function create(Order $order)
    {
        abort_unless($order->customer_id === auth()->id() && $order->canReview(), 403);
        $order->load(['items.product', 'farmer']);

        return view('reviews.create', compact('order'));
    }

    public function store(Request $request, Order $order)
    {
        abort_unless($order->customer_id === auth()->id() && $order->canReview(), 403);

        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
            'product_id' => 'nullable|exists:products,id',
        ]);

        $review = Review::create([
            'customer_id' => auth()->id(),
            'farmer_id' => $order->farmer_id,
            'product_id' => $data['product_id'] ?? $order->items->first()?->product_id,
            'order_id' => $order->id,
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
            'status' => 'approved',
        ]);

        AppNotification::notify(
            $order->farmer->user,
            'new_review',
            'New review received',
            auth()->user()->name.' left a '.$data['rating'].'-star review.',
            ['review_id' => $review->id]
        );

        return redirect()->route('customer.orders.show', $order)->with('success', 'Thank you for your review!');
    }

    public function helpful(Review $review)
    {
        $review->increment('helpful_count');

        return back()->with('success', 'Marked as helpful.');
    }
}
