<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index()
    {
        $favorites = Favorite::with(['product.farmer', 'product.market', 'farmer.user', 'market'])
            ->where('customer_id', auth()->id())
            ->latest()
            ->paginate(12);

        return view('customer.favorites.index', compact('favorites'));
    }

    public function toggle(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'farmer_id' => 'nullable|exists:farmer_profiles,id',
            'market_id' => 'nullable|exists:markets,id',
        ]);

        if (empty($data['product_id']) && empty($data['farmer_id']) && empty($data['market_id'])) {
            return back()->with('error', 'Nothing to favorite.');
        }

        $attrs = [
            'customer_id' => auth()->id(),
            'product_id' => $data['product_id'] ?? null,
            'farmer_id' => $data['farmer_id'] ?? null,
            'market_id' => $data['market_id'] ?? null,
        ];

        $existing = Favorite::where($attrs)->first();

        if ($existing) {
            $existing->delete();

            return back()->with('success', 'Removed from favorites.');
        }

        Favorite::create($attrs);

        return back()->with('success', 'Added to favorites.');
    }
}
