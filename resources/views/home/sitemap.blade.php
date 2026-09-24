@extends('layouts.app')
@section('title', 'Sitemap')
@section('content')
<div class="container py-5">
    <h1 class="display-font mb-4">Sitemap</h1>
    <div class="row g-4">
        <div class="col-md-4">
            <h5>Public</h5>
            <ul>
                <li><a href="{{ route('home') }}">Home</a></li>
                <li><a href="{{ route('about') }}">About</a></li>
                <li><a href="{{ route('contact') }}">Contact</a></li>
                <li><a href="{{ route('markets.index') }}">Markets</a></li>
                <li><a href="{{ route('farmers.index') }}">Farmers</a></li>
                <li><a href="{{ route('products.index') }}">Products</a></li>
            </ul>
        </div>
        <div class="col-md-4">
            <h5>Account</h5>
            <ul>
                <li><a href="{{ route('login') }}">Login</a></li>
                <li><a href="{{ route('register') }}">Register</a></li>
                <li><a href="{{ route('customer.dashboard') }}">Customer dashboard</a></li>
                <li><a href="{{ route('farmer.dashboard') }}">Farmer dashboard</a></li>
                <li><a href="{{ route('admin.dashboard') }}">Admin dashboard</a></li>
            </ul>
        </div>
        <div class="col-md-4">
            <h5>Help</h5>
            <ul>
                <li>Use the chatbot widget for FAQ answers</li>
                <li>Pickup only — no delivery</li>
                <li>Pay in person at the stall</li>
            </ul>
        </div>
    </div>
</div>
@endsection
