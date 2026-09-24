<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Category;
use App\Models\ChatbotFaq;
use App\Models\FarmerProfile;
use App\Models\Market;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featuredProducts = Product::with(['farmer.user', 'market', 'category'])
            ->available()
            ->featured()
            ->latest()
            ->take(8)
            ->get();

        if ($featuredProducts->isEmpty()) {
            $featuredProducts = Product::with(['farmer.user', 'market', 'category'])
                ->available()
                ->latest()
                ->take(8)
                ->get();
        }

        $markets = Market::active()->withCount('products')->take(6)->get();
        $farmers = FarmerProfile::approved()->with('user')->withCount('products')->take(6)->get();
        $categories = Category::withCount('products')->orderBy('name')->get();
        $announcements = Announcement::published()->latest('published_at')->take(3)->get();

        return view('home.index', compact('featuredProducts', 'markets', 'farmers', 'categories', 'announcements'));
    }

    public function about(): View
    {
        return view('home.about');
    }

    public function contact(): View
    {
        return view('home.contact');
    }

    public function contactSubmit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'message' => 'required|string|max:2000',
        ]);

        return back()->with('success', 'Thanks for reaching out! We will get back to you soon.');
    }

    public function sitemap(): View
    {
        return view('home.sitemap');
    }

    public function chatbotAsk(Request $request)
    {
        $request->validate(['message' => 'required|string|max:500']);

        $faq = ChatbotFaq::matchQuery($request->message);

        if ($faq) {
            return response()->json([
                'answer' => $faq->answer,
                'matched' => $faq->question,
            ]);
        }

        return response()->json([
            'answer' => 'Sorry, I could not find an answer. Try asking about orders, pickup, farmers, or payment. You can also visit our Contact page.',
            'matched' => null,
        ]);
    }

    public function chatbotSuggestions()
    {
        $suggestions = ChatbotFaq::query()->inRandomOrder()->take(5)->pluck('question');

        return response()->json(['suggestions' => $suggestions]);
    }
}
