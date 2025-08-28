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
        if ($currentCity && $this->isValidCity($currentCity)) {
            return redirect('/'.$currentCity->slug)
                ->withCookie(cookie('city', $currentCity->id, 20*24*60, '/'));
        }

        // Иначе используем город по умолчанию
        if ($defaultCity) {
            return redirect('/'.$defaultCity->slug)
                ->withCookie(cookie('city', $defaultCity->id, 20*24*60, '/'));
        }

        abort(404, 'City not found');
    }

    protected function isValidCity(?City $city): bool
    {
        if (!$city || !$city->area || !$city->area->edge || $city->area->edge->is_show != 1) {
            return false;
        }

        $orgCount = Cache::remember("city_org_count_{$city->id}", 3600, function() use ($city) {
            return Organization::where('city_id', $city->id)->count();
        });

        return $orgCount >= 1;
    }

    public function handle(Request $request, Closure $next)
    {
        $citySlug = $request->segment(1);
        $path = $request->path();
        
        // Пропускаем специальные маршруты
        $excludedRoutes = ['livewire', 'admin', 'api', 'storage', 'vendor', 'css', 'js'];
        if (in_array($citySlug, $excludedRoutes) || $request->is($excludedRoutes)) {
            return $next($request);
        }

        $defaultCity = City::where('selected_admin', 1)->first();
        $currentCityId = $request->cookie('city');
        $currentCity = $currentCityId ? City::find($currentCityId) : null;
        
        // Для главной страницы
        if ($path === '/' || empty($citySlug)) {
            // Если есть валидный город в куках - редирект на него
            if ($currentCity && $this->isValidCity($currentCity)) {
                return redirect('/'.$currentCity->slug);
            }
            
            // Иначе устанавливаем город по умолчанию
            if ($defaultCity && $this->isValidCity($defaultCity)) {
                return redirect('/'.$defaultCity->slug)
                    ->withCookie(cookie('city', $defaultCity->id, 20*24*60, '/'));
            }
            
            // Если нет валидного города - показываем главную без редиректа
            return $next($request);
        }

        // Для городских страниц
        $city = City::with(['area.edge'])->where('slug', $citySlug)->first();

        // Проверка города
        if (!$this->isValidCity($city)) {
            return $this->handleInvalidCity($defaultCity, $currentCity);
        }

        // Если город в URL валиден - обновляем куки
        return $next($request)
            ->withCookie(cookie('city', $city->id, 20*24*60, '/'));
    }
}