<?php

namespace App\Providers;

use App\Models\Announcement;
use App\Models\Setting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        require_once app_path('helpers.php');
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        if (! $this->app->runningInConsole() && ! empty($_SERVER['HTTP_HOST'])) {
            $https = (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
            $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
            $base = $base === '/' ? '' : $base;
            URL::forceRootUrl(($https ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].$base);
        }

        View::composer('*', function ($view) {
            $user = auth()->user();
            $unread = 0;
            if ($user) {
                $unread = $user->appNotifications()->where('is_read', false)->count();
            }
            $cartCount = 0;
            if ($user && $user->isCustomer()) {
                $cartCount = (int) ($user->cart?->items()->sum('quantity') ?? 0);
            }
            $view->with('unreadNotifications', $unread);
            $view->with('cartCount', $cartCount);
            $view->with('siteName', Setting::getValue('platform_name', 'MarketLink'));
            $view->with('siteEmail', Setting::getValue('contact_email', 'hello@marketlink.com'));
            $view->with('sitePhone', Setting::getValue('contact_phone', ''));
            $view->with('siteAddress', Setting::getValue('contact_address', ''));
            $view->with('siteFacebook', Setting::getValue('facebook', ''));
            $view->with('siteInstagram', Setting::getValue('instagram', ''));
        });

        View::composer('layouts.app', function ($view) {
            $view->with('liveAnnouncements', Announcement::query()->live()->latest('published_at')->take(3)->get());
        });
    }
}
