<?php

namespace App\Console\Commands;

use App\Models\City;
use Illuminate\Console\Command;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RereadCities extends Command
{
    protected $signature = 'app:reread-cities
                            {--limit=500 : Лимит городов за один запуск}
                            {--concurrency=3 : Количество одновременных запросов}
                            {--city-id= : Обновить конкретный город по ID}
                            {--city-name= : Обновить город по названию}
                            {--test : Тестовый режим - только проверка API}
                            {--dry-run : Только найти координаты, не сохранять}
                            {--strict : Строгий поиск (только города)}
                            {--retry=3 : Количество попыток при ошибке API}
                            {--chunk-size=1000 : Размер чанка для выборки из БД}
                            {--batch-update=100 : Размер батча для обновления БД}
                            {--skip-validate : Пропустить проверку координат}';

    protected $description = 'Массовое обновление координат населенных пунктов через DaData API';

    protected $client;
    protected $apiKey;
    protected $secretKey;
    protected $updatedCount = 0;
    protected $failedCount = 0;
    protected $totalProcessed = 0;
    protected $batchData = [];
    protected $useDaData = true;
    
    // Расширенный список префиксов и типов населенных пунктов
    protected $prefixes = [
        // Села и деревни
        'с.' => 'село',
        'c.' => 'село',
        'д.' => 'деревня',
        'дер.' => 'деревня',
        'с/пос.' => 'сельское поселение',
        'с-з.' => 'сельское поселение',
        
        // Поселки
        'пос.' => 'поселок',
        'п.' => 'поселок',
        'пгт.' => 'поселок городского типа',
        'пгт' => 'поселок городского типа',
        'рп.' => 'рабочий поселок',
        'рп' => 'рабочий поселок',
        'п/ст.' => 'поселок при станции',
        'п.ст.' => 'поселок при станции',
        'кп.' => 'курортный поселок',
        
        // Города
        'г.' => 'город',
        'гор.' => 'город',
        
        // Станции и станицы
        'ст.' => 'станция',
        'ст-ца' => 'станица',
        'ст.' => 'станица',
        'ж/д_ст.' => 'железнодорожная станция',
        'ж/д ст.' => 'железнодорожная станция',
        'жд ст.' => 'железнодорожная станция',
        'ж/д кв.' => 'железнодорожный квартал',
        
        // Хутора и аулы
        'х.' => 'хутор',
        'хут.' => 'хутор',
        'а.' => 'аул',
        'аул.' => 'аул',
        
        // Улицы и микрорайоны (обычно не должно быть в городах, но на всякий случай)
        'ул.' => 'улица',
        'у.' => 'улица',
        'мкр.' => 'микрорайон',
        'мкр' => 'микрорайон',
        'кв-л' => 'квартал',
        
        // Общие
        'нп.' => 'населенный пункт',
        'м.' => 'местечко',
        'заст.' => 'застава',
        'корд.' => 'кордон',
        
        // Особые случаи (с пробелами и без)
        'п ' => 'поселок', // п Татарский Ключ
        'с ' => 'село',    // с Ехэ-Цакир
        'рп ' => 'рабочий поселок', // рп Чердаклы
        'пгт ' => 'поселок городского типа', // пгт Прогресс
    ];

    // Специфичные префиксы для DaData
    protected $dadataSettlementTypes = [
        'село' => 'село',
        'деревня' => 'деревня',
        'поселок' => 'поселок',
        'поселок городского типа' => 'поселок городского типа',
        'рабочий поселок' => 'рабочий поселок',
        'город' => 'город',
        'станица' => 'станица',
        'хутор' => 'хутор',
        'аул' => 'аул',
        'железнодорожная станция' => 'железнодорожная станция',
        'железнодорожный разъезд' => 'железнодорожный разъезд',
        'микрорайон' => 'микрорайон',
    ];

    public function handle()
    {
        $startTime = microtime(true);
        $memoryStart = memory_get_usage(true);

        $this->initApiClient();
        
        if ($this->option('test')) {
            return $this->testApi();
        }

        if ($cityId = $this->option('city-id')) {
            return $this->processSingleCityById($cityId);
        }

        if ($cityName = $this->option('city-name')) {
            return $this->processSingleCityByName($cityName);
        }

        $this->massUpdate();

        $executionTime = microtime(true) - $startTime;
        $memoryUsed = (memory_get_peak_usage(true) - $memoryStart) / 1024 / 1024;

        $this->outputSummary($executionTime, $memoryUsed);
        
        return $this->failedCount === 0 ? Command::SUCCESS : Command::FAILURE;
    }

    protected function initApiClient()
    {
        $this->apiKey = env('DADATA_API_KEY');
        $this->secretKey = env('DADATA_SECRET_KEY');
        
        if (!$this->apiKey) {
            $this->useDaData = false;
            $this->apiKey = env('OPENCAGE_API_KEY') ?: env('DADATA_API_KEY');
            $this->warn('⚠️  DaData ключ не найден, используем OpenCage API');
        }

        $this->client = new Client([
            'timeout' => 20,
            'connect_timeout' => 15,
            'http_errors' => true,
        ]);
    }

    protected function testApi()
    {
        $this->info('Тестирование API с разными форматами названий...');
        
        $testCases = [
            'пгт Прогресс',
            'рп. Озинки',
            'п. Татарский Ключ',
            'с. Ехэ-Цакир',
            'ул. Бортой',
            'с. Хамней',
            'рп Чердаклы',
            'пгт. Прогресс',
            'c. Чигири',
            'пос. Чигири',
            'д. Чигири',
            'ст-ца Чигири',
            'ж/д_ст. Чигири',
        ];

        foreach ($testCases as $testName) {
            $this->processTestName($testName, 'Амурская область');
        }
        
        return Command::SUCCESS;
    }

    protected function processTestName($cityName, $region = null)
    {
        $this->info("\n🔍 Тест: '{$cityName}' в регионе: " . ($region ?? 'не указан'));
        
        $parsed = $this->parseCityName($cityName);
        $this->info("   📝 Парсинг: '{$parsed['name']}' (тип: {$parsed['type']})");
        $this->info("   🏷️  Оригинал: '{$parsed['original']}', префикс: " . ($parsed['prefix'] ?? 'нет'));
        
        try {
            $coordinates = $this->getCoordinates($parsed['name'], $region, $parsed['type']);
            
            if ($coordinates) {
                $this->info("   ✅ Координаты: {$coordinates['lat']}, {$coordinates['lng']}");
                $this->info("   📍 Полный адрес: " . ($coordinates['address'] ?? 'не указан'));
                $this->info("   🔧 Источник: " . ($coordinates['source'] ?? 'неизвестно'));
                
                if (isset($coordinates['settlement_type'])) {
                    $this->info("   🏘️  Тип населенного пункта (DaData): {$coordinates['settlement_type']}");
                }
            } else {
                $this->error("   ❌ Координаты не найдены");
                
                // Пробуем альтернативные методы
                $this->tryAlternativeMethods($parsed['name'], $region, $parsed['type']);
            }
        } catch (\Exception $e) {
            $this->error("   ❌ Ошибка: " . $e->getMessage());
        }
    }

    protected function parseCityName($cityName)
    {
        $cityName = trim($cityName);
        $original = $cityName;
        
        // Заменяем неразрывные пробелы на обычные
        $cityName = str_replace(["\xc2\xa0", " "], ' ', $cityName);
        
        // Проверяем наличие префикса
        foreach ($this->prefixes as $prefix => $type) {
            // Нормализуем префикс
            $prefix = trim($prefix);
            
            // Варианты для поиска
            $variants = [
                $prefix . ' ',  // префикс с пробелом
                $prefix,        // префикс без пробела
                rtrim($prefix, '.') . ' ', // без точки с пробелом
                rtrim($prefix, '.'),       // без точки
            ];
            
            foreach ($variants as $variant) {
                $variantLength = mb_strlen($variant);
                
                // Проверяем начало строки (регистронезависимо)
                if (mb_stripos($cityName, $variant) === 0) {
                    $name = trim(mb_substr($cityName, $variantLength));
                    
                    if (!empty($name)) {
                        return [
                            'name' => $this->normalizeName($name),
                            'type' => $type,
                            'original' => $original,
                            'prefix' => $prefix,
                            'has_prefix' => true,
                        ];
                    }
                }
            }
        }
        
        // Проверяем, есть ли тип в середине названия (редкий случай)
        if (preg_match('/^(.+?)\s+(село|деревня|поселок|город|станица|хутор|аул|пгт|рп|ст|ж\/д|жд)\b\.?$/iu', $cityName, $matches)) {
            $name = trim($matches[1]);
            $detectedType = mb_strtolower(trim($matches[2]));
            
            // Маппинг сокращений на полные названия
            $typeMap = [
                'пгт' => 'поселок городского типа',
                'рп' => 'рабочий поселок',
                'ст' => 'станция',
                'ж/д' => 'железнодорожная станция',
                'жд' => 'железнодорожная станция',
            ];
            
            $type = $typeMap[$detectedType] ?? $detectedType;
            
            return [
                'name' => $this->normalizeName($name),
                'type' => $type,
                'original' => $original,
                'prefix' => $detectedType,
                'has_prefix' => true,
            ];
        }
        
        // Если префикс не найден, возможно название уже содержит тип
        $patterns = [
            '/(село|деревня|поселок|город|станица|хутор|аул|микрорайон|квартал)\s+(.+)$/iu',
            '/(с|д|п|г|ст|х|а|мкр|кв)\.?\s+(.+)$/iu',
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $cityName, $matches)) {
                $detectedType = mb_strtolower(trim($matches[1]));
                $name = trim($matches[2]);
                
                // Маппинг букв на типы
                $letterMap = [
                    'с' => 'село',
                    'д' => 'деревня',
                    'п' => 'поселок',
                    'г' => 'город',
                    'ст' => 'станица',
                    'х' => 'хутор',
                    'а' => 'аул',
                    'мкр' => 'микрорайон',
                    'кв' => 'квартал',
                ];
                
                $type = $letterMap[$detectedType] ?? $detectedType;
                
                return [
                    'name' => $this->normalizeName($name),
                    'type' => $type,
                    'original' => $original,
                    'prefix' => $detectedType,
                    'has_prefix' => true,
                ];
            }
        }
        
        // Возвращаем как есть, но нормализуем
        return [
            'name' => $this->normalizeName($cityName),
            'type' => $this->option('strict') ? 'город' : 'населенный пункт',
            'original' => $original,
            'prefix' => null,
            'has_prefix' => false,
        ];
    }

    protected function normalizeName($name)
    {
        // Убираем лишние пробелы
        $name = preg_replace('/\s+/', ' ', trim($name));
        
        // Первая буква заглавная, остальные строчные (для русского языка)
        if (preg_match('/^[а-яё]/iu', $name)) {
            $name = mb_strtoupper(mb_substr($name, 0, 1)) . mb_substr($name, 1);
        }
        
        return $name;
    }

    protected function getCoordinates($cityName, $region = null, $settlementType = null, $retry = null)
    {
        $retry = $retry ?? $this->option('retry');
        
        for ($attempt = 1; $attempt <= $retry; $attempt++) {
            try {
                if ($this->useDaData) {
                    $response = $this->client->send(
                        $this->createDaDataRequestForSingle($cityName, $region, $settlementType)
                    );
                    return $this->parseDaDataResponse($response);
                } else {
                    $response = $this->client->send(
                        $this->createOpenCageRequestForSingle($cityName, $region)
                    );
                    return $this->parseOpenCageResponse($response);
                }
                
            } catch (RequestException $e) {
                if ($attempt === $retry) {
                    throw $e;
                }
                usleep(500000 * $attempt); // Экспоненциальная задержка
            }
        }
        
        return null;
    }

    protected function createDaDataRequestForSingle($cityName, $region = null, $settlementType = null)
    {
        $query = [
            'query' => $cityName,
            'count' => 5, // Берем больше результатов для лучшего поиска
            'language' => 'ru',
        ];

        // Фильтрация по типу населенного пункта
        if ($settlementType && isset($this->dadataSettlementTypes[$settlementType])) {
            $query['from_bound'] = ['value' => 'settlement'];
            $query['to_bound'] = ['value' => 'settlement'];
        }

        // Добавляем регион для более точного поиска
        if ($region) {
            $query['locations'] = [
                [
                    'region' => $this->normalizeRegionName($region),
                    'country' => 'Россия'
                ]
            ];
            
            // Также ищем по всей России если не найдено в регионе
            $query['restrict_value'] = false;
        }

        return new Request('POST', 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address', [
            'Authorization' => 'Token ' . $this->apiKey,
            'X-Secret' => $this->secretKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ], json_encode($query));
    }

    protected function normalizeRegionName($region)
    {
        // Убираем лишние слова
        $replacements = [
            'республика' => '',
            'респ.' => '',
            'край' => '',
            'область' => '',
            'обл.' => '',
            'автономный округ' => '',
            'ао' => '',
            'автономная область' => '',
        ];
        
        $region = trim(str_ireplace(array_keys($replacements), array_values($replacements), $region));
        
        // Убираем двойные пробелы
        $region = preg_replace('/\s+/', ' ', $region);
        
        return $region;
    }

    protected function parseDaDataResponse($response)
    {
        $data = json_decode($response->getBody()->getContents(), true);
        
        if (empty($data['suggestions'])) {
            return null;
        }

        // Ищем лучший результат
        foreach ($data['suggestions'] as $suggestion) {
            $item = $suggestion['data'];
            
            // Проверяем, что это населенный пункт в России
            if ($item['country'] !== 'Россия') {
                continue;
            }
            
            // Проверяем наличие координат
            if (!empty($item['geo_lat']) && !empty($item['geo_lon'])) {
                // Проверяем тип населенного пункта
                $settlementType = $item['settlement_type'] ?? $item['city_type'] ?? null;
                
                return [
                    'lat' => (float) $item['geo_lat'],
                    'lng' => (float) $item['geo_lon'],
                    'address' => $suggestion['value'],
                    'settlement_type' => $settlementType,
                    'region' => $item['region_with_type'] ?? null,
                    'source' => 'dadata',
                    'quality' => $item['qc_geo'] ?? 0, // Качество геокодирования
                ];
            }
        }
        
        return null;
    }

    protected function tryAlternativeMethods($cityName, $region = null, $settlementType = null)
    {
        $this->info("   🔄 Пробуем альтернативные методы поиска...");
        
        // 1. Пробуем без типа
        $this->info("   1. Поиск без указания типа населенного пункта...");
        try {
            $coordinates = $this->getCoordinates($cityName, $region);
            if ($coordinates) {
                $this->info("      ✅ Найдено без типа: {$coordinates['lat']}, {$coordinates['lng']}");
                return $coordinates;
            }
        } catch (\Exception $e) {
            // Игнорируем ошибку
        }
        
        // 2. Пробуем с добавлением "село"/"поселок" к названию
        if (!$settlementType || in_array($settlementType, ['село', 'поселок', 'деревня'])) {
            $this->info("   2. Поиск с добавлением 'село' к названию...");
            try {
                $coordinates = $this->getCoordinates("село $cityName", $region);
                if ($coordinates) {
                    $this->info("      ✅ Найдено с 'село': {$coordinates['lat']}, {$coordinates['lng']}");
                    return $coordinates;
                }
            } catch (\Exception $e) {
                // Игнорируем ошибку
            }
        }
        
        // 3. Пробуем искать только по региону
        if ($region) {
            $this->info("   3. Поиск только по названию в указанном регионе...");
            try {
                if ($this->useDaData) {
                    // Специальный запрос для DaData
                    $query = [
                        'query' => $cityName,
                        'count' => 1,
                        'locations' => [['region' => $this->normalizeRegionName($region)]],
                        'restrict_value' => true,
                    ];
                    
                    $request = new Request('POST', 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address', [
                        'Authorization' => 'Token ' . $this->apiKey,
                        'X-Secret' => $this->secretKey,
                        'Content-Type' => 'application/json',
                    ], json_encode($query));
                    
                    $response = $this->client->send($request);
                    $coordinates = $this->parseDaDataResponse($response);
                    
                    if ($coordinates) {
                        $this->info("      ✅ Найдено с ограничением по региону: {$coordinates['lat']}, {$coordinates['lng']}");
                        return $coordinates;
                    }
                }
            } catch (\Exception $e) {
                // Игнорируем ошибку
            }
        }
        
        $this->info("   ❌ Все методы не дали результатов");
        return null;
    }

    protected function processSingleCityById($cityId)
    {
        $city = City::with('area')
            ->whereHas('organizations')
            ->find($cityId);

        if (!$city) {
            $this->error("Населенный пункт с ID {$cityId} не найден или нет организаций");
            return Command::FAILURE;
        }

        return $this->processCity($city);
    }

    protected function processSingleCityByName($cityName)
    {
        $city = City::with('area')
            ->whereHas('organizations')
            ->where('title', 'LIKE', "%{$cityName}%")
            ->first();

        if (!$city) {
            $this->error("Населенный пункт с названием '{$cityName}' не найден");
            return Command::FAILURE;
        }

        return $this->processCity($city);
    }

    protected function processCity(City $city)
    {
        $parsed = $this->parseCityName($city->title);
        
        $this->info("Обработка населенного пункта: {$city->title}");
        $this->info("   📝 Парсинг: '{$parsed['name']}' (тип: {$parsed['type']})");
        $this->info("   🏙️  Регион: " . ($city->area->title ?? 'не указан'));
        
        try {
            $coordinates = $this->getCoordinates(
                $parsed['name'], 
                $city->area->title ?? null, 
                $parsed['type']
            );
            
            if (!$coordinates) {
                $this->warn("   ⚠️  Основной поиск не дал результатов");
                $coordinates = $this->tryAlternativeMethods($parsed['name'], $city->area->title ?? null, $parsed['type']);
            }
            
            if ($coordinates) {
                $this->info("   ✅ Найдены координаты: {$coordinates['lat']}, {$coordinates['lng']}");
                $this->info("   📍 Адрес: " . ($coordinates['address'] ?? 'не указан'));
                
                if (!$this->option('dry-run')) {
                    $updateResult = $city->update([
                        'width' => $coordinates['lat'],
                        'longitude' => $coordinates['lng'],
                    ]);
                    
                    if ($updateResult) {
                        $this->info("   ✅ Координаты сохранены в БД");
                        return Command::SUCCESS;
                    } else {
                        $this->error("   ❌ Ошибка сохранения в БД");
                        return Command::FAILURE;
                    }
                } else {
                    $this->info("   ⚠️  Режим dry-run - координаты не сохранены");
                    return Command::SUCCESS;
                }
            } else {
                $this->error("   ❌ Координаты не найдены");
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error("   ❌ Ошибка: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    protected function massUpdate()
    {
        $limit = $this->option('limit');
        $concurrency = min($this->option('concurrency'), 10); // Ограничиваем для безопасности
        $chunkSize = $this->option('chunk-size');
        $batchSize = $this->option('batch-update');

        $totalQuery = City::whereHas('organizations')
            ->where(function($q) {
                $q->whereNull('width')
                  ->orWhereNull('longitude');
            });

        $total = min($totalQuery->count(), $limit);
        
        if ($total === 0) {
            $this->info('✅ Все населенные пункты уже имеют координаты');
            return;
        }

        $this->info("🚀 Начинаем обработку {$total} населенных пунктов");
        $this->info("⚡ Конкурентность: {$concurrency} запросов");
        $this->info("🔧 Режим: " . ($this->useDaData ? 'DaData' : 'OpenCage'));

        $progressBar = $this->output->createProgressBar($total);
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');

        City::whereHas('organizations')
            ->where(function($q) {
                $q->whereNull('width')
                  ->orWhereNull('longitude');
            })
            ->with('area')
            ->take($limit)
            ->chunkById($chunkSize, function ($cities) use ($concurrency, $batchSize, $progressBar) {
                $this->processCitiesBatch($cities, $concurrency, $batchSize, $progressBar);
            });

        $progressBar->finish();
        $this->newLine();
    }

    // Остальные методы (processCitiesBatch, createRequest, parseResponse, saveBatch и т.д.)
    // остаются аналогичными предыдущей версии, но с учетом новой логики парсинга

    protected function validateCoordinates($coordinates)
    {
        if ($this->option('skip-validate')) {
            return true;
        }

        if (!isset($coordinates['lat'], $coordinates['lng'])) {
            return false;
        }

        $lat = $coordinates['lat'];
        $lng = $coordinates['lng'];

        return is_numeric($lat) && is_numeric($lng) &&
               $lat >= 41.0 && $lat <= 82.0 &&
               $lng >= 19.0 && $lng <= 190.0;
    }

    protected function outputSummary($executionTime, $memoryUsed)
    {
        $this->newLine(2);
        $this->info("═══════════════════════════════════════════════════════");
        $this->info("📊 ИТОГИ ОБРАБОТКИ НАСЕЛЕННЫХ ПУНКТОВ");
        $this->info("═══════════════════════════════════════════════════════");
        $this->info("✅ Обновлено: {$this->updatedCount}");
        $this->info("❌ Ошибки: {$this->failedCount}");
        $this->info("📈 Всего обработано: {$this->totalProcessed}");
        $this->info("⏱️  Время: " . round($executionTime, 2) . " сек.");
        $this->info("📊 Скорость: " . round($this->totalProcessed / $executionTime, 2) . " записей/сек");
        $this->info("💾 Память: " . round($memoryUsed, 2) . " МБ");
        $this->info("🔌 API: " . ($this->useDaData ? 'DaData' : 'OpenCage'));
        
        if ($this->option('dry-run')) {
            $this->warn("⚠️  РЕЖИМ DRY-RUN - данные не сохранены!");
        }
        
        $this->info("═══════════════════════════════════════════════════════");
    }
}