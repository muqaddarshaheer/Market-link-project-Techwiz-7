@extends('layouts.app')
@section('title', 'About')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="display-font mb-3">About MarketLink</h1>
            <p class="lead">MarketLink connects local farmers-market growers with customers who want fresh, seasonal food — without delivery apps or online checkouts.</p>
            <p>Customers browse nearby markets, filter produce, and place pre-orders for pickup. Farmers manage stock, accept orders, and prepare stall pickups. Admins keep the marketplace trustworthy with approvals, moderation, and reports.</p>
            <ul>
                <li>No online payments — pay in person at pickup</li>
                <li>No delivery — collect at the market stall</li>
                <li>Farmers are admin-approved before listing</li>
                <li>Stock-validated carts and cutoff-aware order changes</li>
            </ul>
            <a href="{{ route('register') }}" class="btn btn-primary">Join MarketLink</a>
        </div>
    </div>
</div>
@endsection
