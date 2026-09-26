@extends('layouts.farmer')
@section('title', 'Account')
@section('content')
<div class="panel-head mb-4">
    <div>
        <p class="panel-kicker mb-1" data-i18n="acc.kicker">Account</p>
        <h1 class="section-title mb-0" data-i18n="acc.title">Your account</h1>
        <p class="home-sub mb-0" data-i18n="acc.lead">Photo, contact details, and password.</p>
    </div>
</div>
@include('partials.account-forms')
@endsection
