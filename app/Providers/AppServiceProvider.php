<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Setting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // مشاركة بيانات الإعدادات على مستوى كل الـ Views تلقائياً
        View::composer('*', function ($view) {
            $siteSetting = Setting::first();
            $view->with('siteSetting', $siteSetting);
        });
    }
}
