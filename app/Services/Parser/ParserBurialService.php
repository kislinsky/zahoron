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
        try {
            set_time_limit(3600);
            ignore_user_abort(true);

            Log::info('Import started');

            // Валидация
            $validated = $request->validate([
                'files' => 'required|array',
                'files.*' => 'file|mimes:xlsx,xls',
                'id_cemetery'=>'nullable|integer',
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
                        'City',  'LastName', 'FirstName',
                        'BirthDate', 'DeathDate', 'Latitude', 'Longitude'
                    ];

                    if(!isset($request->id_cemetery) && $request->id_cemetery!=null){
                        $requiredColumns[]='Cemetery';
                    }else{
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
                                ->where('title', 'like','%'.$cityTitle.'%')
                                ->first();

                            if (!$city) {
                                Log::warning("City not found: " . $cityTitle);
                                $skippedRows++;
                                continue;
                            }

                            if(!isset($request->id_cemetery) && $request->id_cemetery!=null){
                                $cemeteryTitle = $burialRow[$columns['Cemetery']] ?? null;
                                if (!$cemeteryTitle) {
                                    $skippedRows++;
                                    continue;
                                }
                            }


                            if(!isset($request->id_cemetery) || $request->id_cemetery==null){
                                $cemetery = Cemetery::where('city_id', $city->id)
                                ->where('title', $cemeteryTitle)
                                ->first();
                            }

                            if (!$cemetery) {
                                Log::warning("Cemetery not found: " . $cemeteryTitle . " for city: " . $cityTitle);
                                $skippedRows++;
                                continue;
                            }

                            $slug = self::generateOptimizedSlug(
                                $burialRow[$columns['LastName']] ?? '',
                                $burialRow[$columns['FirstName']] ?? '',
                                $burialRow[$columns['MiddleName']] ?? null,
                                $burialRow[$columns['BirthDate']] ?? '',
                                $burialRow[$columns['DeathDate']] ?? ''
                            );

                            $status=1;
                            if($burialRow[$columns['Status']] !='Готово'){
                                $status=0;
                            }

                            $burialData = [
                                'surname' => $burialRow[$columns['LastName']] ?? '',
                                'name' => $burialRow[$columns['FirstName']] ?? '',
                                'patronymic' => $burialRow[$columns['MiddleName']] ?? null,
                                'date_death' => $burialRow[$columns['DeathDate']] ?? '',
                                'date_birth' => $burialRow[$columns['BirthDate']] ?? '',
                                'status' => $status,
                                'href_img' => 1,
                                'who' => 'Гражданский',
                                'img_url' => $burialRow[$columns['PreparedImageURL']] ?? null,
                                'img_original_url' => $burialRow[$columns['OriginalImageURL']] ?? null,
                                'width' => isset($burialRow[$columns['Latitude']]) ? str_replace(',', '.', $burialRow[$columns['Latitude']]) : null,
                                'longitude' => isset($burialRow[$columns['Longitude']]) ? str_replace(',', '.', $burialRow[$columns['Longitude']]) : null,
                                'cemetery_id' => $cemetery->id,
                                'slug' => $slug,
                                'location_death' => "Россия,{$city->area->edge->title},{$city->title}",
                                'created_at' => now(),
                                'updated_at' => now(),
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
        '/^г\.\s*/iu' => '',
        '/^пос\.\s*/iu' => '',
        '/^дер\.\s*/iu' => '',
        '/^село\s*/iu' => '',
        '/^деревня\s*/iu' => '',
        '/^пгт\.\s*/iu' => '',
        '/^рп\.\s*/iu' => '',
    ];

    return trim(preg_replace(array_keys($replacements), array_values($replacements), $name));
}
}