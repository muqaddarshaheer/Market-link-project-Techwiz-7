@extends('layouts.app')
@section('title', 'Guest checkout')
@section('content')
@php
    use App\Support\ImageStore;
    $count = 0;
    $total = 0;
    foreach ($products as $product) {
        $qty = (int) ($lines[$product->id] ?? 0);
        if ($qty > 0) {
            $count += $qty;
            $total += $qty * (float) $product->price;
        }
    }
@endphp

<section class="guest-checkout" x-data="guestCheckout()">
    <header class="guest-checkout-hero">
        <p class="guest-checkout-kicker">No account · pay at stall</p>
        <h1 class="guest-checkout-title">Guest checkout</h1>
        <p class="guest-checkout-lead">Three quick steps. After you confirm, your Order ID and farmer chat open right away.</p>
        <ol class="guest-checkout-steps" aria-label="Checkout steps">
            <li :class="{ 'is-active': step === 1, 'is-done': step > 1 }"><span>1</span> You</li>
            <li :class="{ 'is-active': step === 2, 'is-done': step > 2 }"><span>2</span> Pickup</li>
            <li :class="{ 'is-active': step === 3 }"><span>3</span> Confirm</li>
        </ol>
    </header>

    @if($count < 1)
        <div class="empty-state card-ml">
            <i class="bi bi-basket"></i>
            <p>Your guest basket is empty.</p>
            <a class="btn btn-ml" href="{{ route('products.index') }}">Browse produce</a>
        </div>
    @else
        <div class="guest-checkout-layout">
            <form method="POST" action="{{ route('guest.checkout.store') }}" class="guest-checkout-form card-ml" id="guestForm" @submit="submitting = true">
                @csrf

                <div class="guest-step" x-show="step === 1" x-cloak>
                    <h2 class="guest-step-title"><i class="bi bi-person-badge"></i> Who should the farmer reach?</h2>
                    <p class="guest-step-hint">Used only for this pre-order — no account is created.</p>
                    <div class="guest-fields">
                        <label class="guest-field">
                            <span>Full name</span>
                            <input class="form-control" name="name" value="{{ old('name') }}" required autocomplete="name" placeholder="e.g. Ayesha Khan" x-model="name">
                        </label>
                        <label class="guest-field">
                            <span>Phone / WhatsApp</span>
                            <input class="form-control" name="phone" value="{{ old('phone') }}" required autocomplete="tel" inputmode="tel" placeholder="03XX XXXXXXX" x-model="phone">
                        </label>
                        <label class="guest-field guest-field--full">
                            <span>Where can they reach you</span>
                            <input class="form-control" name="address" value="{{ old('address') }}" required autocomplete="street-address" placeholder="Neighborhood, landmark, or stall meetup note" x-model="address">
                        </label>
                    </div>
                    <div class="guest-step-nav">
                        <button class="btn btn-ml" type="button" @click="go(2)" :disabled="!canStep1">Continue to pickup</button>
                    </div>
                </div>

                <div class="guest-step" x-show="step === 2" x-cloak>
                    <h2 class="guest-step-title"><i class="bi bi-clock-history"></i> Pick a stall window</h2>
                    <p class="guest-step-hint">Choose when you’ll collect — pay the farmer in person.</p>
                    <div class="guest-fields">
                        <label class="guest-field">
                            <span>Pickup date</span>
                            <input class="form-control" type="date" name="pickup_date" value="{{ old('pickup_date', now()->addDay()->toDateString()) }}" min="{{ now()->toDateString() }}" required x-model="pickupDate">
                        </label>
                        <label class="guest-field">
                            <span>Pickup slot</span>
                            <div class="guest-slot-grid">
                                @foreach($slots as $slot)
                                    <label class="guest-slot">
                                        <input type="radio" name="pickup_slot" value="{{ $slot }}" @checked(old('pickup_slot', $slots->first()) === $slot) x-model="pickupSlot" required>
                                        <span>{{ $slot }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </label>
                        <label class="guest-field guest-field--full">
                            <span>Note for farmer <em>(optional)</em></span>
                            <textarea class="form-control" name="customer_note" rows="2" placeholder="Ripe tomatoes, bag separately…">{{ old('customer_note') }}</textarea>
                        </label>
                    </div>
                    <div class="guest-step-nav">
                        <button class="btn btn-outline-ml" type="button" @click="step = 1">Back</button>
                        <button class="btn btn-ml" type="button" @click="go(3)" :disabled="!canStep2">Review order</button>
                    </div>
                </div>

                <div class="guest-step" x-show="step === 3" x-cloak>
                    <h2 class="guest-step-title"><i class="bi bi-check2-circle"></i> Confirm &amp; open chat</h2>
                    <p class="guest-step-hint">You’ll land on your Order ID page with farmer chat — not the homepage.</p>
                    <dl class="guest-review">
                        <div><dt>Name</dt><dd x-text="name || '—'"></dd></div>
                        <div><dt>Phone</dt><dd x-text="phone || '—'"></dd></div>
                        <div><dt>Reach</dt><dd x-text="address || '—'"></dd></div>
                        <div><dt>Pickup</dt><dd><span x-text="pickupDate || '—'"></span> · <span x-text="pickupSlot || '—'"></span></dd></div>
                        <div><dt>Pay</dt><dd>{{ money($total) }} at the stall</dd></div>
                    </dl>
                    <div class="guest-step-nav">
                        <button class="btn btn-outline-ml" type="button" @click="step = 2">Back</button>
                        <button class="btn btn-ml" id="guestBtn" type="submit" :disabled="submitting">
                            <span x-show="!submitting"><i class="bi bi-send-check"></i> Place pre-order</span>
                            <span x-show="submitting" x-cloak>Placing order…</span>
                        </button>
                    </div>
                </div>
            </form>

            <aside class="guest-checkout-summary card-ml">
                <h2 class="h6 mb-3">Basket summary</h2>
                <ul class="guest-summary-list">
                    @foreach($products as $product)
                        @php $qty = (int) ($lines[$product->id] ?? 0); @endphp
                        @if($qty > 0)
                            <li>
                                <img src="{{ ImageStore::picture($product->image, $product->name) }}" alt="" width="48" height="48" loading="lazy">
                                <div>
                                    <strong>{{ $product->name }}</strong>
                                    <span class="muted small">{{ $qty }} × {{ money($product->price) }} · {{ $product->farmer->stall_name ?? 'Stall' }}</span>
                                </div>
                                <span>{{ money($qty * $product->price) }}</span>
                            </li>
                        @endif
                    @endforeach
                </ul>
                <div class="guest-summary-total">
                    <span>{{ $count }} {{ Str::plural('item', $count) }}</span>
                    <strong>{{ money($total) }}</strong>
                </div>
                <p class="small muted mb-0 mt-2"><i class="bi bi-shield-check"></i> No login. Order chat + slip after confirm.</p>
                <a class="btn btn-outline-ml btn-sm w-100 mt-3" href="{{ route('guest.cart') }}">Edit basket</a>
            </aside>
        </div>
    @endif
</section>
@endsection
@push('scripts')
<script>
function guestCheckout() {
    return {
        step: {{ $errors->any() ? 1 : 1 }},
        name: @json(old('name', '')),
        phone: @json(old('phone', '')),
        address: @json(old('address', '')),
        pickupDate: @json(old('pickup_date', now()->addDay()->toDateString())),
        pickupSlot: @json(old('pickup_slot', $slots->first())),
        submitting: false,
        get canStep1() {
            return this.name.trim().length > 1 && this.phone.trim().length > 5 && this.address.trim().length > 2;
        },
        get canStep2() {
            return !!this.pickupDate && !!this.pickupSlot;
        },
        go(n) {
            if (n === 2 && !this.canStep1) return;
            if (n === 3 && !this.canStep2) return;
            this.step = n;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    };
}
@if($errors->any())
document.addEventListener('DOMContentLoaded', function () {
    // Stay on step 1 if validation failed so fields are visible
});
@endif
</script>
@endpush
