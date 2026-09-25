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
        <li class="nav-item"><button class="nav-link {{ old('_form', 'customer') === 'farmer' ? '' : 'active' }}" id="tab-customer" data-bs-toggle="tab" data-bs-target="#customer" type="button">Customer</button></li>
        <li class="nav-item"><button class="nav-link {{ old('_form') === 'farmer' ? 'active' : '' }}" id="tab-farmer" data-bs-toggle="tab" data-bs-target="#farmer" type="button">Farmer</button></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade {{ old('_form', 'customer') === 'farmer' ? '' : 'show active' }}" id="customer">
            <form method="POST" action="{{ route('register.customer') }}" class="auth-form-grid" id="customerForm">
                @csrf
                <input type="hidden" name="_form" value="customer">
                <label class="form-label" for="c-name">Name</label>
                <div class="login-field mb-3"><i class="bi bi-person"></i><input class="form-control" id="c-name" name="name" value="{{ old('name') }}" required placeholder="Your name"></div>
                <label class="form-label" for="c-email">Email</label>
                <div class="login-field mb-3"><i class="bi bi-envelope"></i><input class="form-control" id="c-email" type="email" name="email" value="{{ old('email') }}" required placeholder="you@email.com"></div>
                <label class="form-label" for="c-phone">Phone</label>
                <div class="login-field mb-3"><i class="bi bi-telephone"></i><input class="form-control" id="c-phone" name="phone" value="{{ old('phone') }}" required placeholder="03xxxxxxxxx"></div>
                <label class="form-label" for="c-address">Address</label>
                <textarea class="form-control mb-3" id="c-address" name="address" required placeholder="Street, city">{{ old('address') }}</textarea>
                <label class="form-label" for="c-password">4-digit PIN</label>
                <div class="login-field mb-3"><i class="bi bi-lock"></i><input class="form-control pin-input" id="c-password" name="password" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" minlength="4" required placeholder="4 digits" autocomplete="new-password"></div>
                <label class="form-label" for="c-confirm">Confirm PIN</label>
                <div class="login-field mb-3"><i class="bi bi-lock"></i><input class="form-control pin-input" id="c-confirm" name="password_confirmation" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" minlength="4" required placeholder="Repeat PIN" autocomplete="new-password"></div>
                <button class="btn btn-ml w-100" type="submit">Create customer account</button>
            </form>
        </div>
        <div class="tab-pane fade {{ old('_form') === 'farmer' ? 'show active' : '' }}" id="farmer">
            <form method="POST" action="{{ route('register.farmer') }}" id="farmerForm">
                @csrf
                <input type="hidden" name="_form" value="farmer">
                <label class="form-label" for="stall">Stall name</label>
                <div class="login-field mb-3"><i class="bi bi-shop"></i><input class="form-control" id="stall" name="stall_name" value="{{ old('stall_name') }}" required placeholder="Green Row Farm"></div>
                <label class="form-label" for="contact">Contact person</label>
                <div class="login-field mb-3"><i class="bi bi-person"></i><input class="form-control" id="contact" name="contact_person" value="{{ old('contact_person') }}" required placeholder="Your name"></div>
                <label class="form-label" for="f-email">Email</label>
                <div class="login-field mb-3"><i class="bi bi-envelope"></i><input class="form-control" id="f-email" type="email" name="email" value="{{ old('email') }}" required></div>
                <label class="form-label" for="f-phone">Phone</label>
                <div class="login-field mb-3"><i class="bi bi-telephone"></i><input class="form-control" id="f-phone" name="phone" value="{{ old('phone') }}" required></div>
                <label class="form-label" for="f-address">Address</label>
                <textarea class="form-control mb-3" id="f-address" name="address" required>{{ old('address') }}</textarea>
                <label class="form-label">Operating days</label>
                <div class="day-picks mb-3">
                    @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)
                        <label><input type="checkbox" name="operating_days[]" value="{{ $day }}" @checked(in_array($day, old('operating_days', ['Saturday'])))> {{ substr($day, 0, 3) }}</label>
                    @endforeach
                </div>
                <label class="form-label" for="f-password">4-digit PIN</label>
                <div class="login-field mb-3"><i class="bi bi-lock"></i><input class="form-control pin-input" id="f-password" name="password" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" minlength="4" required autocomplete="new-password"></div>
                <label class="form-label" for="f-confirm">Confirm PIN</label>
                <div class="login-field mb-3"><i class="bi bi-lock"></i><input class="form-control pin-input" id="f-confirm" name="password_confirmation" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" minlength="4" required autocomplete="new-password"></div>
                <button class="btn btn-ml w-100" type="submit">Apply as a farmer</button>
                <p class="small muted mt-2 mb-0">Your stall stays pending until an admin approves it.</p>
            </form>
        </div>
    </div>
    <a class="btn btn-outline-ml w-100 mt-4" href="{{ route('login') }}">Back to log in</a>
</div>
<script>
document.querySelectorAll('.pin-input').forEach(function (el) {
    el.addEventListener('input', function () { this.value = this.value.replace(/\D/g, '').slice(0, 4); });
});
</script>
@endsection
