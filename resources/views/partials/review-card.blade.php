@php
    $variant = $variant ?? 'full';
    $showReplyForm = !empty($showReplyForm);
    $showModeration = !empty($showModeration);
    $showStatus = !empty($showStatus);
    $customerName = $review->customer->name ?? 'Customer';
    $initial = mb_strtoupper(mb_substr($customerName, 0, 1));
    $tone = (abs(crc32($customerName)) % 4) + 1;
    $productName = $review->product->name ?? null;
    $stallName = $review->farmer->stall_name ?? null;
    $dateLabel = $review->created_at?->format('M j, Y');
    $score = round((float) $review->rating, 1);
    $helpful = (int) ($review->helpful_count ?? 0);
    $classes = trim('ml-review ml-review--'.$variant.($showReplyForm ? ' ml-review--replyable' : ''));
@endphp
<article class="{{ $classes }}">
    <div class="ml-review__glow" aria-hidden="true"></div>
    <header class="ml-review__head">
        <div class="ml-review__who">
            <span class="ml-review__avatar ml-review__avatar--{{ $tone }}" aria-hidden="true">{{ $initial }}</span>
            <div class="ml-review__who-copy">
                <strong class="ml-review__name">{{ $customerName }}</strong>
                <div class="ml-review__chips">
                    @if($productName)
                        <span class="ml-review__chip"><i class="bi bi-basket2" aria-hidden="true"></i>{{ $productName }}</span>
                    @endif
                    @if($stallName && $variant !== 'product')
                        <span class="ml-review__chip ml-review__chip--stall"><i class="bi bi-shop-window" aria-hidden="true"></i>{{ $stallName }}</span>
                    @endif
                    @if($dateLabel)
                        <time class="ml-review__chip ml-review__chip--muted" datetime="{{ $review->created_at?->toDateString() }}">{{ $dateLabel }}</time>
                    @endif
                    @if($showStatus && $review->status)
                        <span class="ml-review__chip ml-review__chip--status">{{ $review->status }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="ml-review__score" title="{{ $score }} out of 5">
            <span class="ml-review__score-num">{{ number_format($score, 1) }}</span>
            @include('partials.star-rating', ['rating' => $review->rating])
        </div>
    </header>

    <blockquote class="ml-review__body">
        <span class="ml-review__mark" aria-hidden="true">“</span>
        <p>{{ $review->comment }}</p>
    </blockquote>

    @if($review->farmer_reply)
        <div class="ml-review__reply">
            <div class="ml-review__reply-label">
                <i class="bi bi-chat-left-quote-fill" aria-hidden="true"></i>
                Stall reply{{ $stallName ? ' · '.$stallName : '' }}
            </div>
            <p>{{ $review->farmer_reply }}</p>
        </div>
    @endif

    <div class="ml-review__bottom">
        <footer class="ml-review__foot">
            @if($helpful > 0)
                <span class="ml-review__helpful"><i class="bi bi-hand-thumbs-up-fill" aria-hidden="true"></i> {{ $helpful }} found this helpful</span>
            @else
                <span class="ml-review__helpful ml-review__helpful--quiet"><i class="bi bi-patch-check" aria-hidden="true"></i> Verified pickup</span>
            @endif

            @if($showModeration)
                <div class="ml-review__actions">
                    <form method="POST" action="{{ route('admin.reviews.moderate', $review) }}" class="ml-review__mod">
                        @csrf
                        <select class="form-select form-select-sm" name="status" aria-label="Review status">
                            <option @selected($review->status === 'approved')>approved</option>
                            <option @selected($review->status === 'pending')>pending</option>
                            <option @selected($review->status === 'rejected')>rejected</option>
                        </select>
                        <button class="btn btn-ml btn-sm" type="submit">Update</button>
                    </form>
                    <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" onsubmit="return confirm('Delete this review?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger btn-sm" type="submit">Delete</button>
                    </form>
                </div>
            @endif
        </footer>

        @if($showReplyForm)
            <form method="POST" action="{{ route('farmer.reviews.reply', $review) }}" class="ml-review__form">
                @csrf
                <label class="visually-hidden" for="reply-{{ $review->id }}">Reply to review</label>
                <textarea id="reply-{{ $review->id }}" class="form-control" name="farmer_reply" rows="2" required data-i18n-placeholder="rev.replyPh" placeholder="Write your reply…">{{ $review->farmer_reply }}</textarea>
                <button class="btn btn-ml btn-sm" data-i18n="rev.reply" type="submit">Reply</button>
            </form>
        @endif
    </div>
</article>
