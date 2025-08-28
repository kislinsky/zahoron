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
        // // Регистрируем мок-драйвер для локального окружения
        // if (env('API_WORK'=='false')) {
        //     $this->app->bind('location', function() {
        //         return new class {
        //             public function get($ip) {
        //                 return (object) [
        //                     'cityName' => 'Москва',
        //                     'countryName' => 'Россия',
        //                     'regionName' => 'Московская область',
        //                     'latitude' => '55.7558',
        //                     'longitude' => '37.6176'
        //                 ];
        //             }
        //         };
        //     });
        // }
        // // Регистрируем реальный драйвер для production
        // else {
        //     $this->app->bind('location', function() {
        //         $driver = config('location.driver');
                
        //         if ($driver === 'ipapi') {
        //             return new class(config('location.drivers.ipapi')) {
        //                 protected $token;
        //                 protected $secure;
                        
        //                 public function __construct(array $config) {
        //                     $this->token = $config['token'] ?? null;
        //                     $this->secure = $config['secure'] ?? false;
        //                 }
                        
        //                 public function get($ip) {
        //                     $protocol = $this->secure ? 'https' : 'http';
        //                     $url = "{$protocol}://ipinfo.io/{$ip}/json/";
                            
        //                     if ($this->token) {
        //                         $url .= "?key={$this->token}";
        //                     }
                            
        //                     $response = file_get_contents($url);
        //                     $data = json_decode($response);
                            
        //                     return (object) [
        //                         'cityName' => $data->city ?? null,
        //                         'countryName' => $data->country_name ?? null,
        //                         'regionName' => $data->region ?? null,
        //                         'latitude' => $data->latitude ?? null,
        //                         'longitude' => $data->longitude ?? null
        //                     ];
        //                 }
        //             };
        //         }
                
        //         throw new \RuntimeException("The location driver [{$driver}] does not exist.");
        //     });
        // }
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
            
            // if ($city->area && $city->area->edge && $city->area->edge->is_show != 1) {
            //     $shouldNoIndex = true;
            // }
            
            // $organizationsCount = Organization::where('city_id', $city->id)->count();
            // if ($organizationsCount < 3) {
            //     $shouldNoIndex = true;
            // }
            
            // if ($shouldNoIndex) {
            //     SEOMeta::setRobots('noindex,nofollow');
            // }
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