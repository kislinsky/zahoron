<?php

namespace App\Services\Parser;

use App\Models\Cemetery;
use App\Models\City;

use App\Models\Edge;
use App\Models\ImageCemetery;
use App\Models\PriceService;
use App\Models\ReviewCemetery;
use App\Models\Service;
use App\Models\WorkingHoursCemetery;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ParserCemeteryService
{

    public static function index($request) {
        // Валидация входных данных
        $validated = $request->validate([
            'files' => 'required|array',
            'files.*' => 'file|mimes:xlsx,xls',
            'price_geo' => 'nullable|numeric',
            'import_type' => 'required|in:create,update',
            'columns_to_update' => 'nullable|array',
        ]);
        $files = $request->file('files');
        $price = $request->input('price_geo');
        $importAction = $request->input('import_type', 'create');
        $updateFields = $request->input('columns_to_update', []);
        
        $processedFiles = 0;
        $createdCemeteries = 0;
        $updatedCemeteries = 0;
        $skippedRows = 0;
        $errors = [];
    
        foreach ($files as $file) {
            if (!$file->isValid()) {
                $errors[] = "Файл {$file->getClientOriginalName()} не валиден";
                continue;
            }
    
            try {
                $spreadsheet = IOFactory::load($file->getRealPath());
                $sheet = $spreadsheet->getActiveSheet();
                $titles = $sheet->toArray()[0];
                $cemeteriesData = array_slice($sheet->toArray(), 1);
                $filteredTitles = array_filter($titles, fn($value) => $value !== null);
                $columns = array_flip($filteredTitles);

                // Проверка наличия обязательных колонок
                $requiredColumns = ['ID 2GIS', 'Название кладбища', ];
                foreach ($requiredColumns as $col) {
                    if (!isset($columns[$col])) {
                        $errors[] = "В файле {$file->getClientOriginalName()} отсутствует обязательная колонка: {$col}";
                        continue 2;
                    }
                }

                foreach ($cemeteriesData as $rowIndex => $cemeteryRow) {
                    try {
                        $objectId = transformId(isset($columns['ID 2GIS']) ? (int)rtrim($cemeteryRow[$columns['ID 2GIS']] ?? '', '!') : null);
                        
                        // Проверка обязательных полей
                        if ($objectId == null) {
                            $skippedRows++;
                            continue;
                        }

                        // Проверка обязательных полей
                        if (empty($cemeteryRow[$columns['Название кладбища']])) {
                            $skippedRows++;
                            continue;
                        }
                             
                        dd($request);


                        // // Проверка координат
                        // if (empty($cemeteryRow[$columns['Широта']])) {
                        //     $skippedRows++;
                        //     continue;
                        // }
    
                        // if (empty($cemeteryRow[$columns['Долгота']])) {
                        //     $skippedRows++;
                        //     continue;
                        // }

                        $objects = linkRegionDistrictCity(
                            $cemeteryRow[$columns['Регион'] ?? null],
                            $cemeteryRow[$columns['Район'] ?? null],
                            $cemeteryRow[$columns['Населённый пункт'] ?? null]
                        );

                        $area = $objects['district'] ?? null;
                        $city = $objects['city'] ?? null;

                        if (!$city || !$area) {
                            $skippedRows++;
                            continue;
                        }

                        $status = 1;
                        if ($cemeteryRow[$columns['Статус']] == null || $cemeteryRow[$columns['Статус']] != 'Действующая организация') {
                            $status = 0;
                        }                        

                        $time_difference = $city->utc_offset ?? null;
                        if ($time_difference == null && env('API_WORK') == 'true') {
                            $timeData = getTimeByCoordinates($cemeteryRow[$columns['Широта']], $cemeteryRow[$columns['Долгота']]);
                            $time_difference = differencetHoursTimezone($timeData['timezone'] ?? 'UTC');
                            $city->update(['utc_offset' => $time_difference]);
                        }
                        if ($time_difference == null) {
                            $time_difference = 0;
                        }   
                        
                        // Полный набор данных для кладбища
                        $cemeteryData = [
                            'id' => $objectId,
                            'title' => $cemeteryRow[$columns['Название кладбища']],
                            'slug' => Str::slug($cemeteryRow[$columns['Название кладбища']]),
                            'adres' => $cemeteryRow[$columns['Адрес кладбища'] ?? null],
                            'responsible_person_address' => $cemeteryRow[$columns['Адрес (ответственного лица)'] ?? null],
                            'responsible_organization' => $cemeteryRow[$columns['Ответственная организация'] ?? null],
                            'okved' => $cemeteryRow[$columns['Okved'] ?? null],
                            'inn' => (int)($cemeteryRow[$columns['ИНН'] ?? null] ?? 0),
                            'city_id' => $city->id,
                            'area_id' => $area->id,
                            'width' => $cemeteryRow[$columns['Широта']],
                            'longitude' => $cemeteryRow[$columns['Долгота']],
                            'rating' => (float)str_replace(',', '.', $cemeteryRow[$columns['Рейтинг'] ?? 0]),
                            'phone' => normalizePhone($cemeteryRow[$columns['Телефон'] ?? null]),
                            'email' => $cemeteryRow[$columns['Емейл'] ?? null],
                            'img_url' => 'default',
                            'href_img' => 1,
                            'time_difference' => $time_difference,
                            'responsible' => $cemeteryRow[$columns['Ответственное лицо (ФИО)'] ?? null],
                            'cadastral_number' => $cemeteryRow[$columns['Кадастровый номер'] ?? null],
                            'price_burial_location' => $price ?? 5900,
                            'two_gis_link' => $cemeteryRow[$columns['URL'] ?? null],
                            'status' => $status,
                            'date_foundation' => $cemeteryRow[$columns['Дата парсинга'] ?? null],
                            'address_responsible_person' => $cemeteryRow[$columns['Адрес (ответственного лица)'] ?? null],
                            'responsible_person_full_name' => $cemeteryRow[$columns['Ответственное лицо (ФИО)'] ?? null],
                        ];

                        if ($importAction === 'create') {
                            // Проверяем существование по ID или two_gis_link
                            $existing = Cemetery::where('id', $objectId)
                                                ->orWhere('two_gis_link', $objectId)
                                                ->first();
                            if ($existing) {
                                $skippedRows++;
                                continue;
                            }    

                            $cemetery = Cemetery::create($cemeteryData);
                            $createdCemeteries++;

                            // Обработка режима работы при создании
                            if (isset($columns['Режим работы']) && $cemeteryRow[$columns['Режим работы']] != null) {
                                $workHours = $cemeteryRow[$columns['Режим работы']];
                                $days = parseWorkingHours($workHours);
                                
                                foreach ($days as $day) {
                                    $holiday = ($day['time_start_work'] == 'Выходной') ? 1 : 0;
                                    WorkingHoursCemetery::create([
                                        'day' => $day['day'],
                                        'time_start_work' => $day['time_start_work'],
                                        'time_end_work' => $day['time_end_work'],
                                        'holiday' => $holiday,
                                        'cemetery_id' => $cemetery->id,
                                    ]);
                                }
                            }

                            // Обработка фотографий при создании
                            if (isset($columns['Фотографии']) && !empty($cemeteryRow[$columns['Фотографии']])) {
                                $urls_array = explode(', ', $cemeteryRow[$columns['Фотографии']]);
                                foreach ($urls_array as $img) {
                                    if ($img != null) {
                                        ImageCemetery::create([
                                            'img_url' => $img,
                                            'href_img' => 1,
                                            'cemetery_id' => $cemetery->id,
                                        ]);
                                    }
                                }
                            }

                        } elseif ($importAction === 'update') {



                            // Ищем кладбище по two_gis_link или ID
                            $cemetery = Cemetery::where('two_gis_link', $objectId)
                                                ->orWhere('id', $objectId)
                                                ->first();

                            if ($cemetery) {
                                $updateData = [];
                                foreach ($updateFields as $field) {
                                    if (isset($cemeteryData[$field])) {
                                        $updateData[$field] = $cemeteryData[$field];
                                    }
                                }

                                // Обновляем slug если обновляется title
                                if (isset($updateData['title']) && !isset($updateData['slug'])) {
                                    $updateData['slug'] = Str::slug($updateData['title']);
                                }

                                if (!empty($updateData)) {
                                    $cemetery->update($updateData);
                                    $updatedCemeteries++;
                                }

                                // Обработка режима работы при обновлении
                                if (in_array('working_hours', $updateFields) && isset($columns['Режим работы'])) {
                                    $workHours = $cemeteryRow[$columns['Режим работы']] ?? null;
                                    if ($workHours) {
                                        // Удаляем старые записи о рабочем времени
                                        WorkingHoursCemetery::where('cemetery_id', $cemetery->id)->delete();
                                        
                                        // Создаем новые записи
                                        $days = parseWorkingHours($workHours);
                                        foreach ($days as $day) {
                                            $holiday = ($day['time_start_work'] == 'Выходной') ? 1 : 0;
                                            WorkingHoursCemetery::create([
                                                'day' => $day['day'],
                                                'time_start_work' => $day['time_start_work'],
                                                'time_end_work' => $day['time_end_work'],
                                                'holiday' => $holiday,
                                                'cemetery_id' => $cemetery->id,
                                            ]);
                                        }
                                    }
                                }

                                // Обработка фотографий при обновлении
                                if (in_array('galerey', $updateFields) && isset($columns['Фотографии']) && !empty($cemeteryRow[$columns['Фотографии']])) {
                                    ImageCemetery::where('cemetery_id', $cemetery->id)->delete();
                                    
                                    $urls_array = explode(', ', $cemeteryRow[$columns['Фотографии']]);
                                    foreach ($urls_array as $img) {
                                        if ($img != null) {
                                            ImageCemetery::create([
                                                'img_url' => $img,
                                                'href_img' => 1,
                                                'cemetery_id' => $cemetery->id,
                                            ]);
                                        }
                                    }
                                }
                            } else {
                                $skippedRows++;
                            }
                        }
                    } catch (\Exception $e) {
                        $skippedRows++;
                        $errors[] = "Ошибка в строке " . ($rowIndex + 2) . ": " . $e->getMessage();
                    }
                }
                
                $processedFiles++;
            } catch (\Exception $e) {
                $errors[] = "Ошибка при обработке файла {$file->getClientOriginalName()}: " . $e->getMessage();
            }
        }
    
        $message = "Импорт кладбищ завершен. " .
                   "Файлов обработано: $processedFiles, " .
                   "Создано кладбищ: $createdCemeteries, " .
                   "Обновлено кладбищ: $updatedCemeteries, " .
                   "Пропущено строк: $skippedRows";
    
        return redirect()->back()
            ->with("message_cart", $message)
            ->withErrors($errors);
    }


    

    public static function importReviews($request)
{
    $file = $request->file('file_reviews');
    $spreadsheet = IOFactory::load($file);
    $sheet = $spreadsheet->getActiveSheet();
    
    $headers = $sheet->rangeToArray('A1:' . $sheet->getHighestColumn() . '1')[0];
    $headers = array_map('strtolower', $headers);
    
    $columnIndexes = [
        'cemetery_id' => array_search('id', $headers),
        'name' => array_search('Имя', $headers),
        'date' => array_search('Дата', $headers),
        'rating' => array_search('Оценка', $headers),
        'content' => array_search('Отзыв', $headers),
    ];

    foreach ($columnIndexes as $key => $index) {
        if ($index === false) {
            return redirect()->back()->with("error_cart", "Отсутствует обязательная колонка: " . $key);
        }
    }

    $reviews = array_slice($sheet->toArray(), 1);
    $addedReviews = 0;
    $skippedReviews = 0;
    $errors = [];

    foreach ($reviews as $rowIndex => $review) {
        $rowNumber = $rowIndex + 2;
        
        try {
            if (empty(array_filter($review))) {
                $skippedReviews++;
                continue;
            }
            
            $cemeteryId =rtrim($review[$columnIndexes['cemetery_id']] ?? '', '!');
            $reviewerName = $review[$columnIndexes['name']] ?? null;
            $reviewDate = $review[$columnIndexes['date']] ?? null;
            $rating = $review[$columnIndexes['rating']] ?? null;
            $content = $review[$columnIndexes['content']] ?? null;
            
            if (empty($cemeteryId)) {
                $errors[] = "Строка {$rowNumber}: Не указан ID кладбища";
                $skippedReviews++;
                continue;
            }

            $cemetery = Cemetery::find(transformID($cemeteryId));
            if (!$cemetery) {
                $errors[] = "Строка {$rowNumber}: Кладбище с ID {$cemeteryId} не найдено";
                $skippedReviews++;
                continue;
            }
            
            if (!$cemetery->city) {
                $errors[] = "Строка {$rowNumber}: У кладбища не указан город";
                $skippedReviews++;
                continue;
            }
            
            if (!is_numeric($rating) || $rating < 1 || $rating > 5) {
                $errors[] = "Строка {$rowNumber}: Рейтинг должен быть числом от 1 до 5";
                $skippedReviews++;
                continue;
            }
            
            if (!empty($reviewDate)) {
                $reviewDate = trim(preg_replace('/отредактирован/ui', '', $reviewDate));
                
                $russianMonths = [
                    'января' => '01', 'февраля' => '02', 'марта' => '03',
                    'апреля' => '04', 'мая' => '05', 'июня' => '06',
                    'июля' => '07', 'августа' => '08', 'сентября' => '09',
                    'октября' => '10', 'ноября' => '11', 'декабря' => '12'
                ];
                
                if (preg_match('/^(\d{1,2})\s+([а-яё]+)\s+(\d{4})$/ui', $reviewDate, $matches)) {
                    $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                    $month = strtolower($matches[2]);
                    $year = $matches[3];
                    
                    if (isset($russianMonths[$month])) {
                        $reviewDate = "{$year}-{$russianMonths[$month]}-{$day}";
                    } else {
                        $errors[] = "Строка {$rowNumber}: Неизвестный месяц '{$matches[2]}' в дате '{$reviewDate}'";
                        $skippedReviews++;
                        continue;
                    }
                } 
                elseif (($timestamp = strtotime($reviewDate)) !== false) {
                    $reviewDate = date('Y-m-d', $timestamp);
                } else {
                    $errors[] = "Строка {$rowNumber}: Не удалось распознать дату '{$reviewDate}'";
                    $skippedReviews++;
                    continue;
                }
            } else {
                $reviewDate = now()->format('Y-m-d');
            }
            
            ReviewCemetery::create([
                'name' => $reviewerName,
                'rating' => $rating,
                'content' => $content,
                'created_at' => !empty($reviewDate) ? $reviewDate : now(),
                'cemetery_id' => $cemetery->id,
                'status' => 1,
                'city_id' => $cemetery->city->id,
            ]);
            
            $addedReviews++;
            
        } catch (\Exception $e) {
            $errors[] = "Строка {$rowNumber}: Ошибка обработки - " . $e->getMessage();
            $skippedReviews++;
            continue;
        }
    }
    
    $message = "Импорт отзывов для кладбищ завершен. Добавлено: {$addedReviews}, Пропущено: {$skippedReviews}";
    
    if (!empty($errors)) {
        $message .= "<br><br>Ошибки:<br>" . implode("<br>", array_slice($errors, 0, 10));
        if (count($errors) > 10) {
            $message .= "<br>... и ещё " . (count($errors) - 10) . " ошибок";
        }
    }
    
    return redirect()->back()->with("message_cart", $message);
}

}