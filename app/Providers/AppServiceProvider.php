<?php

namespace App\Providers;

use App\Models\AppNotification;
use App\Models\Cart;
use App\Models\Setting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        View::composer('*', function ($view) {
            $cartCount = 0;
            $unreadNotifications = 0;

            if (Auth::check()) {
                if (Auth::user()->isCustomer()) {
                    $cart = Cart::where('customer_id', Auth::id())->withCount('items')->first();
                    $cartCount = $cart?->items()->sum('quantity') ?? 0;
                }
                $unreadNotifications = AppNotification::where('user_id', Auth::id())
                    ->where('is_read', false)
                    ->count();
            }

            $tagline = 'Fresh from local farmers markets';
            try {
                $tagline = Setting::getValue('site_tagline', $tagline);
            } catch (\Throwable $e) {
                // DB may not be ready during early artisan commands
            }

            $view->with([
                'cartCount' => $cartCount,
                'unreadNotifications' => $unreadNotifications,
                'siteTagline' => $tagline,
            ]);
        });
    }
}
