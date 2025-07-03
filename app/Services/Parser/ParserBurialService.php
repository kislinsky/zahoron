<?php

namespace App\Services\Parser;

use App\Models\Burial;
use App\Models\Cemetery;
use App\Models\City;
use App\Models\ImagePersonal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ParserBurialService
{
    private static $existingSlugs = [];

    public static function index($request)
    {
        set_time_limit(3600);
        ignore_user_abort(true);

        $validated = $request->validate([
            'files' => 'required|array',
            'files.*' => 'file|mimes:xlsx,xls',
        ]);

        // Предзагрузка существующих slug для проверки уникальности
        self::$existingSlugs = Burial::pluck('slug')->toArray();

        $files = $request->file('files');
        $createdBurials = 0;
        $skippedRows = 0;
        $errors = [];

        // Предзагрузка всех необходимых данных
        $cities = City::with('area.edge')->get()->keyBy('title');
        $cemeteries = Cemetery::all()->groupBy('city_id');

        foreach ($files as $file) {
            if (!$file->isValid()) {
                $errors[] = "Файл {$file->getClientOriginalName()} не валиден";
                continue;
            }

            try {
                $spreadsheet = IOFactory::load($file->getRealPath());
                $sheet = $spreadsheet->getActiveSheet();
                
                $titles = $sheet->toArray()[0];
                $burialsData = array_slice($sheet->toArray(), 1);
                
                $filteredTitles = array_filter($titles, fn($value) => $value !== null);
                $columns = array_flip($filteredTitles);

                $requiredColumns = [
                    'Населённый пункт', 'Кладбище', 'Фамилия', 'Имя',
                    'Дата рождения', 'Дата смерти', 'Широта', 'Долгота'
                ];

                foreach ($requiredColumns as $col) {
                    if (!isset($columns[$col])) {
                        $errors[] = "В файле {$file->getClientOriginalName()} отсутствует обязательная колонка: {$col}";
                        continue 2;
                    }
                }

                DB::beginTransaction();

                foreach ($burialsData as $rowIndex => $burialRow) {
                    try {
                        $city = $cities[$burialRow[$columns['Населённый пункт']]] ?? null;
                        if (!$city) {
                            $skippedRows++;
                            continue;
                        }

                        $cemetery = self::findCemeteryInPreloaded(
                            $burialRow[$columns['Кладбище']],
                            $city->id,
                            $cemeteries[$city->id] ?? collect()
                        );

                        if (!$cemetery) {
                            $skippedRows++;
                            continue;
                        }

                        $slug = self::generateOptimizedSlug(
                            $burialRow[$columns['Фамилия']],
                            $burialRow[$columns['Имя']],
                            $burialRow[$columns['Отчество']] ?? null,
                            $burialRow[$columns['Дата рождения']],
                            $burialRow[$columns['Дата смерти']]
                        );

                        $burialData = [
                            'surname' => $burialRow[$columns['Фамилия']],
                            'name' => $burialRow[$columns['Имя']],
                            'patronymic' => $burialRow[$columns['Отчество']] ?? null,
                            'date_death' => $burialRow[$columns['Дата смерти']],
                            'date_birth' => $burialRow[$columns['Дата рождения']],
                            'status' => 1,
                            'href_img' => 1,
                            'url' => $burialRow[$columns['URL']] ?? null,
                            'who' => 'Гражданский',
                            'img_url' => $burialRow[$columns['Главное фото']] ?? null,
                            'width' => str_replace(',', '.', $burialRow[$columns['Широта']]),
                            'longitude' => str_replace(',', '.', $burialRow[$columns['Долгота']]),
                            'cemetery_id' => $cemetery->id,
                            'slug' => $slug,
                            'location_death' => "Россия,{$city->area->edge->title},{$city->title}",
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                        
                        $burialId = DB::table('burials')->insertGetId($burialData);
                        self::$existingSlugs[] = $slug; // Добавляем новый slug в кеш
                        
                        if (isset($columns['Фотографии']) && !empty($burialRow[$columns['Фотографии']])) {
                            $photos = array_filter(array_map('trim', explode(',', $burialRow[$columns['Фотографии']])));
                            
                            $imagesData = array_map(function($photo) use ($burialId) {
                                return [
                                    'title' => $photo,
                                    'burial_id' => $burialId,
                                    'status' => 1,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ];
                            }, $photos);
                            
                            DB::table('image_personals')->insert($imagesData);
                        }

                        $createdBurials++;

                    } catch (\Exception $e) {
                        $skippedRows++;
                        Log::error("Ошибка при импорте строки " . ($rowIndex + 2) . ": " . $e->getMessage());
                    }
                }

                DB::commit();

            } catch (\Exception $e) {
                DB::rollBack();
                $errors[] = "Ошибка при обработке файла {$file->getClientOriginalName()}: " . $e->getMessage();
                continue;
            }
        }

        $message = "Импорт завершен. Файлов: " . count($files) . 
                  ", Захоронений: $createdBurials, Пропущено: $skippedRows";

        return redirect()->back()
            ->with("message_cart", $message)
            ->withErrors($errors);
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

    protected static function findCemeteryInPreloaded($name, $cityId, $cityCemeteries)
    {
        $cleanedName = self::cleanCemeteryName($name);
        
        foreach ($cityCemeteries as $cemetery) {
            if ($cemetery->title === $name || $cemetery->title === $cleanedName) {
                return $cemetery;
            }
        }
        
        foreach ($cityCemeteries as $cemetery) {
            if (stripos($cemetery->title, $cleanedName) !== false || 
                stripos($cleanedName, $cemetery->title) !== false) {
                return $cemetery;
            }
        }
        
        return null;
    }

    protected static function cleanCemeteryName(string $name): string
    {
        static $replacements = [
            '/^Кладбище\s+/iu' => '',
            '/г\.\s*/iu' => '',
            '/пос\.\s*/iu' => '',
            '/дер\.\s*/iu' => '',
            '/село\s*/iu' => '',
            '/деревня\s*/iu' => '',
            '/\(.*\)/' => '',
        ];

        return trim(preg_replace(array_keys($replacements), array_values($replacements), $name));
    }
}