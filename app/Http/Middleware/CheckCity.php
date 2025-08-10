<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\City;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CheckCity
{
    protected function handleInvalidCity(?City $defaultCity, ?City $currentCity = null)
    {
        // Если есть текущий город в куках и он валиден - используем его
        if ($currentCity && $currentCity->area && $currentCity->area->edge && $currentCity->area->edge->is_show == 1) {
            $orgCount = Cache::remember("city_org_count_{$currentCity->id}", 86400, function() use ($currentCity) {
                return Organization::where('city_id', $currentCity->id)->count();
            });
            
            if ($orgCount >= 3) {
                return redirect('/'.$currentCity->slug);
            }
        }

        // Иначе используем город по умолчанию
        if ($defaultCity) {
            return redirect('/'.$defaultCity->slug)
                ->withCookie(cookie('city', $defaultCity->id, 20*24*60, '/'));
        }

        abort(404, 'City not found');
    }

    public function handle(Request $request, Closure $next)
    {
        $citySlug = request()->segment(1);
        $isHomepage = $request->path() === '/';
        
        // Пропускаем специальные маршруты
        if ($citySlug === 'livewire' || $citySlug === 'admin') {
            return $next($request);
        }

        $defaultCity = City::where('selected_admin', 1)->first();
        $currentCityId = $request->cookie('city');
        $currentCity = $currentCityId ? City::find($currentCityId) : null;
        
        // Для главной страницы
        if ($isHomepage) {
            // Если есть валидный город в куках - редирект на него
            if ($currentCity && $currentCity->slug !== ($defaultCity->slug ?? null)) {
                return redirect('/'.$currentCity->slug);
            }
            
            // Иначе устанавливаем город по умолчанию
            if ($defaultCity) {
                return $next($request)
                    ->withCookie(cookie('city', $defaultCity->id, 20*24*60, '/'));
            }
            
            return $next($request);
        }

        // Для всех остальных страниц
        $city = City::with(['area.edge'])->where('slug', $citySlug)->first();

        // Проверка города и региона
        if (!$city || !$city->area || !$city->area->edge || $city->area->edge->is_show != 1) {
            return $this->handleInvalidCity($defaultCity, $currentCity);
        }

        // Проверка количества организаций
        $orgCount = Cache::remember("city_org_count_{$city->id}", 86400, function() use ($city) {
            return Organization::where('city_id', $city->id)->count();
        });

        if ($orgCount < 3) {
            return $this->handleInvalidCity($defaultCity, $currentCity);
        }

        // Если город в URL валиден - обновляем куки
        return $next($request)
            ->withCookie(cookie('city', $city->id, 20*24*60, '/'));
    }
}