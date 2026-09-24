@extends('layouts.guest')
@section('title', 'Register')
@section('content')
<ul class="nav nav-pills mb-3" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#customer">Customer</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#farmer">Farmer</button></li>
</ul>
<div class="tab-content">
    <div class="tab-pane fade show active" id="customer">
        <form method="POST" action="{{ route('register.customer') }}" class="card-ml p-4">
            @csrf
            <label class="form-label">Name</label><input class="form-control mb-2" name="name" value="{{ old('name') }}" required>
            <label class="form-label">Email</label><input class="form-control mb-2" type="email" name="email" required>
            <label class="form-label">Phone</label><input class="form-control mb-2" name="phone" required>
            <label class="form-label">Address</label><textarea class="form-control mb-2" name="address" required></textarea>
            <label class="form-label">Password</label><input class="form-control mb-2" type="password" name="password" required>
            <label class="form-label">Confirm</label><input class="form-control mb-3" type="password" name="password_confirmation" required>
            <button class="btn btn-ml">Create customer account</button>
        </form>
    </div>
    <div class="tab-pane fade" id="farmer">
        <form method="POST" action="{{ route('register.farmer') }}" class="card-ml p-4">
            @csrf
            <label class="form-label">Stall name</label><input class="form-control mb-2" name="stall_name" required>
            <label class="form-label">Contact person</label><input class="form-control mb-2" name="contact_person" required>
            <label class="form-label">Email</label><input class="form-control mb-2" type="email" name="email" required>
            <label class="form-label">Phone</label><input class="form-control mb-2" name="phone" required>
            <label class="form-label">Address</label><textarea class="form-control mb-2" name="address" required></textarea>
            <label class="form-label">Operating days</label>
            <div class="mb-2">@foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)
                <label class="me-2"><input type="checkbox" name="operating_days[]" value="{{ $day }}"> {{ $day }}</label>
            @endforeach</div>
            <label class="form-label">Password</label><input class="form-control mb-2" type="password" name="password" required>
            <input class="form-control mb-3" type="password" name="password_confirmation" required>
            <button class="btn btn-ml">Apply as a farmer</button>
        </form>
    </div>
</div>
@endsection
