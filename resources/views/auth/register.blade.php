@extends('layouts.guest')
@section('title', 'Create an account')
@section('auth_panel')
    <div class="auth-panel-copy">
        <p class="auth-panel-kicker">Join the market</p>
        <h1 class="auth-brand-title">MarketLink</h1>
        <p class="auth-panel-lead">Shop as a customer, or apply for a stall. Farmers go live after a quick admin check.</p>
        <ul class="auth-steps">
            <li><span>1</span><div><strong>Choose a role</strong><small>Customer or farmer</small></div></li>
            <li><span>2</span><div><strong>Add basics</strong><small>Details + 4-digit PIN</small></div></li>
            <li><span>3</span><div><strong>Start</strong><small>Browse or wait for approval</small></div></li>
        </ul>
    </div>
@endsection
@section('content')
<div class="login-card card-ml">
    <div class="login-card-head">
        <img class="brand-logo" src="{{ asset('images/logo.svg') }}" alt="" width="40" height="40">
        <div>
            <p class="login-kicker">Join MarketLink</p>
            <h1 class="login-title">Create an account</h1>
        </div>
    </div>
    <p class="login-sub">Only the basics — you can fill the rest later.</p>
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="auth-type-pills mb-4" role="group" aria-label="Account type">
        <button type="button" class="auth-type-pill {{ old('_form', 'customer') !== 'farmer' ? 'is-on' : '' }}" data-type="customer">Customer</button>
        <button type="button" class="auth-type-pill {{ old('_form') === 'farmer' ? 'is-on' : '' }}" data-type="farmer">Farmer</button>
    </div>
    <input type="hidden" id="accountType" value="{{ old('_form', 'customer') === 'farmer' ? 'farmer' : 'customer' }}">

    <div id="customerPane" class="auth-pane" @style(['display:none' => old('_form') === 'farmer'])>
        <form method="POST" action="{{ route('register.customer') }}" id="customerForm">
            @csrf
            <input type="hidden" name="_form" value="customer">
            <label class="form-label" for="c-name">Name</label>
            <div class="login-field mb-3"><i class="bi bi-person"></i><input class="form-control" id="c-name" name="name" value="{{ old('name') }}" required placeholder="Your name"></div>
            <label class="form-label" for="c-email">Email</label>
            <div class="login-field mb-3"><i class="bi bi-envelope"></i><input class="form-control" id="c-email" type="email" name="email" value="{{ old('email') }}" required placeholder="you@email.com"></div>
            <label class="form-label" for="c-phone">Phone</label>
            <div class="login-field mb-3"><i class="bi bi-telephone"></i><input class="form-control" id="c-phone" name="phone" value="{{ old('phone') }}" required placeholder="03xxxxxxxxx"></div>
            <label class="form-label" for="c-password">Choose a 4-digit PIN</label>
            <div class="login-field mb-3"><i class="bi bi-lock"></i><input class="form-control pin-input" id="c-password" name="password" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" minlength="4" required placeholder="e.g. 2580" autocomplete="new-password"></div>
            <label class="form-label" for="c-confirm">Confirm PIN</label>
            <div class="login-field mb-3"><i class="bi bi-lock"></i><input class="form-control pin-input" id="c-confirm" name="password_confirmation" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" minlength="4" required placeholder="Repeat PIN" autocomplete="new-password"></div>
            <button class="btn btn-ml w-100" type="submit">Create customer account</button>
        </form>
    </div>

    <div id="farmerPane" class="auth-pane" @style(['display:none' => old('_form', 'customer') !== 'farmer'])>
        <form method="POST" action="{{ route('register.farmer') }}" id="farmerForm">
            @csrf
            <input type="hidden" name="_form" value="farmer">
            <input type="hidden" name="operating_days[]" value="Saturday">
            <label class="form-label" for="stall">Stall name</label>
            <div class="login-field mb-3"><i class="bi bi-shop"></i><input class="form-control" id="stall" name="stall_name" value="{{ old('stall_name') }}" placeholder="Green Row Farm"></div>
            <label class="form-label" for="contact">Your name</label>
            <div class="login-field mb-3"><i class="bi bi-person"></i><input class="form-control" id="contact" name="contact_person" value="{{ old('contact_person') }}" placeholder="Contact person"></div>
            <label class="form-label" for="f-email">Email</label>
            <div class="login-field mb-3"><i class="bi bi-envelope"></i><input class="form-control" id="f-email" type="email" name="email" value="{{ old('email') }}"></div>
            <label class="form-label" for="f-phone">Phone</label>
            <div class="login-field mb-3"><i class="bi bi-telephone"></i><input class="form-control" id="f-phone" name="phone" value="{{ old('phone') }}"></div>
            <label class="form-label" for="f-password">Choose a 4-digit PIN</label>
            <div class="login-field mb-3"><i class="bi bi-lock"></i><input class="form-control pin-input" id="f-password" name="password" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" minlength="4" autocomplete="new-password"></div>
            <label class="form-label" for="f-confirm">Confirm PIN</label>
            <div class="login-field mb-3"><i class="bi bi-lock"></i><input class="form-control pin-input" id="f-confirm" name="password_confirmation" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" minlength="4" autocomplete="new-password"></div>
            <button class="btn btn-ml w-100" type="submit">Apply as a farmer</button>
            <p class="small muted mt-2 mb-0">Starts as Saturday stall — edit days later after approval.</p>
        </form>
    </div>

    <a class="btn btn-outline-ml w-100 mt-4" href="{{ route('login') }}">Back to log in</a>
</div>
<script>
(function () {
    const typeInput = document.getElementById('accountType');
    const customerPane = document.getElementById('customerPane');
    const farmerPane = document.getElementById('farmerPane');
    const customerForm = document.getElementById('customerForm');
    const farmerForm = document.getElementById('farmerForm');
    const pills = document.querySelectorAll('.auth-type-pill');

    function setRequired(form, on) {
        form.querySelectorAll('input, textarea').forEach(function (el) {
            if (el.type === 'hidden') return;
            if (on) {
                if (el.dataset.wasRequired === '1') el.required = true;
            } else {
                if (el.required) el.dataset.wasRequired = '1';
                el.required = false;
            }
        });
    }

    function sync(type) {
        typeInput.value = type;
        const farmer = type === 'farmer';
        customerPane.style.display = farmer ? 'none' : '';
        farmerPane.style.display = farmer ? '' : 'none';
        pills.forEach(function (pill) {
            pill.classList.toggle('is-on', pill.dataset.type === type);
        });
        setRequired(customerForm, !farmer);
        setRequired(farmerForm, farmer);
        if (farmer) {
            ['stall', 'contact', 'f-email', 'f-phone', 'f-password', 'f-confirm'].forEach(function (id) {
                const el = document.getElementById(id);
                if (el) el.required = true;
            });
        }
    }

    pills.forEach(function (pill) {
        pill.addEventListener('click', function () { sync(pill.dataset.type); });
    });
    document.querySelectorAll('.pin-input').forEach(function (el) {
        el.addEventListener('input', function () { this.value = this.value.replace(/\D/g, '').slice(0, 4); });
    });
    sync(typeInput.value || 'customer');
})();
</script>
@endsection
