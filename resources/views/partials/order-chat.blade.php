{{-- Order chat box: customer/guest ↔ farmer, tied to Order ID --}}
@php
    $postRoute = $postRoute ?? null;
    $slipRoute = $slipRoute ?? null;
    $asGuest = (bool) ($asGuest ?? false);
    $messages = ($order->relationLoaded('messages')
        ? $order->messages
        : $order->messages()->with('sender')->latest()->get()
    )->sortBy('created_at');
    $peerLabel = $peerLabel ?? 'the other party';
@endphp
<section class="order-chat card-ml p-3 mt-3" id="order-chat" aria-labelledby="order-chat-title">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-2">
        <div>
            <h2 class="h6 mb-1" id="order-chat-title"><i class="bi bi-chat-dots"></i> Order chat</h2>
            <p class="small muted mb-0">
                Chat about <strong>{{ $order->order_number }}</strong> with {{ $peerLabel }}.
                Messages are saved with this order.
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @if($slipRoute)
                <a class="btn btn-outline-ml btn-sm" href="{{ $slipRoute }}" target="_blank" rel="noopener">
                    <i class="bi bi-receipt"></i> Order slip
                </a>
            @endif
            <span class="badge text-bg-light border align-self-center">ID: {{ $order->order_number }}</span>
        </div>
    </div>

    <div class="order-chat-thread" id="orderChatThread">
        @forelse($messages as $msg)
            @php $mine = $msg->isMine(null, $asGuest); @endphp
            <div class="order-chat-bubble {{ $mine ? 'is-mine' : 'is-theirs' }}">
                <div class="order-chat-meta">
                    <strong>{{ $mine ? 'You' : $msg->senderLabel() }}</strong>
                    <span>{{ $msg->created_at->format('M j, g:i A') }}</span>
                </div>
                <div class="order-chat-body">{{ $msg->body }}</div>
            </div>
        @empty
            <div class="order-chat-empty muted small">
                No messages yet. Say hello and mention your Order ID
                <strong>{{ $order->order_number }}</strong> if you call or WhatsApp.
            </div>
        @endforelse
    </div>

    @if($postRoute)
        <form method="POST" action="{{ $postRoute }}" class="order-chat-form mt-3" id="orderChatForm">
            @csrf
            <label class="visually-hidden" for="orderChatBody">Message</label>
            <textarea
                id="orderChatBody"
                class="form-control"
                name="body"
                rows="2"
                maxlength="2000"
                required
                placeholder="Write about pickup, quantity, or timing… (Order {{ $order->order_number }})"
            >{{ old('body') }}</textarea>
            @error('body')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-2">
                <span class="small muted"><i class="bi bi-shield-check"></i> Stored on this order only</span>
                <button class="btn btn-ml btn-sm" type="submit"><i class="bi bi-send"></i> Send</button>
            </div>
        </form>
    @endif
</section>
<script>
    (function () {
        var thread = document.getElementById('orderChatThread');
        if (thread) thread.scrollTop = thread.scrollHeight;
        if (window.location.hash === '#order-chat') {
            document.getElementById('order-chat')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    })();
</script>
