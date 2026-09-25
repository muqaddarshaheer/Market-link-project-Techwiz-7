<?php

namespace App\Http\Controllers;

use App\Models\FarmerProfile;
use Illuminate\Http\Request;

class FarmerController extends Controller
{
    public function index(Request $request)
    {
        $farmers = FarmerProfile::query()
            ->where('approval_status', 'approved')
            ->with(['user', 'markets', 'products.category'])
            ->withAvg(['reviews as rating_avg' => fn ($q) => $q->where('status', 'approved')], 'rating')
            ->withCount('products')
            ->when($request->q, function ($q, $term) {
                $q->where(function ($inner) use ($term) {
                    $inner->where('stall_name', 'like', "%{$term}%")
                        ->orWhere('contact_person', 'like', "%{$term}%")
                        ->orWhereHas('products', fn ($products) => $products->where('name', 'like', "%{$term}%"));
                });
            })
            ->when($request->day, fn ($q, $day) => $q->whereJsonContains('operating_days', $day))
            ->orderBy('stall_name')
            ->paginate(9)
            ->withQueryString();

        return view('farmers.index', compact('farmers'));
    }

    public function show(FarmerProfile $farmer)
    {
        abort_unless($farmer->approval_status === 'approved' || auth()->id() === $farmer->user_id || auth()->user()?->isAdmin(), 404);

        $farmer->load([
            'user',
            'markets',
            'products.category',
            'products.market',
            'reviews' => fn ($q) => $q->where('status', 'approved')->with(['customer', 'product'])->latest(),
        ]);

        return view('farmers.show', compact('farmer'));
    }
}
