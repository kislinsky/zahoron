<?php

namespace App\Services\Parser;

use App\Models\Burial;
use App\Models\Cemetery;
use App\Models\City;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use OpenSpout\Reader\Common\Creator\ReaderFactory;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Rap2hpoutre\FastExcel\FastExcel;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Facades\Redis;

class ParserBurialService
{
    public static function index($request)
    {
        try {
            set_time_limit(3600);
            ignore_user_abort(true);

            Log::info('Import started');

            // Валидация
            $validated = $request->validate([
                'files'       => 'required|array',
                'files.*'     => 'file|mimes:xlsx,xls',
                'id_cemetery' => 'nullable|integer',
            ]);

            $files = $request->file('files');
            $createdBurials = 0;
            $skippedRows = 0;
            $errors = [];

            foreach ($files as $file) {
                if (!$file->isValid()) {
                    $errors[] = "Файл {$file->getClientOriginalName()} не валиден";
                    continue;
                }

                try {
                    Log::info('Processing file: ' . $file->getClientOriginalName());
                    $spreadsheet = IOFactory::load($file->getRealPath());
                    $sheet = $spreadsheet->getActiveSheet();

                    $allData = $sheet->toArray();
                    if (empty($allData)) {
                        $errors[] = "Файл {$file->getClientOriginalName()} пустой";
                        continue;
                    }

                    $titles = $allData[0];
                    $burialsData = array_slice($allData, 1);

                    $filteredTitles = array_filter($titles, fn($value) => $value !== null);
                    $columns = array_flip($filteredTitles);

                    Log::info('Columns found: ' . implode(', ', array_keys($columns)));


                    $requiredColumns = [
                        'City', 'LastName', 'FirstName',
                        'BirthDate', 'DeathDate', 'Latitude', 'Longitude'
                    ];

                    if (!isset($request->id_cemetery) && $request->id_cemetery != null) {
                        $requiredColumns[] = 'Cemetery';
                    } else {
                        $cemetery = Cemetery::find($request->id_cemetery);
                    }

                    foreach ($requiredColumns as $col) {
                        if (!isset($columns[$col])) {
                            $errorMsg = "В файле {$file->getClientOriginalName()} отсутствует обязательная колонка: {$col}";
                            $errors[] = $errorMsg;
                            Log::error($errorMsg);
                            continue 2;
                        }
                    }

                    DB::beginTransaction();


                    foreach ($burialsData as $rowIndex => $burialRow) {
                        try {
                            // Пропускаем пустые строки
                            if (empty(array_filter($burialRow))) {
                                continue;
                            }

                            // Проверяем наличие города
                            $cityTitle = $burialRow[$columns['City']] ?? null;
                            if (!$cityTitle) {
                                $skippedRows++;
                                continue;
                            }
                            $cityTitle = self::cleanCityName($cityTitle);


                            // Ищем город в базе
                            $city = City::with('area.edge')
                                ->where('title', 'like', '%' . $cityTitle . '%')
                                ->first();

                            if (!$city) {
                                Log::warning("City not found: " . $cityTitle);
                                $skippedRows++;
                                continue;
                            }

                            if (!isset($request->id_cemetery) && $request->id_cemetery != null) {
                                $cemeteryTitle = $burialRow[$columns['Cemetery']] ?? null;
                                if (!$cemeteryTitle) {
                                    $skippedRows++;
                                    continue;
                                }
                            }


                            if (!isset($request->id_cemetery) || $request->id_cemetery == null) {
                                $cemetery = Cemetery::where('city_id', $city->id)
                                    ->where('title', $cemeteryTitle)
                                    ->first();
                            }

                            if (!$cemetery) {
                                Log::warning("Cemetery not found: " . $cemeteryTitle . " for city: " . $cityTitle);
                                $skippedRows++;
                                continue;
                            }

                            // Получение Edge и City для location_death
                            $edge = $city->area->edge;

                            $slug = self::generateOptimizedSlug(
                                $burialRow[$columns['LastName']] ?? '',
                                $burialRow[$columns['FirstName']] ?? '',
                                $burialRow[$columns['MiddleName']] ?? null,
                                $burialRow[$columns['BirthDate']] ?? '',
                                $burialRow[$columns['DeathDate']] ?? ''
                            );

                            $status = 1;
                            if (isset($columns['Status']) && $burialRow[$columns['Status']] != 'Готово') {
                                $status = 0;
                            }

                            $burialData = [
                                'surname'          => $burialRow[$columns['LastName']] ?? '',
                                'name'             => $burialRow[$columns['FirstName']] ?? '',
                                'patronymic'       => $burialRow[$columns['MiddleName']] ?? null,
                                'date_death'       => $burialRow[$columns['DeathDate']] ?? '',
                                'date_birth'       => $burialRow[$columns['BirthDate']] ?? '',
                                'status'           => $status,
                                'href_img'         => 1,
                                'who'              => 'Гражданский',
                                'img_url'          => $burialRow[$columns['PreparedImageURL']] ?? null,
                                'img_original_url' => $burialRow[$columns['OriginalImageURL']] ?? null,
                                'width'            => isset($burialRow[$columns['Latitude']]) ? str_replace(',', '.', $burialRow[$columns['Latitude']]) : null,
                                'longitude'        => isset($burialRow[$columns['Longitude']]) ? str_replace(',', '.', $burialRow[$columns['Longitude']]) : null,
                                'cemetery_id'      => $cemetery->id,
                                'slug'             => $slug,
                                'location_death'   => "Россия,{$edge->title},{$city->title}",
                                'created_at'       => now(),
                                'updated_at'       => now(),
                            ];

                            $burialId = DB::table('burials')->insertGetId($burialData);

                            $createdBurials++;

                            if ($createdBurials % 100 === 0) {
                                Log::info("Processed $createdBurials burials...");
                            }

                        } catch (\Exception $e) {
                            $skippedRows++;
                            Log::error("Ошибка при импорте строки " . ($rowIndex + 2) . ": " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
                        }
                    }

                    DB::commit();
                    Log::info("File processed successfully: " . $file->getClientOriginalName());

                } catch (\Exception $e) {
                    if (DB::transactionLevel() > 0) {
                        DB::rollBack();
                    }
                    $errorMsg = "Ошибка при обработке файла {$file->getClientOriginalName()}: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine();
                    $errors[] = $errorMsg;
                    Log::error($errorMsg);
                    continue;
                }
            }

            $message = "Импорт завершен. Файлов: " . count($files) .
                ", Захоронений: $createdBurials, Пропущено: $skippedRows";

            Log::info($message);

            return redirect()->back()
                ->with("message_cart", $message)
                ->withErrors($errors);

        } catch (\Exception $e) {
            Log::error("Critical error in import: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
            return redirect()->back()
                ->withErrors(["Критическая ошибка: " . $e->getMessage()]);
        }
    }

    public function getCountRowsInXlsx(string $path): int
    {
        $zip = new \ZipArchive();
        if ($zip->open($path) === true) {
            $content = $zip->getFromName('xl/worksheets/sheet1.xml');
            $zip->close();

            if (preg_match('/ref="[A-Z]+\d+:[A-Z]+(\d+)"/', $content, $matches)) {
                return (int)$matches[1] - 1;
            }
        }
        return 0;
    }

    /**
     * Максимально быстрый подсчет строк в CSV
     */
    private function getCountRowsInCsv(string $path): int
    {
        if (!file_exists($path)) return 0;

        $count = 0;

        // Если сервер на Linux/Unix
        if (function_exists('shell_exec') && strpos(strtolower(PHP_OS), 'win') === false) {
            $output = shell_exec("wc -l < " . escapeshellarg($path));
            $count = (int)$output;
        } else {
            // Фоллбек: потоковое чтение файла, чтобы не "съесть" оперативку
            $handle = fopen($path, "r");
            while (!feof($handle)) {
                fgets($handle);
                $count++;
            }
            fclose($handle);
        }

        return $count > 0 ? $count - 1 : 0;
    }

    public function importFromFilament(string $file, array $columnMapping, ?int $defaultCemeteryId, string $jobId): array
    {
        try {
            $createdBurials = 0;
            $skippedRows = 0;
            $errors = [];

            // Переменные для пакетной вставки
            $batch = [];
            $batchSlugs = [];

            $logStep = 10000; // Будем выводить отчет каждые 10к строк
            $totalStartTime = microtime(true);
            $chunkStartTime = microtime(true);

            $realPath = Storage::disk('public')->path($file);
            $fileName = basename($file);

            if (!file_exists($realPath)) {
                $error = "Файл {$fileName} не найден по пути {$realPath}";
                Log::error($error);
                return ['created' => 0, 'skipped' => 0, 'errors' => [$error]];
            }

            $totalRows = $this->getCountRowsInCsv($realPath);

            // Определяем размер пачки динамически
            $batchSize = $this->calculateBatchSize($totalRows);

            Redis::set("import_progress:{$jobId}:total", $totalRows);
            Redis::set("import_progress:{$jobId}:current", 0);
            Redis::set("import_progress:{$jobId}:status", 'В процессе');


            (new FastExcel)->import($realPath, function ($row) use (
                &$createdBurials,
                &$skippedRows,
                &$errors,
                $columnMapping,
                $defaultCemeteryId,
                $jobId,

                &$chunkStartTime,
                $totalStartTime,
                $logStep,

                &$batch,
                &$batchSlugs,
                $batchSize
            ) {
                try {
                    if (empty(array_filter($row))) {
                        Redis::incr("import_progress:{$jobId}:current");
                        return;
                    }

                    // Маппинг и доступ к полям
                    $getFieldValue = function ($sysKey) use ($columnMapping, $row) {
                        $fileColumn = $columnMapping[$sysKey] ?? null;
                        return $fileColumn && isset($row[$fileColumn])
                            ? trim((string)$row[$fileColumn])
                            : null;
                    };

                    $cityTitle = $getFieldValue('city');
                    if (!$cityTitle) {
                        $skippedRows++;
                        Redis::incr("import_progress:{$jobId}:current");
                        return;
                    }

                    $cleanedCityTitle = self::cleanCityName($cityTitle);

                    $city = $this->getCityWithCache($cleanedCityTitle);

                    if (!$city) {
                        Log::warning("Skipped row: city not found: {$cityTitle}");
                        $skippedRows++;
                        Redis::incr("import_progress:{$jobId}:current");
                        return;
                    }


                    $cemeteryId = $defaultCemeteryId;
                    if (!$cemeteryId) {
                        $cemeteryTitle = $getFieldValue('cemetery_column');
                        if (!$cemeteryTitle) {
                            $skippedRows++;
                            Redis::incr("import_progress:{$jobId}:current");
                            return;
                        }

                        $cemeteryData = [
                            'width'     => $getFieldValue('width'),
                            'longitude' => $getFieldValue('longitude'),
                        ];

                        $cemeteryId = $this->getCemeteryIdWithCache($city, $cemeteryTitle, $cemeteryData);
                    }

                    $edge = $city->area->edge;
                    $location_death = "Россия,{$edge->title},{$city->title}";

                    $surname = $getFieldValue('surname');
                    $name = $getFieldValue('name');
                    $patronymic = $getFieldValue('patronymic');
                    $date_birth = $getFieldValue('date_birth');
                    $date_death = $getFieldValue('date_death');

                    $rawWidth = $getFieldValue('width');
                    $rawLongitude = $getFieldValue('longitude');

                    $img_url = $getFieldValue('img_url');
                    $img_original_url = $getFieldValue('img_original_url');

                    $processedWidth = $rawWidth ? str_replace(',', '.', $rawWidth) : null;
                    $processedLongitude = $rawLongitude ? str_replace(',', '.', $rawLongitude) : null;

                    $slug = self::generateOptimizedSlug($surname, $name, $patronymic, $date_birth, $date_death, $batchSlugs);
                    $status = 1;

                    $batch[] = [
                        'surname'          => $surname,
                        'name'             => $name,
                        'patronymic'       => $patronymic,
                        'date_birth'       => $date_birth,
                        'date_death'       => $date_death,
                        'status'           => $status,
                        'href_img'         => 1,
                        'who'              => 'Гражданский',
                        'img_url'          => $img_url,
                        'img_original_url' => $img_original_url,
                        'width'            => $processedWidth,
                        'longitude'        => $processedLongitude,
                        'cemetery_id'      => $cemeteryId,
                        'slug'             => $slug,
                        'location_death'   => $location_death,
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ];

                    // Если пачка собрана — вставляем
                    if (count($batch) >= $batchSize) {
                        DB::table('burials')->insert($batch);
                        $createdBurials += count($batch);

                        Redis::incrby("import_progress:{$jobId}:current", count($batch));

                        $batch = [];
                        $batchSlugs = [];

                        // Логирование прогресса
                        $processed = $createdBurials + $skippedRows;
                        if ($processed > 0 && $processed % $logStep === 0) {
                            $now = microtime(true);
                            $chunkTime = round($now - $chunkStartTime, 2);
                            $avgSpeed = round($processed / ($now - $totalStartTime), 0);
                            echo "[PROGRESS] Processed: {$processed} | Batch: {$batchSize} | Last {$logStep} took: {$chunkTime}s | Avg Speed: {$avgSpeed} rows/sec\n";
                            $chunkStartTime = $now;
                        }
                    }
                } catch (\Exception $e) {
                    $skippedRows++;
                    Redis::incr("import_progress:{$jobId}:current");
                    Log::error("Ошибка при импорте строки: {$e->getMessage()} in {$e->getFile()}:{$e->getLine()}");
                }
            });

            // Вставка последнего остатка
            if (!empty($batch)) {
                DB::table('burials')->insert($batch);
                $createdBurials += count($batch);
                Redis::incrby("import_progress:{$jobId}:current", count($batch));
            }

            Redis::set("import_progress:{$jobId}:status", 'Выполнен');
            Redis::set("import_progress:{$jobId}:created", $createdBurials);
            Redis::set("import_progress:{$jobId}:skipped", $skippedRows);

            return [
                'created' => $createdBurials,
                'skipped' => $skippedRows,
                'errors'  => $errors,
            ];

        } catch (\Exception $e) {
            $error = "Критическая ошибка импорта: {$e->getMessage()} in {$e->getFile()}:{$e->getLine()}";
            Log::error($error);

            Redis::set("import_progress:{$jobId}:status", 'Ошибка');
            Redis::set("import_progress:{$jobId}:errors", json_encode([$error]));

            return [
                'created' => 0,
                'skipped' => 0,
                'errors'  => [$error],
            ];
        }
    }

    // Свойство для хранения кеша в пределах одного процесса импорта
    private array $localCityCache = [];

    /**
     * Получить город с использованием ленивого кеша.
     * @param string $cleanedCityTitle
     * @return Builder|Model|mixed|object|null
     */
    private function getCityWithCache(string $cleanedCityTitle): mixed
    {
        // Если мы уже искали этот город (даже если получили null), возвращаем результат из памяти
        if (array_key_exists($cleanedCityTitle, $this->localCityCache)) {
            return $this->localCityCache[$cleanedCityTitle];
        }

        // Если в памяти нет — идем в базу (только 1 раз для каждого названия)
        $city = City::with('area.edge')
            ->where('title', 'like', '%' . $cleanedCityTitle . '%')
            ->first();

        // Сохраняем результат в кеш
        $this->localCityCache[$cleanedCityTitle] = $city;

        return $city;
    }

    private array $localCemeteryCache = [];

    /**
     * Получить ID кладбища (найти или создать) с использованием кеша.
     * @param City $city
     * @param string|null $cemeteryTitle
     * @param array $cemeteryData
     * @return int|null
     */
    private function getCemeteryIdWithCache(City $city, ?string $cemeteryTitle, array $cemeteryData): ?int
    {
        if (!$cemeteryTitle) return null;

        // Уникальный ключ для кеша: ID города + название кладбища
        $cacheKey = "{$city->id}_" . mb_strtolower($cemeteryTitle);

        if (array_key_exists($cacheKey, $this->localCemeteryCache)) {
            return $this->localCemeteryCache[$cacheKey];
        }

        // 1. Пытаемся найти существующее
        $cemetery = Cemetery::where('city_id', $city->id)
            ->where('title', $cemeteryTitle)
            ->first();

        // 2. Если не нашли — создаем (Eloquent создаст модель и вернет объект с ID)
        if (!$cemetery) {
            $cemetery = Cemetery::create([
                'title'     => $cemeteryTitle,
                'slug'      => slug($cemeteryTitle),
                'img_url'   => 'default',
                'width'     => $cemeteryData['width'],
                'longitude' => $cemeteryData['longitude'],
                'rating'    => 5,
                'href_img'  => 1,
                'city_id'   => $city->id,
                'area_id'   => $city->area_id
            ]);
        }

        $this->localCemeteryCache[$cacheKey] = $cemetery->id;

        return $cemetery->id;
    }

    /**
     * Динамический расчет размера пачки
     */
    private function calculateBatchSize(int $totalRows): int
    {
        if ($totalRows <= 1000) return 100;    // Для маленьких файлов
        if ($totalRows <= 10000) return 500;   // Для средних
        if ($totalRows <= 100000) return 1000; // Для крупных

        return 2000; // Потолок, чтобы не упереться в лимиты MySQL параметров
    }

    protected static function generateOptimizedSlug($surname, $name, $patronymic, $birthDate, $deathDate, $batchSlugs)
    {
        $parts = array_filter([$surname, $name, $patronymic, $birthDate, $deathDate]);
        $baseSlug = Str::slug(implode(' ', $parts));

        $baseSlug = preg_replace('/-{2,}/', '-', $baseSlug);
        $baseSlug = trim($baseSlug, '-');

        $finalSlug = $baseSlug;
        $counter = 1;

        // Вместо проверки в гигантском массиве, спрашиваем у базы: "Есть ли такой слаг?"
        while (isset($batchSlugs[$finalSlug]) || DB::table('burials')->where('slug', $finalSlug)->exists()) {
            $finalSlug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $finalSlug;
    }

    protected static function cleanCityName(string $name): string
    {
        $replacements = [
            '/^г\.\s*/iu'     => '',
            '/^пос\.\s*/iu'   => '',
            '/^дер\.\s*/iu'   => '',
            '/^село\s*/iu'    => '',
            '/^деревня\s*/iu' => '',
            '/^пгт\.\s*/iu'   => '',
            '/^рп\.\s*/iu'    => '',
        ];

        return trim(preg_replace(array_keys($replacements), array_values($replacements), $name));
    }

    /**
     * Извлекает заголовки из загруженного файла.
     *
     * @param TemporaryUploadedFile $file
     * @return array
     * @throws Exception
     */
    public function getFileHeaders(TemporaryUploadedFile $file): array
    {
        $reader = ReaderFactory::createFromFile($file->getRealPath());
        $reader->open($file->getRealPath());

        $headers = [];
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $headers = $row->toArray();
                break 2;
            }
        }
        $reader->close();

        return array_filter($headers, fn($h) => !empty(trim((string)$h)));
    }
}
