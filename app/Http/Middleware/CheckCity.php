<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\City;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckCity
{
    // Время кэширования в секундах
    const CITY_CACHE_TTL = 3600; // 1 час
    const DEFAULT_CITY_CACHE_TTL = 86400; // 24 часа
    const VALIDITY_CACHE_TTL = 1800; // 30 минут

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
        if (!$city) return false;

        // Кешируем проверку валидности города
        return Cache::remember("city_valid_{$city->id}", self::VALIDITY_CACHE_TTL, function() use ($city) {
            
            // Предзагружаем необходимые отношения если их нет
            if (!$city->relationLoaded('area.edge')) {
                $city->load(['area.edge']);
            }
            
            // Проверяем область и границу
            if (!$city->area || !$city->area->edge || $city->area->edge->is_show != 1) {
                return false;
            }

            // Кэшируем проверку наличия организаций
            return Cache::remember("city_has_orgs_{$city->id}", self::VALIDITY_CACHE_TTL, function() use ($city) {
                return Organization::where('city_id', $city->id)
                    ->where('status', 1)
                    ->exists();
            });
        });
    }

    public function handle(Request $request, Closure $next)
    {
        $citySlug = $request->segment(1);
        $path = $request->path();
        
        // Пропускаем специальные маршруты
        $excludedRoutes = ['livewire', 'admin', 'api', 'storage', 'vendor', 'css', 'js', 'img', 'fonts'];
        if (in_array($citySlug, $excludedRoutes) || $request->is($excludedRoutes)) {
            return $next($request);
        }

        // Кешируем город по умолчанию на 24 часа
        $defaultCity = Cache::remember('default_city', self::DEFAULT_CITY_CACHE_TTL, function() {
            return City::where('selected_admin', 1)
                ->with(['area.edge'])
                ->first(['id', 'slug', 'area_id']);
        });

        $currentCityId = $request->cookie('city');
        $currentCity = $currentCityId ? $this->getCachedCity($currentCityId) : null;
        
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

        // Для городских страниц - кешируем запрос города
        $city = Cache::remember("city_slug_{$citySlug}", self::CITY_CACHE_TTL, function() use ($citySlug) {
            return City::with(['area.edge'])
                ->where('slug', $citySlug)
                ->first(['id', 'slug', 'area_id']);
        });

        // Проверка города
        if (!$this->isValidCity($city)) {
            return $this->handleInvalidCity($defaultCity, $currentCity);
        }

        // Если город в URL валиден - обновляем куки
        return $next($request)
            ->withCookie(cookie('city', $city->id, 20*24*60, '/'));
    }

    protected function getCachedCity($cityId)
    {
        return Cache::remember("city_{$cityId}", self::CITY_CACHE_TTL, function() use ($cityId) {
            return City::with(['area.edge'])->find($cityId, ['id', 'slug', 'area_id']);
        });
    }
}