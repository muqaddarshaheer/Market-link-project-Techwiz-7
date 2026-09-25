@extends('layouts.app')
@section('title', 'Contact')
@section('content')
<div class="contact-only">
    <p class="story-kicker">Contact</p>
    <h1 class="section-title">Send a message</h1>
    <p class="muted mb-4">Ask about markets, farmers, produce, or your pickup. We reply to the email you leave.</p>
    <form method="POST" action="{{ route('contact.send') }}" class="card-ml p-4 contact-form-card" id="contactForm">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="c-name">Name</label>
                <input id="c-name" class="form-control" name="name" required value="{{ old('name') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label" for="c-email">Email</label>
                <input id="c-email" class="form-control" type="email" name="email" required value="{{ old('email') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label" for="c-type">Topic</label>
                <select id="c-type" class="form-select" name="type">
                    @foreach(['General Question','Market','Farmer','Product','Reservation','Other'] as $type)
                        <option @selected(old('type') === $type)>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="c-subject">Subject</label>
                <input id="c-subject" class="form-control" name="subject" value="{{ old('subject') }}">
            </div>
            <div class="col-12">
                <label class="form-label" for="c-message">Message</label>
                <textarea id="c-message" class="form-control" name="message" rows="5" required>{{ old('message') }}</textarea>
            </div>
        </div>
        <button class="btn btn-ml mt-3" type="submit" id="contactSend">Send message</button>
    </form>
</div>
@endsection
@push('scripts')
<script>
document.getElementById('contactForm').addEventListener('submit', function () {
    const button = document.getElementById('contactSend');
    button.disabled = true;
    button.textContent = 'Sending…';
});
</script>
@endpush
