<?php

namespace App\Http\Controllers;

use App\Models\Market;
use Illuminate\Http\Request;

class MarketController extends Controller
{
    public function index(Request $request)
    {
        $markets = Market::query()
            ->where('status', 'active')
            ->when($request->city, fn ($q, $city) => $q->where('city', $city))
            ->when($request->day, fn ($q, $day) => $q->whereJsonContains('operating_days', $day))
            ->withCount(['products', 'farmers'])
            ->orderBy('name')
            ->paginate(9)
            ->withQueryString();

        return view('markets.index', [
            'markets' => $markets,
            'cities' => Market::query()->where('status', 'active')->distinct()->orderBy('city')->pluck('city'),
        ]);
    }

    public function show(Market $market)
    {
        abort_unless($market->status === 'active' || auth()->user()?->isAdmin(), 404);

        $market->load([
            'farmers.user',
            'products' => fn ($q) => $q->where('is_available', true)->with(['farmer', 'category'])->latest()->take(12),
        ]);

        return view('markets.show', compact('market'));
    }
}
