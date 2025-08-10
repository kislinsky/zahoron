<?php

namespace App\Providers;

use App\Models\Organization;
use Artesaos\SEOTools\Facades\SEOMeta;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Stevebauman\Location\Facades\Location;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Регистрируем мок-драйвер для локального окружения
        if ($this->app->environment('local')) {
            $this->app->bind('location', function() {
                return new class {
                    public function get($ip) {
                        return (object) [
                            'cityName' => 'Москва',
                            'countryName' => 'Россия',
                            'regionName' => 'Московская область',
                            'latitude' => '55.7558',
                            'longitude' => '37.6176'
                        ];
                    }
                };
            });
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // SEO настройки
        $city = selectCity();
        if ($city) {
            $shouldNoIndex = false;
            
            if ($city->area && $city->area->edge && $city->area->edge->is_show != 1) {
                $shouldNoIndex = true;
            }
            
            $organizationsCount = Organization::where('city_id', $city->id)->count();
            if ($organizationsCount < 3) {
                $shouldNoIndex = true;
            }
            
            if ($shouldNoIndex) {
                SEOMeta::setRobots('noindex,nofollow');
            }
        }

        Paginator::useBootstrapFive();
        
        // Кастомное правило валидации reCAPTCHA
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