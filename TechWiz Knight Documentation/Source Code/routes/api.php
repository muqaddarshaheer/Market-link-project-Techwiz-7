<?php

use App\Models\Market;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:60,1')->group(function () {
    Route::get('/markets', function () {
        return Market::query()
            ->where('status', 'active')
            ->get(['id', 'name', 'city', 'latitude', 'longitude']);
    });

    Route::get('/products', function () {
        return Product::query()
            ->where('is_available', true)
            ->where('is_sold_out', false)
            ->whereHas('farmer', fn ($q) => $q->where('approval_status', 'approved'))
            ->with('farmer:id,stall_name')
            ->latest()
            ->take(50)
            ->get(['id', 'name', 'price', 'unit', 'farmer_id']);
    });
});
