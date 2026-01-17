<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class Tag extends Model
{
    protected $table = 'tags';
    
    protected $fillable = [
        'entity_type',
        'entity_id',
        'name',
        'slug',
        'priority',
        'tag_type',
        'meta_title',
        'meta_description',
        'search_count',
        'click_count',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'search_count' => 'integer',
        'click_count' => 'integer',
        'priority' => 'integer'
    ];
    
    // Константы для entity_type
    const ENTITY_CATEGORY_PRODUCT = 'category_product';
    const ENTITY_PRODUCT = 'product';
    const ENTITY_SERVICE = 'service';
    const ENTITY_ORGANIZATION = 'organization';
    const ENTITY_SUBCATEGORY = 'subcategory';
    const ENTITY_ATTRIBUTE = 'attribute';
    
    // Константы для tag_type
    const TYPE_POPULAR = 'popular';     // Популярные запросы
    const TYPE_RELATED = 'related';     // С этим также ищут
    const TYPE_MATERIAL = 'material';   // Материал
    const TYPE_STYLE = 'style';         // Стиль
    const TYPE_FILTER = 'filter';       // Фильтр
    const TYPE_SEO = 'seo';             // SEO тег
    const TYPE_BRAND = 'brand';         // Бренд
    
    // Время кэширования в секундах
    const CACHE_TTL_SHORT = 300;    // 5 минут
    const CACHE_TTL_MEDIUM = 1800;  // 30 минут
    const CACHE_TTL_LONG = 7200;    // 2 часа
    
    /**
     * Получить теги для сущности (категории, товара и т.д.)
     * Использует кэширование Redis
     */
    public static function getForEntity(string $entityType, int $entityId, ?string $tagType = null, int $limit = 20): array
    {
        $cacheKey = "tags:{$entityType}:{$entityId}:" . ($tagType ?: 'all');
        
        if (Redis::exists($cacheKey)) {
            return json_decode(Redis::get($cacheKey), true);
        }
        
        $query = self::where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->where('is_active', true);
            
        if ($tagType) {
            $query->where('tag_type', $tagType);
        }
        
        $tags = $query->orderBy('priority', 'desc')
            ->orderBy('search_count', 'desc')
            ->orderBy('click_count', 'desc')
            ->limit($limit)
            ->get(['id', 'name', 'slug', 'tag_type', 'search_count', 'click_count', 'priority', 'meta_title'])
            ->toArray();
        
        Redis::setex($cacheKey, self::CACHE_TTL_LONG, json_encode($tags));
        
        return $tags;
    }
    
    /**
     * Добавить теги к сущности (массово)
     */
    public static function addTagsToEntity(string $entityType, int $entityId, array $tags, string $tagType = null, int $priority = 0): void
    {
        $now = now();
        $tagsToInsert = [];
        
        foreach ($tags as $tagName) {
            $tagName = trim($tagName);
            if (empty($tagName)) continue;
            
            $slug = Str::slug($tagName);
            
            // Проверяем, существует ли уже такой тег для этой сущности
            $exists = self::where('entity_type', $entityType)
                ->where('entity_id', $entityId)
                ->where('name', $tagName)
                ->exists();
            
            if (!$exists) {
                $tagsToInsert[] = [
                    'entity_type' => $entityType,
                    'entity_id' => $entityId,
                    'name' => $tagName,
                    'slug' => $slug,
                    'tag_type' => $tagType,
                    'priority' => $priority,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }
        
        if (!empty($tagsToInsert)) {
            DB::table('tags')->insert($tagsToInsert);
            
            // Инвалидируем кэш
            self::clearEntityCache($entityType, $entityId, $tagType);
        }
    }
    
    /**
     * Получить популярные теги для категории товаров
     */
    public static function getPopularForCategory(int $categoryId, int $limit = 15): array
    {
        $cacheKey = "tags:popular:category:{$categoryId}:{$limit}";
        
        if (Redis::exists($cacheKey)) {
            return json_decode(Redis::get($cacheKey), true);
        }
        
        $tags = self::where('entity_type', self::ENTITY_CATEGORY_PRODUCT)
            ->where('entity_id', $categoryId)
            ->where('is_active', true)
            ->whereIn('tag_type', [self::TYPE_POPULAR, self::TYPE_FILTER])
            ->orderBy('search_count', 'desc')
            ->orderBy('priority', 'desc')
            ->limit($limit)
            ->get(['name', 'slug', 'tag_type', 'search_count', 'click_count'])
            ->toArray();
        
        Redis::setex($cacheKey, self::CACHE_TTL_LONG, json_encode($tags));
        
        return $tags;
    }
    
    /**
     * Получить "С этим также ищут" для категории
     */
    public static function getRelatedForCategory(int $categoryId, int $limit = 10): array
    {
        $cacheKey = "tags:related:category:{$categoryId}:{$limit}";
        
        if (Redis::exists($cacheKey)) {
            return json_decode(Redis::get($cacheKey), true);
        }
        
        $tags = self::where('entity_type', self::ENTITY_CATEGORY_PRODUCT)
            ->where('entity_id', $categoryId)
            ->where('is_active', true)
            ->where('tag_type', self::TYPE_RELATED)
            ->orderBy('click_count', 'desc')
            ->orderBy('priority', 'desc')
            ->limit($limit)
            ->get(['name', 'slug', 'tag_type', 'click_count', 'search_count'])
            ->toArray();
        
        Redis::setex($cacheKey, self::CACHE_TTL_LONG, json_encode($tags));
        
        return $tags;
    }
    
    /**
     * Увеличить счетчик поиска для тега
     */
    public static function incrementSearchCount(string $tagName, string $entityType = null, int $entityId = null): void
    {
        $query = DB::table('tags')->where('name', $tagName);
        
        if ($entityType && $entityId) {
            $query->where('entity_type', $entityType)
                  ->where('entity_id', $entityId);
        }
        
        $query->increment('search_count');
        
        // Инвалидируем кэш если есть конкретная сущность
        if ($entityType && $entityId) {
            self::clearEntityCache($entityType, $entityId);
        }
    }
    
    /**
     * Увеличить счетчик кликов для тега
     */
    public static function incrementClickCount(string $tagName, string $entityType = null, int $entityId = null): void
    {
        $query = DB::table('tags')->where('name', $tagName);
        
        if ($entityType && $entityId) {
            $query->where('entity_type', $entityType)
                  ->where('entity_id', $entityId);
        }
        
        $query->increment('click_count');
        
        if ($entityType && $entityId) {
            self::clearEntityCache($entityType, $entityId);
        }
    }
    
    /**
     * Очистить кэш для сущности
     */
    protected static function clearEntityCache(string $entityType, int $entityId, ?string $tagType = null): void
    {
        // Удаляем общий кэш для сущности
        Redis::del("tags:{$entityType}:{$entityId}:all");
        
        // Удаляем кэш по конкретному типу тега если указан
        if ($tagType) {
            Redis::del("tags:{$entityType}:{$entityId}:{$tagType}");
        }
        
        // Удаляем популярные и связанные теги для категорий
        if ($entityType === self::ENTITY_CATEGORY_PRODUCT) {
            Redis::del("tags:popular:category:{$entityId}:*");
            Redis::del("tags:related:category:{$entityId}:*");
        }
        
        // Удаляем топ тегов
        Redis::del("tags:top:*");
    }
    
    /**
     * Получить топ тегов по поискам (вычисляем на лету, но кэшируем)
     */
    public static function getTopTags(int $limit = 50, ?string $entityType = null): array
    {
        $cacheKey = "tags:top:" . ($entityType ?: 'all') . ":{$limit}";
        
        if (Redis::exists($cacheKey)) {
            return json_decode(Redis::get($cacheKey), true);
        }
        
        $query = self::select([
                'name',
                'slug',
                'entity_type',
                DB::raw('SUM(search_count) as total_searches'),
                DB::raw('SUM(click_count) as total_clicks'),
                DB::raw('COUNT(*) as usage_count')
            ])
            ->where('is_active', true)
            ->groupBy('name', 'slug', 'entity_type')
            ->orderBy('total_searches', 'desc')
            ->orderBy('total_clicks', 'desc')
            ->limit($limit);
            
        if ($entityType) {
            $query->where('entity_type', $entityType);
        }
        
        $tags = $query->get()->toArray();
        
        Redis::setex($cacheKey, self::CACHE_TTL_MEDIUM, json_encode($tags));
        
        return $tags;
    }
    
    /**
     * Автодополнение тегов с учетом типа сущности
     */
    public static function autocomplete(string $query, ?string $entityType = null, int $limit = 15): array
    {
        $cacheKey = "tags:autocomplete:" . md5($query . $entityType . $limit);
        
        if (Redis::exists($cacheKey)) {
            return json_decode(Redis::get($cacheKey), true);
        }
        
        $searchQuery = self::where('is_active', true)
            ->where('name', 'like', $query . '%');
            
        if ($entityType) {
            $searchQuery->where('entity_type', $entityType);
        }
        
        $results = $searchQuery->select([
                'name',
                'slug',
                'entity_type',
                'tag_type',
                'search_count',
                DB::raw('COUNT(*) as entity_count')
            ])
            ->groupBy('name', 'slug', 'entity_type', 'tag_type', 'search_count')
            ->orderBy('search_count', 'desc')
            ->orderBy('entity_count', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
        
        Redis::setex($cacheKey, self::CACHE_TTL_SHORT, json_encode($results));
        
        return $results;
    }
    
    /**
     * Массовый импорт тегов для сущности
     * Пример: импорт для категории "Кресты" (id: 3)
     */
    public static function importTags(string $entityType, int $entityId, array $tagGroups): void
    {
        DB::transaction(function () use ($entityType, $entityId, $tagGroups) {
            // Удаляем старые теги для этой сущности
            self::where('entity_type', $entityType)
                ->where('entity_id', $entityId)
                ->delete();
            
            // Добавляем новые теги группами
            foreach ($tagGroups as $tagType => $tags) {
                if (!empty($tags)) {
                    self::addTagsToEntity(
                        $entityType, 
                        $entityId, 
                        is_array($tags) ? $tags : explode(',', $tags), 
                        $tagType,
                        self::getPriorityByTagType($tagType)
                    );
                }
            }
        });
        
        // Полная инвалидация кэша для этой сущности
        self::clearEntityCache($entityType, $entityId);
    }
    
    /**
     * Получить приоритет по типу тега
     */
    protected static function getPriorityByTagType(string $tagType): int
    {
        return match($tagType) {
            self::TYPE_POPULAR, self::TYPE_FILTER => 10,
            self::TYPE_RELATED => 8,
            self::TYPE_SEO => 6,
            self::TYPE_BRAND => 5,
            self::TYPE_MATERIAL => 4,
            self::TYPE_STYLE => 3,
            default => 1,
        };
    }
    
    /**
     * Поиск сущностей по тегу (универсальный метод)
     */
    public static function searchByTag(string $tagName, string $entityType, array $filters = [], int $perPage = 24)
    {
        $modelClass = self::getModelClassByEntityType($entityType);
        
        if (!$modelClass) {
            return collect();
        }
        
        return $modelClass::whereHas('tags', function ($query) use ($tagName) {
                $query->where('name', 'like', $tagName . '%')
                      ->orWhere('slug', 'like', Str::slug($tagName) . '%');
            })
            ->when(!empty($filters), function ($query) use ($filters) {
                foreach ($filters as $key => $value) {
                    if (!empty($value)) {
                        $query->where($key, $value);
                    }
                }
            })
            ->paginate($perPage);
    }
    
  
    
    /**
     * Получить статистику по тегам для админки
     */
    public static function getStats(?string $entityType = null, ?string $tagType = null): array
    {
        $cacheKey = "tags:stats:" . ($entityType ?: 'all') . ":" . ($tagType ?: 'all');
        
        if (Redis::exists($cacheKey)) {
            return json_decode(Redis::get($cacheKey), true);
        }
        
        $query = self::select([
                DB::raw('COUNT(*) as total_tags'),
                DB::raw('SUM(search_count) as total_searches'),
                DB::raw('SUM(click_count) as total_clicks'),
                DB::raw('AVG(priority) as avg_priority'),
                DB::raw('COUNT(DISTINCT name) as unique_tags'),
                DB::raw('COUNT(DISTINCT entity_type) as entity_types_count'),
                DB::raw('COUNT(DISTINCT entity_id) as entities_count')
            ])
            ->where('is_active', true);
            
        if ($entityType) {
            $query->where('entity_type', $entityType);
        }
        
        if ($tagType) {
            $query->where('tag_type', $tagType);
        }
        
        $stats = $query->first()->toArray();
        
        Redis::setex($cacheKey, self::CACHE_TTL_MEDIUM, json_encode($stats));
        
        return $stats;
    }
    
    /**
     * Получить все теги для товара с SEO информацией
     */
    public static function getProductTagsWithSeo(int $productId): array
    {
        $cacheKey = "tags:product:seo:{$productId}";
        
        if (Redis::exists($cacheKey)) {
            return json_decode(Redis::get($cacheKey), true);
        }
        
        $tags = self::where('entity_type', self::ENTITY_PRODUCT)
            ->where('entity_id', $productId)
            ->where('is_active', true)
            ->whereNotNull('meta_title')
            ->whereNotNull('meta_description')
            ->orderBy('priority', 'desc')
            ->get(['name', 'slug', 'meta_title', 'meta_description', 'tag_type'])
            ->toArray();
        
        Redis::setex($cacheKey, self::CACHE_TTL_LONG, json_encode($tags));
        
        return $tags;
    }
    
    /**
     * Пример использования для категории "Кресты" (id: 3)
     */
    public static function exampleForCrossesCategory(): void
    {
        // Популярные запросы
        $popularTags = [
            'Кресты на могилу из металла',
            'Кресты на могилу из нержавейки',
            'Кресты на могилу из дерева',
            'Кресты на могилу из гранита',
            'Купить крест на могилу недорого',
            'Крест на могилу с фигурой Иисуса'
        ];
        
        // С этим также ищут
        $relatedTags = [
            'оградки для могил',
            'столики для кладбища',
            'цоколи для памятников',
            'вазы для цветов на могилу'
        ];
        
        // Материалы
        $materialTags = [
            'металлические кресты',
            'деревянные кресты',
            'гранитные кресты',
            'бронзовые кресты'
        ];
        
        // Импортируем все теги для категории Кресты (id: 3)
        self::importTags(self::ENTITY_CATEGORY_PRODUCT, 3, [
            self::TYPE_POPULAR => $popularTags,
            self::TYPE_RELATED => $relatedTags,
            self::TYPE_MATERIAL => $materialTags
        ]);
    }
    
    /**
     * Scope для поиска по типу сущности
     */
    public function scopeByEntity($query, string $entityType, int $entityId)
    {
        return $query->where('entity_type', $entityType)
                     ->where('entity_id', $entityId);
    }
    
    /**
     * Scope для активных тегов
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    /**
     * Scope для типа тега
     */
    public function scopeOfType($query, string $tagType)
    {
        return $query->where('tag_type', $tagType);
    }
}