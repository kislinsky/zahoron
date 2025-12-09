<?php

namespace App\Services\Parser;

use App\Models\Burial;
use App\Models\Cemetery;
use App\Models\City;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Rap2hpoutre\FastExcel\FastExcel;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Facades\Redis;

class ParserBurialService
{
    private static $existingSlugs = [];

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

            // Предзагрузка существующих slug для проверки уникальности
            self::$existingSlugs = Burial::pluck('slug')->toArray();

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
                            self::$existingSlugs[] = $slug;

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


    public function importFromFilament(string $file, array $columnMapping, ?int $defaultCemeteryId, string $jobId): array
    {
        try {
            self::$existingSlugs = Burial::pluck('slug')->toArray();

            $createdBurials = 0;
            $skippedRows = 0;
            $errors = [];

            $realPath = Storage::disk('public')->path($file);
            $fileName = basename($file);

            if (!file_exists($realPath)) {
                $error = "Файл {$fileName} не найден по пути {$realPath}";
                Log::error($error);
                return ['created' => 0, 'skipped' => 0, 'errors' => [$error]];
            }

            $totalRows = (new FastExcel)->import($realPath)->count();

            Redis::set("import_progress:{$jobId}:total", $totalRows);
            Redis::set("import_progress:{$jobId}:current", 0);
            Redis::set("import_progress:{$jobId}:status", 'В процессе');

            DB::beginTransaction();

            (new FastExcel)->import($realPath, function ($row) use (
                &$createdBurials,
                &$skippedRows,
                &$errors,
                $columnMapping,
                $defaultCemeteryId,
                $jobId
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

                    $city = City::with('area.edge')
                        ->where('title', 'like', '%' . $cleanedCityTitle . '%')
                        ->first();

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

                        $cemetery = Cemetery::where('city_id', $city->id)
                            ->where('title', $cemeteryTitle)
                            ->first();

                        if (!$cemetery) {
                            Log::warning("Skipped row: cemetery '{$cemeteryTitle}' not found for city '{$city->title}'");
                            $skippedRows++;
                            Redis::incr("import_progress:{$jobId}:current");
                            return;
                        }

                        $cemeteryId = $cemetery->id;
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

                    $slug = self::generateOptimizedSlug($surname, $name, $patronymic, $date_birth, $date_death);
                    $status = 1;

                    $burialData = [
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

                    DB::table('burials')->insert($burialData);
                    self::$existingSlugs[] = $slug;

                    $createdBurials++;

                    if ($createdBurials % 100 === 0) {
                        Log::info("Processed {$createdBurials} burials...");
                    }

                    Redis::incr("import_progress:{$jobId}:current");
                } catch (\Exception $e) {
                    $skippedRows++;
                    Redis::incr("import_progress:{$jobId}:current");
                    Log::error("Ошибка при импорте строки: {$e->getMessage()} in {$e->getFile()}:{$e->getLine()}");
                }
            });

            DB::commit();

            Redis::set("import_progress:{$jobId}:status", 'Выполнен');
            Redis::set("import_progress:{$jobId}:created", $createdBurials);
            Redis::set("import_progress:{$jobId}:skipped", $skippedRows);

            return [
                'created' => $createdBurials,
                'skipped' => $skippedRows,
                'errors'  => $errors,
            ];

        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

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


    protected static function generateOptimizedSlug($surname, $name, $patronymic, $birthDate, $deathDate)
    {
        $parts = array_filter([$surname, $name, $patronymic, $birthDate, $deathDate]);
        $baseSlug = Str::slug(implode(' ', $parts));

        // Очистка slug от возможных артефактов
        $baseSlug = preg_replace('/-{2,}/', '-', $baseSlug);
        $baseSlug = trim($baseSlug, '-');

        // Если slug уже существует, добавляем суффикс
        if (in_array($baseSlug, self::$existingSlugs)) {
            $counter = 1;
            do {
                $newSlug = $baseSlug . '-' . $counter;
                $counter++;
            } while (in_array($newSlug, self::$existingSlugs));

            return $newSlug;
        }

        return $baseSlug;
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
        $fastExcel = new FastExcel();

        try {
            $collection = $fastExcel
                ->withoutHeaders()
                ->import($file->getRealPath());

            if ($collection->isEmpty()) {
                throw new Exception('Файл не содержит данных.');
            }

            $headers = $collection->first();

            if (!is_array($headers)) {
                throw new Exception('Некорректный формат данных в первой строке.');
            }

            return array_filter($headers, function ($header) {
                return !empty(trim((string)$header));
            });

        } catch (Exception $e) {
            throw new Exception("Не удалось прочитать заголовки из файла: " . $e->getMessage());
        }
    }
}
