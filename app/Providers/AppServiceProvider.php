<?php

namespace App\Providers;

use App\Models\Announcement;
use App\Models\Setting;
use Illuminate\Pagination\Paginator;
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
            $user = auth()->user();
            $unread = 0;
            if ($user) {
                $unread = $user->appNotifications()->where('is_read', false)->count();
            }
            $view->with('unreadNotifications', $unread);
            $view->with('siteName', Setting::getValue('platform_name', 'MarketLink'));
        });

        View::composer('layouts.app', function ($view) {
            $view->with('liveAnnouncements', Announcement::query()->live()->latest('published_at')->take(3)->get());
        });
    }
}
