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
        ]);
    }

    public function about()
    {
        return view('about');
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
