<?php

use App\Models\Market;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/markets', fn () => Market::query()->where('status', 'active')->get(['id', 'name', 'city', 'latitude', 'longitude']));
Route::get('/products', fn () => Product::query()->where('is_available', true)->with('farmer:id,stall_name')->latest()->take(50)->get(['id', 'name', 'price', 'unit', 'farmer_id']));
