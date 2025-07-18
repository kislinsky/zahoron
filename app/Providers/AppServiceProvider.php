<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Artesaos\SEOTools\Facades\SEOMeta; // Импортируем SEOMeta

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
        SEOMeta::setRobots('noindex,nofollow'); // Правильный метод
        // Или альтернативный вариант:
        // SEOMeta::addMeta('robots', 'noindex,nofollow');
        
        Paginator::useBootstrapFive();
         Paginator::useBootstrapFour(); // Выберите одну версию Bootstrap
    }
}