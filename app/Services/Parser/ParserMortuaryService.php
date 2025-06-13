<?php

namespace App\Services\Parser;

use App\Models\Cemetery;
use App\Models\City;

use App\Models\Edge;
use App\Models\ImageCemetery;
use App\Models\ImageMortuary;
use App\Models\Mortuary;
use App\Models\ReviewMortuary;
use App\Models\WorkingHoursMortuary;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ParserMortuaryService
{


  public static function index($request) {
    // Валидация только файлов и типа импорта
    $validated = $request->validate([
        'files' => 'required|array',
        'files.*' => 'file|mimes:xlsx,xls',
        'import_type' => 'required|in:create,update',
        'columns_to_update' => 'nullable|array',
    ]);

    $files = $request->file('files');
    $importAction = $request->input('import_type', 'create');
    $updateFields = $request->input('columns_to_update', []);
    
    $processedFiles = 0;
    $createdMortuaries = 0;
    $updatedMortuaries = 0;
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
            $mortuariesData = array_slice($sheet->toArray(), 1);
            $filteredTitles = array_filter($titles, fn($value) => $value !== null);
            $columns = array_flip($filteredTitles);

            foreach ($mortuariesData as $rowIndex => $mortuaryRow) {
                try {
                    // Получаем ID если есть колонка ID
                    $objectId = isset($columns['ID']) ? rtrim($mortuaryRow[$columns['ID']] ?? '', '!') : null;

                    // Для режима update пропускаем если нет ID
                    if ($importAction === 'update' && !$objectId) {
                        $skippedRows++;
                        continue;
                    }

                    // Получаем связанные объекты (регион, район, город)
                    $objects = linkRegionDistrictCity(
                        $mortuaryRow[$columns['Регион'] ?? null] ?? null,
                        $mortuaryRow[$columns['Район'] ?? null] ?? null,
                        $mortuaryRow[$columns['Населённый пункт'] ?? null] ?? null
                    );
                    
                    $area = $objects['district'] ?? null;
                    $city = $objects['city'] ?? null;

                    // Получаем разницу во времени если есть координаты
                    $time_difference = $city->utc_offset ?? null;
                    if ($time_difference == null && env('API_WORK') == 'true' && 
                        isset($columns['Latitude']) && isset($columns['Longitude']) &&
                        !empty($mortuaryRow[$columns['Latitude']]) && !empty($mortuaryRow[$columns['Longitude']])) {
                        $time_difference = differencetHoursTimezone(getTimeByCoordinates(
                            $mortuaryRow[$columns['Latitude']], 
                            $mortuaryRow[$columns['Longitude']]
                        )['timezone']);
                        
                        if ($city) {
                            $city->update(['utc_offset' => $time_difference]);
                        }
                         if($time_difference==null){
                                $time_difference=0;
                            }
                    }

                    if($time_difference==null){
                        $time_difference=0;
                    }   
                    // Формируем данные для морга
                    $mortuaryData = [
                        'title' => $mortuaryRow[$columns['Название организации'] ?? null] ?? null,
                        'adres' => $mortuaryRow[$columns['Адрес'] ?? null] ?? null,
                        'width' => $mortuaryRow[$columns['Latitude'] ?? null] ?? null,
                        'rating' => $mortuaryRow[$columns['Рейтинг'] ?? null] ?? null,
                        'longitude' => $mortuaryRow[$columns['Longitude'] ?? null] ?? null,
                        'city_id' => $city->id ?? null,
                        'phone' => normalizePhone($mortuaryRow[$columns['Телефоны'] ?? null] ?? null),
                        'content' => $mortuaryRow[$columns['SEO Описание'] ?? null] ?? ($mortuaryRow[$columns['Описание'] ?? null] ?? null),
                        'img_url' => $mortuaryRow[$columns['Логотип'] ?? null] ?? 'default',
                        'href_img' => 1,
                        'two_gis_link' => $mortuaryRow[$columns['URL'] ?? null] ?? null,
                        'time_difference' => $time_difference,
                        'url_site' => $mortuaryRow[$columns['Сайт'] ?? null] ?? null,
                    ];

                    // Обработка логотипа
                    if (isset($columns['Логотип']) && $mortuaryRow[$columns['Логотип']] != 'default') {
                        if ($mortuaryRow[$columns['Логотип']] != null && !isBrokenLink($mortuaryRow[$columns['Логотип']])) {
                            $mortuaryData['img_url'] = $mortuaryRow[$columns['Логотип']];
                        } else {
                            $mortuaryData['img_url'] = 'default';
                        }
                    }

                    if ($importAction === 'create') {
                        // Для создания - если нет ID, пропускаем (или можно генерировать, если нужно)
                        if (!$objectId) {
                            $skippedRows++;
                            continue;
                        }

                        // Проверяем, существует ли уже запись с таким ID
                        if (Mortuary::find($objectId)) {
                            $skippedRows++;
                            continue;
                        }

                        // Создаем новую запись
                        $mortuaryData['id'] = $objectId;
                        $mortuary = Mortuary::create($mortuaryData);
                        $createdMortuaries++;

                        // Обработка режима работы
                        if (isset($columns['Режим работы']) && !empty($mortuaryRow[$columns['Режим работы']])) {
                            $workHours = $mortuaryRow[$columns['Режим работы']];
                            $days = parseWorkingHours($workHours);
                            
                            foreach ($days as $day) {
                                $holiday = ($day['time_start_work'] == 'Выходной') ? 1 : 0;
                                WorkingHoursMortuary::create([
                                    'day' => $day['day'],
                                    'time_start_work' => $day['time_start_work'],
                                    'time_end_work' => $day['time_end_work'],
                                    'holiday' => $holiday,
                                    'mortuary_id' => $mortuary->id,
                                ]);
                            }
                        }

                        // Обработка фотографий
                        if (isset($columns['Фотографии']) && !empty($mortuaryRow[$columns['Фотографии']])) {
                            $urls_array = explode(', ', $mortuaryRow[$columns['Фотографии']]);
                            foreach ($urls_array as $img) {
                                if ($img != null && !isBrokenLink($img)) {
                                    ImageMortuary::create([
                                        'img_url' => $img,
                                        'href_img' => 1,
                                        'mortuary_id' => $mortuary->id,
                                    ]);
                                }
                            }
                        }
                    } elseif ($importAction === 'update') {
                        // Для обновления - находим существующую запись
                        $mortuary = Mortuary::find($objectId);
           
                        if ($mortuary) {
                            // Обновляем только указанные поля
                            $dataToUpdate = [];
                            foreach ($updateFields as $field) {
                                if (array_key_exists($field, $mortuaryData) && !is_null($mortuaryData[$field])) {
                                    $dataToUpdate[$field] = $mortuaryData[$field];
                                }
                            }

                            if (!empty($dataToUpdate)) {
                                $mortuary->update($dataToUpdate);
                                $updatedMortuaries++;
                            }

                            // Обработка режима работы при обновлении
                            if (in_array('working_hours', $updateFields) && isset($columns['Режим работы']) && !empty($mortuaryRow[$columns['Режим работы']])) {
                                WorkingHoursMortuary::where('mortuary_id', $mortuary->id)->delete();
                                
                                $workHours = $mortuaryRow[$columns['Режим работы']];
                                $days = parseWorkingHours($workHours);
                                
                                foreach ($days as $day) {
                                    $holiday = ($day['time_start_work'] == 'Выходной') ? 1 : 0;
                                    WorkingHoursMortuary::create([
                                        'day' => $day['day'],
                                        'time_start_work' => $day['time_start_work'],
                                        'time_end_work' => $day['time_end_work'],
                                        'holiday' => $holiday,
                                        'mortuary_id' => $mortuary->id,
                                    ]);
                                }
                            }

                            // Обработка фотографий при обновлении
                            if (in_array('galerey', $updateFields) && isset($columns['Фотографии']) && !empty($mortuaryRow[$columns['Фотографии']])) {
                                ImageMortuary::where('mortuary_id', $mortuary->id)->delete();
                                
                                $urls_array = explode(', ', $mortuaryRow[$columns['Фотографии']]);
                                foreach ($urls_array as $img) {
                                    if ($img != null && !isBrokenLink($img)) {
                                        ImageMortuary::create([
                                            'img_url' => $img,
                                            'href_img' => 1,
                                            'mortuary_id' => $mortuary->id,
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

    $message = "Импорт моргов завершен. " .
               "Файлов обработано: $processedFiles, " .
               "Создано моргов: $createdMortuaries, " .
               "Обновлено моргов: $updatedMortuaries, " .
               "Пропущено строк: $skippedRows";

    return redirect()->back()
        ->with("message_cart", $message)
        ->withErrors($errors);
}

    public static function importReviews($request){
        $spreadsheet = new Spreadsheet();
        $file = $request->file('file_reviews');
        $spreadsheet = IOFactory::load($file);
        // Получение данных из первого листа
        $sheet = $spreadsheet->getActiveSheet();
        $reviews = array_slice($sheet->toArray(),1);
        foreach($reviews as $review){
            $edge=Edge::where('title',$review[2])->first();
            if($edge!=null){
                $cities=City::where('edge_id',$edge->id)->pluck('id');
                $cemetery=Cemetery::where('title',$review[3])->whereIn('city_id',$cities)->first();
                if($cemetery!=null){
                    ReviewMortuary::create([
                        'name'=>$review[5],
                        'rating'=>$review[7],
                        'content'=>$review[9],
                        'created_at'=>$review[6],
                        'cemetery_id'=>$cemetery->id,
                        'status'=>1,
                    ]);
                    $cemetery->updateRating();
                }
            }
            
        }
        return redirect()->back()->with("message_cart", 'Отзывы успешно добавлены');

    }
}