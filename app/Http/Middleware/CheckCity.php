<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\City;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCity
{
    // public function handle(Request $request, Closure $next)
    // {
    //     // Параметр {city}, получаем из маршрута
    //     //$citySlug = $request->route('city');
    //     $citySlug = request()->segment(1);
        
    //     // Поиск города в таблице \cities\
    //     $city = City::where('slug', $citySlug)->first();

    //     if (!$city) {
    //         // Город не найден, возвращаем 404
    //         abort(404, 'City not found');
    //     }

    //     // Сохраняем выбранный город в cookie
    //     setcookie("city", $city->id, time() + 20 * 24 * 60 * 60, '/');

    //     // Делаем объект города доступным в запросе
    //     $request->merge(['city' => $city]);

    //     // Делаем объект города глобально доступным через фасад app()
    //     app()->instance('current_city', $city);

    //     return $next($request);
    // }


    public function handle(Request $request, Closure $next)
        {
            // Параметр {city}, получаем из маршрута
            $citySlug = request()->segment(1);
            $page = request()->segment(2);
            if($citySlug!='livewire' && $page!='admin'){
                // Если город не выбран, перенаправляем на город по умолчанию
                if (!$citySlug) {
                    $defaultCity = City::where('selected_admin', 1)->first(); // Предположим, что есть поле is_default
                    if ($defaultCity) {
                        setcookie('city', '', -1, '/');
                        setcookie("city", $defaultCity->id, time()+20*24*60*60,'/');
                        header("location: /".$defaultCity->slug);        
                        die;
                    }
                }

                // Поиск города в таблице \cities\
                $city = City::where('slug', $citySlug)->first();

                if (!$city || $city->area->edge->is_show!=1) {
                    $defaultCity = City::where('selected_admin', 1)->first(); // Предположим, что есть поле is_default
                    if ($defaultCity) {
                        setcookie('city', '', -1, '/');
                        setcookie("city", $defaultCity->id, time()+20*24*60*60,'/');
                        header("location: /".$defaultCity->slug);        
                        die;
                    }
                }


                // Сохраняем выбранный город в cookie
                setcookie("city", $city->id, time() + 20 * 24 * 60 * 60, '/');

                // Делаем объект города доступным в запросе
                $request->merge(['city' => $city]);

                // Делаем объект города глобально доступным через фасад app()
                app()->instance('current_city', $city);

                return $next($request);
                }
                                return $next($request);

            }
                
}
