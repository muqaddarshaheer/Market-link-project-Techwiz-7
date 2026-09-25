<?php

namespace App\Providers;

use App\Models\Announcement;
use App\Models\Setting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
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

        View::composer(['layouts.app', 'layouts.admin', 'layouts.farmer', 'layouts.customer'], function ($view) {
            $user = auth()->user();
            $unread = 0;
            $cartCount = 0;

            if ($user) {
                $unread = (int) Cache::remember(
                    'user.'.$user->id.'.unread',
                    20,
                    fn () => $user->appNotifications()->where('is_read', false)->count()
                );

                if ($user->isCustomer()) {
                    $cartCount = (int) Cache::remember(
                        'user.'.$user->id.'.cart_qty',
                        20,
                        fn () => (int) ($user->cart?->items()->sum('quantity') ?? 0)
                    );
                }
            }

            $settings = Cache::remember('settings.all', 300, fn () => Setting::query()->pluck('value', 'key')->all());

            $view->with([
                'unreadNotifications' => $unread,
                'cartCount' => $cartCount,
                'siteName' => $settings['platform_name'] ?? 'MarketLink',
                'siteEmail' => $settings['contact_email'] ?? 'hello@marketlink.com',
                'sitePhone' => $settings['contact_phone'] ?? '',
                'siteAddress' => $settings['contact_address'] ?? '',
                'siteFacebook' => $settings['facebook'] ?? '',
                'siteInstagram' => $settings['instagram'] ?? '',
            ]);
        });

        View::composer('layouts.app', function ($view) {
            $view->with('liveAnnouncements', Cache::remember('announcements.live', 60, function () {
                return Announcement::query()->live()->latest('published_at')->take(3)->get();
            }));
        });
    }
}
