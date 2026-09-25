@extends('layouts.app')
@section('title', 'Contact')
@section('content')
<div class="contact-only">
    <p class="story-kicker">Contact</p>
    <h1 class="section-title">Send a message</h1>
    <p class="muted mb-4">Ask about markets, farmers, produce, or your pickup. We reply to the email you leave.</p>

    <div class="topic-chips mb-3" id="topicChips" role="group" aria-label="Quick topics">
        @foreach(['General Question','Market','Farmer','Product','Reservation','Other'] as $chip)
            <button type="button" class="btn btn-outline-ml btn-sm topic-chip" data-topic="{{ $chip }}">{{ $chip }}</button>
        @endforeach
    </div>

    <form method="POST" action="{{ route('contact.send') }}" class="card-ml p-4 contact-form-card is-alive" id="contactForm" novalidate>
        @csrf
        <div class="contact-progress mb-3" aria-hidden="true"><span id="contactBar"></span></div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="c-name">Name</label>
                <input id="c-name" class="form-control contact-field" name="name" required value="{{ old('name') }}" placeholder="Your name" autocomplete="name">
                <div class="invalid-feedback">Please enter your name.</div>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="c-email">Email</label>
                <input id="c-email" class="form-control contact-field" type="email" name="email" required value="{{ old('email') }}" placeholder="you@email.com" autocomplete="email">
                <div class="invalid-feedback">Enter a valid email.</div>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="c-type">Topic</label>
                <select id="c-type" class="form-select contact-field" name="type" required>
                    @foreach(['General Question','Market','Farmer','Product','Reservation','Other'] as $type)
                        <option value="{{ $type }}" @selected(old('type') === $type)>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="c-subject">Subject</label>
                <input id="c-subject" class="form-control contact-field" name="subject" value="{{ old('subject') }}" placeholder="Short subject" maxlength="120">
                <div class="small muted text-end"><span id="subjectCount">0</span>/120</div>
            </div>
            <div class="col-12">
                <label class="form-label" for="c-message">Message</label>
                <textarea id="c-message" class="form-control contact-field" name="message" rows="5" required maxlength="2000" placeholder="How can we help?">{{ old('message') }}</textarea>
                <div class="d-flex justify-content-between small muted">
                    <span id="contactHint">Tip: include your order number if this is about a pickup.</span>
                    <span><span id="messageCount">0</span>/2000</span>
                </div>
                <div class="invalid-feedback">Write a short message.</div>
            </div>
        </div>
        <div class="d-flex flex-wrap gap-2 mt-3 align-items-center">
            <button class="btn btn-ml" type="submit" id="contactSend">Send message</button>
            <button class="btn btn-outline-ml" type="reset" id="contactReset">Clear</button>
            <span class="small muted" id="contactLive" aria-live="polite"></span>
        </div>
    </form>
</div>
@endsection
@push('scripts')
<script>
(function () {
    const form = document.getElementById('contactForm');
    const bar = document.getElementById('contactBar');
    const live = document.getElementById('contactLive');
    const subject = document.getElementById('c-subject');
    const message = document.getElementById('c-message');
    const type = document.getElementById('c-type');
    const hints = {
        'General Question': 'Ask anything about MarketLink.',
        'Market': 'Share the market name and day if you can.',
        'Farmer': 'Include the stall name or phone if known.',
        'Product': 'Name the produce and quality you need.',
        'Reservation': 'Add your order number and pickup window.',
        'Other': 'Tell us what you need in a few lines.'
    };

    function progress() {
        const fields = form.querySelectorAll('.contact-field');
        let filled = 0;
        fields.forEach(function (el) {
            if (String(el.value || '').trim()) filled += 1;
        });
        const pct = Math.round((filled / fields.length) * 100);
        bar.style.width = pct + '%';
        live.textContent = pct === 100 ? 'Ready to send' : pct + '% complete';
    }

    function counts() {
        document.getElementById('subjectCount').textContent = subject.value.length;
        document.getElementById('messageCount').textContent = message.value.length;
    }

    document.querySelectorAll('.topic-chip').forEach(function (chip) {
        chip.addEventListener('click', function () {
            type.value = chip.dataset.topic;
            document.getElementById('contactHint').textContent = hints[chip.dataset.topic] || '';
            document.querySelectorAll('.topic-chip').forEach(function (c) { c.classList.remove('is-on'); });
            chip.classList.add('is-on');
            progress();
            message.focus();
        });
    });

    type.addEventListener('change', function () {
        document.getElementById('contactHint').textContent = hints[type.value] || '';
        progress();
    });

    form.addEventListener('input', function () { progress(); counts(); });
    form.addEventListener('reset', function () {
        setTimeout(function () {
            progress(); counts();
            live.textContent = '';
            document.querySelectorAll('.topic-chip').forEach(function (c) { c.classList.remove('is-on'); });
        }, 0);
    });

    form.addEventListener('submit', function (e) {
        if (!form.checkValidity()) {
            e.preventDefault();
            form.classList.add('was-validated');
            live.textContent = 'Please fill the highlighted fields.';
            return;
        }
        const button = document.getElementById('contactSend');
        button.disabled = true;
        button.textContent = 'Sending…';
    });

    progress();
    counts();
})();
</script>
@endpush
