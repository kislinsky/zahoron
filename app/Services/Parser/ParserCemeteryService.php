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
                $requiredColumns = ['ID','Название организации', 'Latitude', 'Longitude'];
                foreach ($requiredColumns as $col) {
                    if (!isset($columns[$col])) {
                        continue 2;
                    }
                }
    
                foreach ($cemeteriesData as $rowIndex => $cemeteryRow) {
                    
                    try {
                        
                        
                       
                        $objectId = isset($columns['ID']) ? rtrim($cemeteryRow[$columns['ID']] ?? '', '!') : null;
                        

                        // Проверка обязательных полей
                        if ($objectId==null) {
                            $skippedRows++;
                            continue;
                        }

                        // Проверка обязательных полей
                        if (empty($cemeteryRow[$columns['Название организации']])) {
                            $skippedRows++;
                            continue;
                        }
                        
                        $cadastralNumber=null;
                       

                        // Проверка координат
                        if (empty($cemeteryRow[$columns['Latitude']])) {
                            $skippedRows++;
                            continue;
                        }
    
                        if (empty($cemeteryRow[$columns['Longitude']])) {
                            $skippedRows++;
                            continue;
                        }

                        
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
                        

                        $time_difference = $city->utc_offset ?? null;
                        if($time_difference==null && env('API_WORK')=='true'){
                            $time_difference=differencetHoursTimezone(getTimeByCoordinates($cemeteryRow[$columns['Latitude']],$cemeteryRow[$columns['Longitude']])['timezone']);
                            $city->update(['utc_offset'=> $time_difference]);
                            
                        }
                        if($time_difference==null){
                            $time_difference=0;
                        }   
                        

                        // $cemeteryData = [
                        //     'id'=>$id,
                        //     'title' => $cemeteryRow[$columns['Название организации']],
                        //     'adres' => $cemeteryRow[$columns['Адрес'] ?? null],
                        //     'content'=>$cemeteryRow[$columns['SEO Описание']]  ?? $cemeteryRow[$columns['Описание']] ?? null,
                        //     'rating'=>$cemeteryRow[$columns['Рейтинг']],
                        //     'width' => $cemeteryRow[$columns['Latitude']],
                        //     'longitude' => $cemeteryRow[$columns['Longitude']],
                        //     'city_id' => $city->id,
                        //     'two_gis_link'=> $crematoriumRow[$columns['URL']]  ?? null,
                        //     'area_id' => $area->id,
                        //     'phone' => normalizePhone($cemeteryRow[$columns['Тел. Ответственного'] ?? null]),
                        //     'square' => $cemeteryRow[$columns['Общая площадь (га)'] ?? null],
                        //     'responsible' => $cemeteryRow[$columns['Ответственный'] ?? null],
                        //     'cadastral_number' => $cadastralNumber, // Сохраняем оригинальный кадастровый номер
                        //     'status' => $status,
                        //     'img_url' => 'default',
                        //     'href_img' => 1,
                        //     'count_burials'=>$cemeteryRow[$columns['Захоронения'] ?? null],
                        //     'inn'=>$cemeteryRow[$columns['ИНН'] ?? null],
                        //     'price_burial_location' => $price ?? 0,
                        //     'time_difference' => $time_difference,
                        // ];
                        
                        
                        
                        $cemeteryData = [
                            'id'=>$objectId,
                            'title' => $cemeteryRow[$columns['Название организации']],
                            'width' => $cemeteryRow[$columns['Latitude']],
                            'longitude' => $cemeteryRow[$columns['Longitude']],
                            'city_id' => $city->id,
                            'two_gis_link'=> $cemeteryRow[$columns['URL']],
                            'area_id' => $area->id,
                            'phone' => normalizePhone($cemeteryRow[$columns['Телефоны']]),
                            'img_url' => 'default',
                            'href_img' => 1,
                            'price_burial_location' => $price ?? 0,
                            'time_difference' => $time_difference,
                        ];
                        

                        
                        

    
                        if ($importAction === 'create') {

                            // Проверяем существование по кадастровому номеру
                            $existing = Cemetery::find($objectId);

                            if ($existing) {
                                $skippedRows++;
                                continue;
                            }    

                            $cemetery=Cemetery::create($cemeteryData);
                            
                            $createdCemeteries++;

                            // Обработка режима работы при создании
                            if(isset($columns['Режим работы']) && $cemeteryRow[$columns['Режим работы']] != null) {
                                $workHours = $cemeteryRow[$columns['Режим работы']];
                                $days = parseWorkingHours($workHours);
                                
                                foreach($days as $day) {
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
                        } elseif ($importAction === 'update') {
                            if($cadastralNumber!=null){
                                // Проверяем существование по кадастровому номеру
                                $cemetery = Cemetery::where('cadastral_number', $cadastralNumber)->first();
                                if ($cemetery) {
                                    $skippedRows++;
                                    continue;
                                }    
                            }

                            // Проверяем существование по кадастровому номеру
                            $cemetery = Cemetery::find($objectId);
                            if ($cemetery) {
                                $skippedRows++;
                                continue;
                            }    
                            
                            if ($cemetery) {
                                $updateData = [];
                                foreach ($updateFields as $field) {
                                    if (isset($cemeteryData[$field])) {
                                        $updateData[$field] = $cemeteryData[$field];
                                    }
                                }
                                
                                if (!empty($updateData)) {
                                    $cemetery->update($updateData);
                                    $updatedCemeteries++;
                                }

                                if(in_array('working_hours', $updateFields) && isset($columns['Режим работы'])) {
                                $workHours = $cemeteryRow[$columns['Режим работы']] ?? null;
                                if($workHours) {
                                    // Удаляем старые записи о рабочем времени
                                    WorkingHoursCemetery::where('cemetery_id', $cemetery->id)->delete();
                                    
                                    // Создаем новые записи
                                    $days = parseWorkingHours($workHours);
                                    foreach($days as $day) {
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
                            } else {
                                $skippedRows++;
                            }
                        }
                    } catch (\Exception $e) {
                        $skippedRows++;
                    }
                }
                
                $processedFiles++;
            } catch (\Exception $e) {
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

    // public static function index($request) {
    //     // Валидация входных данных
    //     $validated = $request->validate([
    //         'files' => 'required|array',
    //         'files.*' => 'file|mimes:xlsx,xls',
    //         'price_geo' => 'nullable|numeric',
    //         'import_type' => 'required|in:create,update',
    //         'columns_to_update' => 'nullable|array',
    //     ]);
    
    //     $files = $request->file('files');
    //     $price = $request->input('price_geo');
    //     $importAction = $request->input('import_type', 'create');
    //     $updateFields = $request->input('columns_to_update', []);
        
    //     $processedFiles = 0;
    //     $createdCemeteries = 0;
    //     $updatedCemeteries = 0;
    //     $skippedRows = 0;
    //     $errors = [];
    
    //     foreach ($files as $file) {
    //         if (!$file->isValid()) {
    //             $errors[] = "Файл {$file->getClientOriginalName()} не валиден";
    //             continue;
    //         }
    
    //         try {
    //             $spreadsheet = IOFactory::load($file->getRealPath());
    //             $sheet = $spreadsheet->getActiveSheet();
    //             $titles = $sheet->toArray()[0];
    //             $cemeteriesData = array_slice($sheet->toArray(), 1);
    //             $filteredTitles = array_filter($titles, fn($value) => $value !== null);
    //             $columns = array_flip($filteredTitles);
    
    //             // Проверка наличия обязательных колонок
    //             $requiredColumns = ['Наименование кладбища', 'Latitude', 'Longitude', 'кадастровый номер'];
    //             foreach ($requiredColumns as $col) {
    //                 if (!isset($columns[$col])) {
    //                     continue 2;
    //                 }
    //             }
    
    //             foreach ($cemeteriesData as $rowIndex => $cemeteryRow) {
    //                 try {
    //                     // Проверка обязательных полей
    //                     if (empty($cemeteryRow[$columns['Наименование кладбища']])) {
    //                         $skippedRows++;
    //                         continue;
    //                     }
    
    //                     $cadastralNumber = $cemeteryRow[$columns['кадастровый номер']];
    //                     if (empty($cadastralNumber)) {
    //                         $skippedRows++;
    //                         continue;
    //                     }
    
    //                     // Проверка координат
    //                     if (empty($cemeteryRow[$columns['Latitude']])) {
    //                         $skippedRows++;
    //                         continue;
    //                     }
    
    //                     if (empty($cemeteryRow[$columns['Longitude']])) {
    //                         $skippedRows++;
    //                         continue;
    //                     }
    
                        
    //                     $objects = linkRegionDistrictCity(
    //                         $cemeteryRow[$columns['Край/Область'] ?? null],
    //                         $cemeteryRow[$columns['Муниципального округа'] ?? null],
    //                         $cemeteryRow[$columns['Населённый пункт'] ?? null]
    //                     );
    
    //                     $area = $objects['district'] ?? null;
    //                     $city = $objects['city'] ?? null;
    
    //                     if (!$city || !$area) {
    //                         $skippedRows++;
    //                         continue;
    //                     }
    
    //                     $status = $cemeteryRow[$columns['Статус кладбища']] == 'Открыто' ? 1 : 0;
                        

    //                     $time_difference = $city->utc_offset ?? null;
    //                     if($time_difference==null && env('API_WORK')=='true'){
    //                         $time_difference=differencetHoursTimezone(getTimeByCoordinates($cemeteryRow[$columns['Latitude']],$cemeteryRow[$columns['Longitude']])['timezone']);
    //                         $city->update(['utc_offset'=> $time_difference]);
                            
    //                     }
    //                     if($time_difference==null){
    //                         $time_difference=0;
    //                     }   

    //                     $cemeteryData = [
    //                         'title' => $cemeteryRow[$columns['Наименование кладбища']],
    //                         'adres' => $cemeteryRow[$columns['Ориентир'] ?? null],
    //                         'content'=>$cemeteryRow[$columns['SEO Описание']]  ?? $cemeteryRow[$columns['Описание']],
    //                         'rating'=>$cemeteryRow[$columns['Рейтинг']],
    //                         'width' => $cemeteryRow[$columns['Latitude']],
    //                         'longitude' => $cemeteryRow[$columns['Longitude']],
    //                         'city_id' => $city->id,
    //                         'two_gis_link'=> $crematoriumRow[$columns['URL']]  ?? null,
    //                         'area_id' => $area->id,
    //                         'phone' => normalizePhone($cemeteryRow[$columns['Тел. Ответственного'] ?? null]),
    //                         'square' => $cemeteryRow[$columns['Общая площадь (га)'] ?? null],
    //                         'responsible' => $cemeteryRow[$columns['Ответственный'] ?? null],
    //                         'cadastral_number' => $cadastralNumber, // Сохраняем оригинальный кадастровый номер
    //                         'status' => $status,
    //                         'img_url' => 'default',
    //                         'href_img' => 1,
    //                         'count_burials'=>$cemeteryRow[$columns['Захоронения'] ?? null],
    //                         'inn'=>$cemeteryRow[$columns['ИНН'] ?? null],
    //                         'price_burial_location' => $price ?? 0,
    //                         'time_difference' => $time_difference,
    //                     ];
    
    //                     if ($importAction === 'create') {
    //                         // Проверяем существование по кадастровому номеру
    //                         $existing = Cemetery::where('cadastral_number', $cadastralNumber)->first();
    //                         if ($existing) {
    //                             $skippedRows++;
    //                             continue;
    //                         }
    //                         $cemetery=Cemetery::create($cemeteryData);
    //                         // Обработка режима работы при создании
    //                         if(isset($columns['Режим работы']) && $cemeteryRow[$columns['Режим работы']] != null) {
    //                             $workHours = $cemeteryRow[$columns['Режим работы']];
    //                             $days = parseWorkingHours($workHours);
                                
    //                             foreach($days as $day) {
    //                                 $holiday = ($day['time_start_work'] == 'Выходной') ? 1 : 0;
    //                                 WorkingHoursCemetery::create([
    //                                     'day' => $day['day'],
    //                                     'time_start_work' => $day['time_start_work'],
    //                                     'time_end_work' => $day['time_end_work'],
    //                                     'holiday' => $holiday,
    //                                     'cemetery_id' => $cemetery->id,
    //                                 ]);
    //                             }
    //                         }
    //                         $createdCemeteries++;
    //                     } elseif ($importAction === 'update') {
    //                         // Ищем по кадастровому номеру
    //                         $cemetery = Cemetery::where('cadastral_number', $cadastralNumber)->first();
                            
    //                         if ($cemetery) {
    //                             $updateData = [];
    //                             foreach ($updateFields as $field) {
    //                                 if (isset($cemeteryData[$field])) {
    //                                     $updateData[$field] = $cemeteryData[$field];
    //                                 }
    //                             }
                                
    //                             if (!empty($updateData)) {
    //                                 $cemetery->update($updateData);
    //                                 $updatedCemeteries++;
    //                             }

    //                             if(in_array('working_hours', $updateFields) && isset($columns['Режим работы'])) {
    //                             $workHours = $cemeteryRow[$columns['Режим работы']] ?? null;
    //                             if($workHours) {
    //                                 // Удаляем старые записи о рабочем времени
    //                                 WorkingHoursCemetery::where('cemetery_id', $cemetery->id)->delete();
                                    
    //                                 // Создаем новые записи
    //                                 $days = parseWorkingHours($workHours);
    //                                 foreach($days as $day) {
    //                                     $holiday = ($day['time_start_work'] == 'Выходной') ? 1 : 0;
    //                                     WorkingHoursCemetery::create([
    //                                         'day' => $day['day'],
    //                                         'time_start_work' => $day['time_start_work'],
    //                                         'time_end_work' => $day['time_end_work'],
    //                                         'holiday' => $holiday,
    //                                         'cemetery_id' => $cemetery->id,
    //                                     ]);
    //                                 }
    //                             }
    //                         }
    //                         } else {
    //                             $skippedRows++;
    //                         }
    //                     }
    //                 } catch (\Exception $e) {
    //                     $skippedRows++;
    //                 }
    //             }
                
    //             $processedFiles++;
    //         } catch (\Exception $e) {
    //         }
    //     }
    
    //     $message = "Импорт кладбищ завершен. " .
    //                "Файлов обработано: $processedFiles, " .
    //                "Создано кладбищ: $createdCemeteries, " .
    //                "Обновлено кладбищ: $updatedCemeteries, " .
    //                "Пропущено строк: $skippedRows";
    
        
    
    //     return redirect()->back()
    //         ->with("message_cart", $message)
    //         ->withErrors($errors);
    // }


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
            
            $cemeteryId = $review[$columnIndexes['cemetery_id']] ?? null;
            $reviewerName = $review[$columnIndexes['name']] ?? null;
            $reviewDate = $review[$columnIndexes['date']] ?? null;
            $rating = $review[$columnIndexes['rating']] ?? null;
            $content = $review[$columnIndexes['content']] ?? null;
            
            if (empty($cemeteryId)) {
                $errors[] = "Строка {$rowNumber}: Не указан ID кладбища";
                $skippedReviews++;
                continue;
            }
            
            $cemetery = Cemetery::find($cemeteryId);
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