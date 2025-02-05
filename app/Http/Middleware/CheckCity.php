<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\City;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCity
{
    public function handle(Request $request, Closure $next)
    {
        // Параметр {city}, получаем из маршрута
        //$citySlug = $request->route('city');
        $citySlug = request()->segment(1);
        
        // Поиск города в таблице \cities\
        $city = City::where('slug', $citySlug)->first();

        if (!$city) {
            // Город не найден, возвращаем 404
            abort(404, 'City not found');
        }

        // Сохраняем выбранный город в cookie
        setcookie("city", $city->id, time() + 20 * 24 * 60 * 60, '/');

        // Делаем объект города доступным в запросе
        $request->merge(['city' => $city]);

        // Делаем объект города глобально доступным через фасад app()
        app()->instance('current_city', $city);

        return $next($request);
    }
}
