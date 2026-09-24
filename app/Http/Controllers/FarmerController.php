<?php

namespace App\Http\Controllers;

use App\Models\FarmerProfile;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FarmerController extends Controller
{
    public function index(Request $request): View
    {
        $query = FarmerProfile::approved()
            ->with(['user', 'markets'])
            ->withCount(['products', 'reviews'])
            ->withAvg(['reviews as avg_rating' => fn ($q) => $q->where('status', 'approved')], 'rating');

        if ($search = $request->string('q')->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('stall_name', 'like', "%{$search}%")
                    ->orWhere('contact_person', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        if ($marketId = $request->integer('market_id')) {
            $query->whereHas('markets', fn ($q) => $q->where('markets.id', $marketId));
        }

        $farmers = $query->orderBy('stall_name')->paginate(12)->withQueryString();
        $mapFarmers = FarmerProfile::approved()->whereNotNull('latitude')->whereNotNull('longitude')->get()
            ->map(fn (FarmerProfile $f) => [
                'lat' => (float) $f->latitude,
                'lng' => (float) $f->longitude,
                'popup' => '<strong>'.e($f->stall_name).'</strong>'
                    .'<br><a href="'.route('farmers.show', $f).'">Open</a>',
            ])->values();

        return view('farmers.index', compact('farmers', 'mapFarmers'));
    }

    public function show(FarmerProfile $farmer): View
    {
        abort_unless($farmer->isApproved() || auth()->user()?->isAdmin() || auth()->id() === $farmer->user_id, 404);

        $farmer->load(['user', 'markets', 'products' => fn ($q) => $q->available()->with('category')]);
        $reviews = $farmer->reviews()->approved()->with('customer')->latest()->paginate(8);
        $avgRating = $farmer->averageRating();

        return view('farmers.show', compact('farmer', 'reviews', 'avgRating'));
    }
}
