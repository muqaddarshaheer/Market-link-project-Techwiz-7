@extends('layouts.customer')
@section('title', 'Profile')
@section('content')
<div class="panel-head mb-4">
    <div>
        <p class="panel-kicker mb-1">Account</p>
        <h1 class="section-title mb-0">Your profile</h1>
    </div>
</div>
@include('partials.account-forms')
@endsection
