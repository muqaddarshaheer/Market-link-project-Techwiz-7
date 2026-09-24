<?php

namespace App\Http\Controllers;

use App\Models\Market;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketController extends Controller
{
    public function index(Request $request): View
    {
        $query = Market::active()->withCount(['products', 'farmers']);

        if ($city = $request->string('city')->toString()) {
            $query->where('city', 'like', '%'.$city.'%');
        }

        if ($day = $request->string('day')->toString()) {
            $query->whereJsonContains('operating_days', $day);
        }

        if ($search = $request->string('q')->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $markets = $query->orderBy('name')->paginate(12)->withQueryString();
        $cities = Market::active()->distinct()->orderBy('city')->pluck('city');
        $mapMarkets = Market::active()->whereNotNull('latitude')->whereNotNull('longitude')->get()
            ->map(fn (Market $m) => [
                'lat' => (float) $m->latitude,
                'lng' => (float) $m->longitude,
                'popup' => '<strong>'.e($m->name).'</strong><br>'.e($m->city)
                    .'<br><a href="'.route('markets.show', $m).'">Open</a>',
            ])->values();

        return view('markets.index', compact('markets', 'cities', 'mapMarkets'));
    }

    public function show(Market $market): View
    {
        abort_unless($market->status === 'active' || auth()->user()?->isAdmin(), 404);

        $market->load([
            'farmers' => fn ($q) => $q->approved()->with('user'),
            'products' => fn ($q) => $q->available()->with(['category', 'farmer'])->take(24),
        ]);

        return view('markets.show', compact('market'));
    }
}
