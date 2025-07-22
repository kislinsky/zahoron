<?php

namespace App\Providers;

use Artesaos\SEOTools\Facades\SEOMeta; // Импортируем SEOMeta
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

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


         Validator::extend('recaptcha', function ($attribute, $value, $parameters, $validator) {
        if (app()->environment('local')) return true;
        
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('recaptcha.secret_key'),
            'response' => $value,
            'remoteip' => request()->ip()
        ]);
        
        return $response->json()['success'];
    });
    }
}