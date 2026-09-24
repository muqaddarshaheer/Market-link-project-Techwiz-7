@extends('layouts.app')
@section('title', 'Register')
@section('content')
<div class="container py-5" x-data="{ role: '{{ old('role', 'customer') }}' }">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="panel">
                <h1 class="h3 display-font mb-3">Create your MarketLink account</h1>
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">I am a</label>
                        <div class="d-flex gap-3">
                            <label class="form-check"><input class="form-check-input" type="radio" name="role" value="customer" x-model="role"> Customer</label>
                            <label class="form-check"><input class="form-check-input" type="radio" name="role" value="farmer" x-model="role"> Farmer</label>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full name</label>
                            <input class="form-control" name="name" value="{{ old('name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input class="form-control" name="phone" value="{{ old('phone') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Address</label>
                            <input class="form-control" name="address" value="{{ old('address') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirm password</label>
                            <input type="password" class="form-control" name="password_confirmation" required>
                        </div>
                    </div>

                    <div class="mt-4 p-3 border rounded" x-show="role === 'farmer'">
                        <h5>Stall details</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Stall name</label>
                                <input class="form-control" name="stall_name" value="{{ old('stall_name') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contact person</label>
                                <input class="form-control" name="contact_person" value="{{ old('contact_person') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Business description</label>
                                <textarea class="form-control" name="business_description" rows="3">{{ old('business_description') }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Farm / stall address</label>
                                <input class="form-control" name="farmer_address" value="{{ old('farmer_address') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label d-block">Operating days</label>
                                @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)
                                    <label class="me-2"><input type="checkbox" name="operating_days[]" value="{{ $day }}"> {{ substr($day,0,3) }}</label>
                                @endforeach
                            </div>
                        </div>
                        <p class="small text-muted mt-2 mb-0">Farmer accounts require admin approval before products appear publicly.</p>
                    </div>

                    <button class="btn btn-primary mt-4" type="submit">Create account</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
