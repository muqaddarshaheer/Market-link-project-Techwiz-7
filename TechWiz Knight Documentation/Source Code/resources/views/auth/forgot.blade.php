@extends('layouts.guest')
@section('content')
<form method="POST" action="{{ route('password.email') }}" class="card-ml p-4">
    @csrf
    <h1 class="h4">Reset password</h1>
    <p class="muted">We will email a reset link. With the log mailer, the link is written to storage/logs/laravel.log.</p>
    <input class="form-control mb-3" type="email" name="email" required placeholder="Email">
    <button class="btn btn-ml">Send link</button>
</form>
@endsection
