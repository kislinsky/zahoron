<?php

namespace App\Services\Parser;

use App\Models\Cemetery;
use App\Models\City;
use App\Models\Columbarium;
use App\Models\Edge;
use App\Models\ImageColumbarium;
use App\Models\ImageCrematorium;
use App\Models\ReviewColumbarium;
use App\Models\WorkingHoursColumbarium;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ParserColumbariumService
{

    public static function index($request) {
    // Валидация входных данных
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
    $createdColumbariums = 0;
    $updatedColumbariums = 0;
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
            $columbariumsData = array_slice($sheet->toArray(), 1);
            $filteredTitles = array_filter($titles, fn($value) => $value !== null);
            $columns = array_flip($filteredTitles);

            foreach ($columbariumsData as $rowIndex => $columbariumRow) {
                try {
                    // Получаем ID если есть колонка ID
                    $objectId = isset($columns['ID']) ? rtrim($columbariumRow[$columns['ID']] ?? '', '!') : null;

                    // Для режима update пропускаем если нет ID
                    if ($importAction === 'update' && !$objectId) {
                        $skippedRows++;
                        continue;
                    }

                    // Получаем связанные объекты (регион, район, город)
                    $objects = linkRegionDistrictCity(
                        $columbariumRow[$columns['Регион'] ?? null] ?? null,
                        $columbariumRow[$columns['Район'] ?? null] ?? null,
                        $columbariumRow[$columns['Населённый пункт'] ?? null] ?? null
                    );
                    
                    $area = $objects['district'] ?? null;
                    $city = $objects['city'] ?? null;

                    // Получаем разницу во времени если есть координаты
                    $time_difference = $city->utc_offset ?? null;
                    if ($time_difference == null && env('API_WORK') == 'true' && 
                        isset($columns['Latitude']) && isset($columns['Longitude']) &&
                        !empty($columbariumRow[$columns['Latitude']]) && !empty($columbariumRow[$columns['Longitude']])) {
                        $time_difference = differencetHoursTimezone(getTimeByCoordinates(
                            $columbariumRow[$columns['Latitude']], 
                            $columbariumRow[$columns['Longitude']]
                        )['timezone']);
                        
                        if ($city) {
                            $city->update(['utc_offset' => $time_difference]);
                        }
                    }

                    // Формируем данные для колумбария
                    $columbariumData = [
                        'title' => $columbariumRow[$columns['Название организации'] ?? null] ?? null,
                        'adres' => $columbariumRow[$columns['Адрес'] ?? null] ?? null,
                        'width' => $columbariumRow[$columns['Latitude'] ?? null] ?? null,
                        'rating' => $columbariumRow[$columns['Рейтинг'] ?? null] ?? null,
                        'longitude' => $columbariumRow[$columns['Longitude'] ?? null] ?? null,
                        'city_id' => $city->id ?? null,
                        'phone' => normalizePhone($columbariumRow[$columns['Телефоны'] ?? null] ?? null),
                        'content' => $columbariumRow[$columns['SEO Описание'] ?? null] ?? ($columbariumRow[$columns['Описание'] ?? null] ?? null),
                        'img_url' => $columbariumRow[$columns['Логотип'] ?? null] ?? 'default',
                        'href_img' => 1,
                        'two_gis_link' => $columbariumRow[$columns['URL'] ?? null] ?? null,
                        'time_difference' => $time_difference,
                        'url_site' => $columbariumRow[$columns['Сайт'] ?? null] ?? null,
                    ];

                    // Обработка логотипа
                    if (isset($columns['Логотип']) && $columbariumRow[$columns['Логотип']] != 'default') {
                        if ($columbariumRow[$columns['Логотип']] != null && !isBrokenLink($columbariumRow[$columns['Логотип']])) {
                            $columbariumData['img_url'] = $columbariumRow[$columns['Логотип']];
                        } else {
                            $columbariumData['img_url'] = 'default';
                        }
                    }

                    if ($importAction === 'create') {
                        // Для создания - если нет ID, пропускаем
                        if (!$objectId) {
                            $skippedRows++;
                            continue;
                        }

                        // Проверяем, существует ли уже запись с таким ID
                        if (Columbarium::find($objectId)) {
                            $skippedRows++;
                            continue;
                        }

                        // Создаем новую запись
                        $columbariumData['id'] = $objectId;
                        $columbarium = Columbarium::create($columbariumData);
                        $createdColumbariums++;

                        // Обработка режима работы
                        if (isset($columns['Режим работы']) && !empty($columbariumRow[$columns['Режим работы']])) {
                            $workHours = $columbariumRow[$columns['Режим работы']];
                            $days = parseWorkingHours($workHours);
                            
                            foreach ($days as $day) {
                                $holiday = ($day['time_start_work'] == 'Выходной') ? 1 : 0;
                                WorkingHoursColumbarium::create([
                                    'day' => $day['day'],
                                    'time_start_work' => $day['time_start_work'],
                                    'time_end_work' => $day['time_end_work'],
                                    'holiday' => $holiday,
                                    'columbarium_id' => $columbarium->id,
                                ]);
                            }
                        }

                        // Обработка фотографий
                        if (isset($columns['Фотографии']) && !empty($columbariumRow[$columns['Фотографии']])) {
                            $urls_array = explode(', ', $columbariumRow[$columns['Фотографии']]);
                            foreach ($urls_array as $img) {
                                if ($img != null && !isBrokenLink($img)) {
                                    ImageColumbarium::create([
                                        'img_url' => $img,
                                        'href_img' => 1,
                                        'columbarium_id' => $columbarium->id,
                                    ]);
                                }
                            }
                        }
                    } elseif ($importAction === 'update') {
                        // Для обновления - находим существующую запись
                        $columbarium = Columbarium::find($objectId);
           
                        if ($columbarium) {
                            // Обновляем только указанные поля
                            $dataToUpdate = [];
                            foreach ($updateFields as $field) {
                                if (array_key_exists($field, $columbariumData) && !is_null($columbariumData[$field])) {
                                    $dataToUpdate[$field] = $columbariumData[$field];
                                }
                            }

                            if (!empty($dataToUpdate)) {
                                $columbarium->update($dataToUpdate);
                                $updatedColumbariums++;
                            }

                            // Обработка режима работы при обновлении
                            if (in_array('working_hours', $updateFields) && isset($columns['Режим работы']) && !empty($columbariumRow[$columns['Режим работы']])) {
                                WorkingHoursColumbarium::where('columbarium_id', $columbarium->id)->delete();
                                
                                $workHours = $columbariumRow[$columns['Режим работы']];
                                $days = parseWorkingHours($workHours);
                                
                                foreach ($days as $day) {
                                    $holiday = ($day['time_start_work'] == 'Выходной') ? 1 : 0;
                                    WorkingHoursColumbarium::create([
                                        'day' => $day['day'],
                                        'time_start_work' => $day['time_start_work'],
                                        'time_end_work' => $day['time_end_work'],
                                        'holiday' => $holiday,
                                        'columbarium_id' => $columbarium->id,
                                    ]);
                                }
                            }

                            // Обработка фотографий при обновлении
                            if (in_array('galerey', $updateFields) && isset($columns['Фотографии']) && !empty($columbariumRow[$columns['Фотографии']])) {
                                ImageColumbarium::where('columbarium_id', $columbarium->id)->delete();
                                
                                $urls_array = explode(', ', $columbariumRow[$columns['Фотографии']]);
                                foreach ($urls_array as $img) {
                                    if ($img != null && !isBrokenLink($img)) {
                                        ImageColumbarium::create([
                                            'img_url' => $img,
                                            'href_img' => 1,
                                            'columbarium_id' => $columbarium->id,
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

    $message = "Импорт колумбариев завершен. " .
               "Файлов обработано: $processedFiles, " .
               "Создано колумбариев: $createdColumbariums, " .
               "Обновлено колумбариев: $updatedColumbariums, " .
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
                    ReviewColumbarium::create([
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