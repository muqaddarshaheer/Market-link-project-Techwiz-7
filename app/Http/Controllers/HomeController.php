<?php

namespace App\Http\Controllers;

use App\Models\FarmerProfile;
use App\Models\Market;
use App\Models\Product;
use App\Models\Review;
use App\Models\Setting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('home', [
            'markets' => Market::query()->where('status', 'active')->withCount('products')->latest()->take(4)->get(),
            'farmers' => FarmerProfile::query()->where('approval_status', 'approved')->with('user')->withCount('products')->take(4)->get(),
            'products' => Product::query()->where('is_featured', true)->where('is_available', true)
                ->with(['farmer', 'category', 'market'])->take(8)->get(),
            'reviews' => Review::query()->where('status', 'approved')->with('customer')->latest()->take(3)->get(),
            'harvestGrowers' => Product::query()
                ->select(['id', 'name', 'quality', 'stock_quantity', 'unit', 'is_available', 'is_sold_out', 'farmer_id', 'market_id'])
                ->whereHas('farmer', fn ($q) => $q->where('approval_status', 'approved'))
                ->with(['farmer:id,stall_name,user_id', 'farmer.user:id,phone', 'market:id,name'])
                ->where('is_available', true)
                ->limit(40)
                ->get()
                ->map(fn (Product $product) => [
                    'name' => $product->name,
                    'quality' => ucfirst($product->quality ?? 'fresh'),
                    'status' => $product->canPurchase() ? 'In stock' : 'Unavailable',
                    'stock' => $product->stock_quantity.' '.$product->unit,
                    'farmer' => $product->farmer->stall_name,
                    'phone' => $product->farmer->user->phone ?? '',
                    'market' => $product->market->name,
                    'url' => route('products.show', $product),
                ])->values(),
        ]);
    }

    public function about()
    {
        return view('about', [
            'farmers' => FarmerProfile::query()->where('approval_status', 'approved')->with('user')->withCount('products')->take(3)->get(),
            'products' => Product::query()->where('is_available', true)->with(['farmer', 'category', 'market'])->take(4)->get(),
        ]);
    }

    public function contact()
    {
        return view('contact', [
            'email' => Setting::getValue('contact_email', 'hello@marketlink.test'),
            'phone' => Setting::getValue('contact_phone', '+1 555 0100'),
            'address' => Setting::getValue('contact_address', '12 Orchard Lane'),
        ]);
    }

    public function sendContact(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email'],
            'subject' => ['nullable', 'string', 'max:120'],
            'type' => ['nullable', 'string', 'max:40'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        return back()->with('success', 'Thanks '.$request->name.'. We received your message and will reply soon.');
    }

    public function sitemap()
    {
        return view('sitemap');
    }

    public function privacy()
    {
        return view('privacy');
    }

    public function terms()
    {
        return view('terms');
    }
}
