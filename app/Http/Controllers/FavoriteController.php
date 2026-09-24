<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Product;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function toggle(Request $request)
    {
        $data = $request->validate([
            'farmer_id' => ['nullable', 'integer', 'exists:farmer_profiles,id'],
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'market_id' => ['nullable', 'integer', 'exists:markets,id'],
        ]);

        if (! ($data['farmer_id'] ?? null) && ! ($data['product_id'] ?? null) && ! ($data['market_id'] ?? null)) {
            return back()->withErrors(['favorite' => 'Choose something to save.']);
        }

        $existing = Favorite::query()
            ->where('customer_id', auth()->id())
            ->where('farmer_id', $data['farmer_id'] ?? null)
            ->where('product_id', $data['product_id'] ?? null)
            ->where('market_id', $data['market_id'] ?? null)
            ->first();

        if ($existing) {
            $existing->delete();
            return back()->with('success', 'Removed from favorites.');
        }

        Favorite::query()->create([
            'customer_id' => auth()->id(),
            ...$data,
        ]);

        return back()->with('success', 'Saved to favorites.');
    }
}
