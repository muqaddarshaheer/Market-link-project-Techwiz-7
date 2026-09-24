@extends('layouts.guest')
@section('title', 'Create an account')
@section('content')
<div class="login-card card-ml p-4 p-md-5">
    <p class="login-kicker">Join MarketLink</p>
    <h1 class="h3 mb-1">Create an account</h1>
    <p class="muted mb-4">Shop as a customer, or apply for a stall. Farmer listings go live after admin approval.</p>
    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    <ul class="nav nav-pills auth-tabs mb-4" role="tablist">
        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#customer" type="button">Customer</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#farmer" type="button">Farmer</button></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade show active" id="customer">
            <form method="POST" action="{{ route('register.customer') }}" class="auth-form-grid">
                @csrf
                <label class="form-label" for="c-name">Name</label>
                <div class="login-field mb-3"><i class="bi bi-person"></i><input class="form-control" id="c-name" name="name" value="{{ old('name') }}" required placeholder="Your name"></div>
                <label class="form-label" for="c-email">Email</label>
                <div class="login-field mb-3"><i class="bi bi-envelope"></i><input class="form-control" id="c-email" type="email" name="email" value="{{ old('email') }}" required placeholder="you@email.com"></div>
                <label class="form-label" for="c-phone">Phone</label>
                <div class="login-field mb-3"><i class="bi bi-telephone"></i><input class="form-control" id="c-phone" name="phone" value="{{ old('phone') }}" required placeholder="555-0100"></div>
                <label class="form-label" for="c-address">Address</label>
                <textarea class="form-control mb-3" id="c-address" name="address" required placeholder="Street, city">{{ old('address') }}</textarea>
                <label class="form-label" for="c-password">Password</label>
                <div class="login-field mb-3"><i class="bi bi-lock"></i><input class="form-control" id="c-password" type="password" name="password" required minlength="8" placeholder="At least 8 characters"></div>
                <label class="form-label" for="c-confirm">Confirm password</label>
                <div class="login-field mb-3"><i class="bi bi-lock"></i><input class="form-control" id="c-confirm" type="password" name="password_confirmation" required placeholder="Repeat password"></div>
                <button class="btn btn-ml w-100" type="submit">Create customer account</button>
            </form>
        </div>
        <div class="tab-pane fade" id="farmer">
            <form method="POST" action="{{ route('register.farmer') }}">
                @csrf
                <label class="form-label" for="stall">Stall name</label>
                <div class="login-field mb-3"><i class="bi bi-shop"></i><input class="form-control" id="stall" name="stall_name" required placeholder="Green Row Farm"></div>
                <label class="form-label" for="contact">Contact person</label>
                <div class="login-field mb-3"><i class="bi bi-person"></i><input class="form-control" id="contact" name="contact_person" required placeholder="Your name"></div>
                <label class="form-label" for="f-email">Email</label>
                <div class="login-field mb-3"><i class="bi bi-envelope"></i><input class="form-control" id="f-email" type="email" name="email" required></div>
                <label class="form-label" for="f-phone">Phone</label>
                <div class="login-field mb-3"><i class="bi bi-telephone"></i><input class="form-control" id="f-phone" name="phone" required></div>
                <label class="form-label" for="f-address">Address</label>
                <textarea class="form-control mb-3" id="f-address" name="address" required></textarea>
                <label class="form-label">Operating days</label>
                <div class="day-picks mb-3">
                    @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)
                        <label><input type="checkbox" name="operating_days[]" value="{{ $day }}" @checked($day === 'Saturday' || in_array($day, old('operating_days', [])))> {{ substr($day, 0, 3) }}</label>
                    @endforeach
                </div>
                <label class="form-label" for="f-password">Password</label>
                <div class="login-field mb-3"><i class="bi bi-lock"></i><input class="form-control" id="f-password" type="password" name="password" required minlength="8"></div>
                <label class="form-label" for="f-confirm">Confirm password</label>
                <div class="login-field mb-3"><i class="bi bi-lock"></i><input class="form-control" id="f-confirm" type="password" name="password_confirmation" required></div>
                <button class="btn btn-ml w-100" type="submit">Apply as a farmer</button>
                <p class="small muted mt-2 mb-0">Your stall stays pending until an admin approves it.</p>
            </form>
        </div>
    </div>
    <p class="mt-4 mb-0">Already have an account? <a href="{{ route('login') }}">Log in</a></p>
</div>
@endsection
