@extends('layouts.app')
@section('title', 'Contact')
@section('content')
<div class="row g-4">
    <div class="col-lg-5">
        <h1 class="section-title">Contact</h1>
        <p>{{ $address }}</p>
        <p>{{ $email }}<br>{{ $phone }}</p>
        <form method="POST" action="{{ route('contact.send') }}" class="card-ml p-3">
            @csrf
            <div class="mb-2"><label class="form-label">Name</label><input class="form-control" name="name" required value="{{ old('name') }}"></div>
            <div class="mb-2"><label class="form-label">Email</label><input class="form-control" type="email" name="email" required value="{{ old('email') }}"></div>
            <div class="mb-2"><label class="form-label">Message</label><textarea class="form-control" name="message" rows="4" required>{{ old('message') }}</textarea></div>
            <button class="btn btn-ml">Send</button>
        </form>
    </div>
    <div class="col-lg-7">
        @include('partials.map', ['id' => 'contact-map', 'points' => [['lat' => 30.2672, 'lng' => -97.7431, 'title' => 'MarketLink', 'subtitle' => $address]]])
    </div>
</div>
@endsection
