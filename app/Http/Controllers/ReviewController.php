<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct(private NotificationService $notifications) {}

    public function store(Request $request, Order $order)
    {
        abort_unless($order->customer_id === auth()->id(), 403);
        abort_unless($order->status === 'completed', 403, 'Reviews open after the order is completed.');
        abort_if($order->reviews()->where('customer_id', auth()->id())->exists(), 422, 'You already reviewed this order.');

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
            'product_id' => ['nullable', 'integer'],
        ]);

        $review = Review::query()->create([
            'customer_id' => auth()->id(),
            'farmer_id' => $order->farmer_id,
            'product_id' => $data['product_id'] ?? null,
            'order_id' => $order->id,
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
            'status' => 'approved',
        ]);

        if ($order->farmer->user) {
            $this->notifications->send(
                $order->farmer->user,
                'new_review',
                'New review',
                auth()->user()->name.' left a '.$review->rating.'-star review.',
                ['review_id' => $review->id]
            );
        }

        return back()->with('success', 'Thanks for your review.');
    }

    public function helpful(Review $review)
    {
        $key = 'helpful_reviews';
        $voted = session($key, []);
        if (! in_array($review->id, $voted, true)) {
            $review->increment('helpful_count');
            session([$key => [...$voted, $review->id]]);
        }

        return back();
    }
}
