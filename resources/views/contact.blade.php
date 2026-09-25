@extends('layouts.app')
@section('title', 'Contact')
@section('content')
<div class="story-page">
    <section class="mb-4">
        <p class="story-kicker">Contact</p>
        <h1 class="section-title">Let’s Connect.</h1>
        <p>Have a question about a market, farmer, seasonal produce or your pickup? We’d love to hear from you.</p>
    </section>
    <div class="row g-4 mb-4">
        <div class="col-lg-5">
            <div class="card-ml p-4 h-100">
                <h2 class="h5">MarketLink</h2>
                <p class="mb-1">{{ $address }}</p>
                <p class="mb-3"><a href="mailto:{{ $email }}">{{ $email }}</a><br>{{ $phone }}</p>
                <article class="contact-note"><h3 class="h6">Market Support</h3><p class="mb-0">Questions about markets, products or reservations.</p></article>
                <article class="contact-note"><h3 class="h6">Farmer Support</h3><p class="mb-0">For growers participating in MarketLink.</p></article>
                <article class="contact-note"><h3 class="h6">General Questions</h3><p class="mb-0">Anything else about using MarketLink.</p></article>
            </div>
        </div>
        <div class="col-lg-7">
            <form method="POST" action="{{ route('contact.send') }}" class="card-ml p-4" id="contactForm">
                @csrf
                <p class="mb-2"><strong>Tell us what you’re looking for</strong></p>
                <div class="topic-chips mb-3" role="group" aria-label="Topics">
                    @foreach(['Farmers' => 'Farmer', 'Produce' => 'Product', 'Market Pickup' => 'Reservation', 'Market Days' => 'Market', 'Reservations' => 'Reservation', 'General Help' => 'General Question'] as $label => $type)
                        <button type="button" class="topic-chip" data-subject="{{ $label }}" data-type="{{ $type }}">{{ $label }}</button>
                    @endforeach
                </div>
                <div class="mb-2"><label class="form-label" for="c-name">Full Name</label><input id="c-name" class="form-control" name="name" required value="{{ old('name') }}"></div>
                <div class="mb-2"><label class="form-label" for="c-email">Email Address</label><input id="c-email" class="form-control" type="email" name="email" required value="{{ old('email') }}"></div>
                <div class="row g-2">
                    <div class="col-md-6 mb-2"><label class="form-label" for="c-type">Contact type</label>
                        <select id="c-type" class="form-select" name="type">
                            @foreach(['General Question','Market','Farmer','Product','Reservation','Other'] as $type)
                                <option @selected(old('type') === $type)>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-2"><label class="form-label" for="c-subject">Subject</label><input id="c-subject" class="form-control" name="subject" value="{{ old('subject') }}"></div>
                </div>
                <div class="mb-3"><label class="form-label" for="c-message">Message</label><textarea id="c-message" class="form-control" name="message" rows="4" required>{{ old('message') }}</textarea></div>
                <button class="btn btn-ml" type="submit" id="contactSend">Send Message →</button>
            </form>
        </div>
    </div>
    <section class="mb-4" aria-label="Market scenes">
        <div id="contactBanner" class="carousel slide contact-banner" data-bs-ride="carousel">
            <div class="carousel-inner">
                @foreach([
                    ['farm.jpg', 'The field', 'Produce is grown for a weekend stall, not shipped.'],
                    ['market.jpg', 'The market', 'Reserve a basket, then collect it at the stall.'],
                    ['field.jpg', 'The season', 'What you see depends on what is growing this week.'],
                ] as $i => [$file, $title, $line])
                    <div class="carousel-item {{ $i ? '' : 'active' }}">
                        <img src="{{ asset('images/produce/'.$file) }}" alt="{{ $title }}" width="1200" height="480" loading="{{ $i ? 'lazy' : 'eager' }}" decoding="async">
                        <div class="contact-banner-copy">
                            <h2 class="h4 mb-1">{{ $title }}</h2>
                            <p class="mb-0">{{ $line }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#contactBanner" data-bs-slide="prev"><span class="visually-hidden">Previous</span></button>
            <button class="carousel-control-next" type="button" data-bs-target="#contactBanner" data-bs-slide="next"><span class="visually-hidden">Next</span></button>
        </div>
    </section>
    @include('partials.map', ['id' => 'contact-map', 'points' => [['lat' => 30.2672, 'lng' => -97.7431, 'title' => 'MarketLink', 'subtitle' => $address]]])
    <section class="card-ml story-cta p-4 p-md-5 mt-4">
        <h2 class="section-title">Your Local Market Is Closer Than You Think.</h2>
        <p>Discover growers, explore seasonal produce and find your next local pickup with MarketLink.</p>
        <a class="btn btn-ml" href="{{ route('markets.index') }}">Explore Markets →</a>
    </section>
</div>
@endsection
@push('scripts')
<script>
(function () {
    const form = document.getElementById('contactForm');
    const subject = document.getElementById('c-subject');
    const type = document.getElementById('c-type');
    document.querySelectorAll('.topic-chip').forEach(function (chip) {
        chip.addEventListener('click', function () {
            document.querySelectorAll('.topic-chip').forEach(function (item) { item.classList.remove('is-on'); });
            chip.classList.add('is-on');
            if (subject) subject.value = chip.getAttribute('data-subject');
            if (type) type.value = chip.getAttribute('data-type');
        });
    });
    if (form) {
        form.addEventListener('submit', function () {
            const button = document.getElementById('contactSend');
            if (!button || button.disabled) return;
            button.disabled = true;
            button.textContent = 'Sending...';
        });
    }
})();
</script>
@endpush
