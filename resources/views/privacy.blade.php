@extends('layouts.app')
@section('title', 'Privacy')
@section('content')
<h1 class="section-title">Privacy</h1>
<p>{{ $siteName }} stores the account details you provide so farmers can prepare your pickup. Orders, favorites, and reviews stay tied to your account. We do not take card payments on this site. Pay the farmer in person when you collect your order.</p>
<p>Questions: {{ $siteEmail }} @if($sitePhone) · {{ $sitePhone }} @endif</p>
@endsection
