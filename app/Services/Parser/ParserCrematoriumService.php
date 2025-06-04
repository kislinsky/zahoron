<?php

namespace App\Services\Parser;

use App\Models\Cemetery;
use App\Models\City;
use App\Models\Crematorium;
use App\Models\Edge;
use App\Models\ImageCrematorium;
use App\Models\ReviewCrematorium;
use App\Models\WorkingHoursCrematorium;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ParserCrematoriumService
{
    // public static function index($request){
    //     $spreadsheet = new Spreadsheet();
    //     $file = $request->file('file');
    //     $spreadsheet = IOFactory::load($file);
    //     // Получение данных из первого листа
    //     $sheet = $spreadsheet->getActiveSheet();
    //     $crematoriums = array_slice($sheet->toArray(),1);
    //     foreach($crematoriums as $crematorium){
    //         $city=createCity($crematorium[7],$crematorium[6]);
    //         if($city!=null && $crematorium[12]!=null && $crematorium[13]!=null){
    //             $content=$crematorium[31];
    //             $timezone=getTimeByCoordinates($crematorium[12],$crematorium[13])['timezone'];
    //             $crematorium_create=Crematorium::create([
    //                 'title'=>$crematorium[3],
    //                 'village'=>$crematorium[8],
    //                 'adres'=>$crematorium[10],
    //                 'width'=>$crematorium[12],
    //                 'longitude'=>$crematorium[13],
    //                 'phone'=>phoneImport($crematorium[15]),
    //                 'email'=>$crematorium[19],
    //                 'img'=>$crematorium[34],
    //                 'city_id'=>$city->id,
    //                 'rating'=>$crematorium[26],
    //                 'mini_content'=>$crematorium[31],
    //                 'href_img'=>1,
    //                 'content'=>$content,
    //                 'time_difference'=>differencetHoursTimezone($timezone),
    //             ]);
    //             if($crematorium[35]!=null){
    //                 $imgs=explode(',',$crematorium[35]);
    //                 foreach($imgs as $img){
    //                     ImageCrematorium::create([
    //                         'title'=>$img,
    //                         'href_img'=>1,
    //                         'crematorium_id'=>$crematorium_create->id,
    //                     ]);
    //                 }
    //             }
    //             if($crematorium[17]!=null){
    //                 $worktime=explode(',',$crematorium[17]);
    //                 foreach($worktime as $days){
    //                     $days=parseWorkingHours($days);
    //                     foreach($days as $day){
    //                         $holiday=0;
    //                         if($day['time_start_work']=='Выходной'){
    //                             $holiday=1;
    //                         }
    //                         WorkingHoursCrematorium::create([
    //                             'day'=>$day['day'],
    //                             'time_start_work'=>$day['time_start_work'],
    //                             'time_end_work'=>$day['time_end_work'],
    //                             'holiday'=>$holiday,
    //                             'crematorium_id'=>$crematorium_create->id,
    //                         ]);
    //                     }

    //                 }
    //             }
    //         }
    //     }
    //     return redirect()->back()->with("message_cart", 'Крематории успешно добавлены');
    // }



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
    $createdCrematoriums = 0;
    $updatedCrematoriums = 0;
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
            $crematoriumsData = array_slice($sheet->toArray(), 1);
            $filteredTitles = array_filter($titles, fn($value) => $value !== null);
            $columns = array_flip($filteredTitles);

            foreach ($crematoriumsData as $rowIndex => $crematoriumRow) {
                try {
                    // Получаем ID если есть колонка ID
                    $objectId = isset($columns['ID']) ? rtrim($crematoriumRow[$columns['ID']] ?? '', '!') : null;

                    // Для режима update пропускаем если нет ID
                    if ($importAction === 'update' && !$objectId) {
                        $skippedRows++;
                        continue;
                    }

                    // Получаем связанные объекты (регион, район, город)
                    $objects = linkRegionDistrictCity(
                        $crematoriumRow[$columns['Регион'] ?? null] ?? null,
                        $crematoriumRow[$columns['Район'] ?? null] ?? null,
                        $crematoriumRow[$columns['Населённый пункт'] ?? null] ?? null
                    );
                    
                    $area = $objects['district'] ?? null;
                    $city = $objects['city'] ?? null;

                    // Получаем разницу во времени если есть координаты
                    $time_difference = $city->utc_offset ?? null;
                    if ($time_difference == null && env('API_WORK') == 'true' && 
                        isset($columns['Latitude']) && isset($columns['Longitude']) &&
                        !empty($crematoriumRow[$columns['Latitude']]) && !empty($crematoriumRow[$columns['Longitude']])) {
                        $time_difference = differencetHoursTimezone(getTimeByCoordinates(
                            $crematoriumRow[$columns['Latitude']], 
                            $crematoriumRow[$columns['Longitude']]
                        )['timezone']);
                        
                        if ($city) {
                            $city->update(['utc_offset' => $time_difference]);
                        }
                    }

                    // Формируем данные для крематория
                    $crematoriumData = [
                        'title' => $crematoriumRow[$columns['Название организации'] ?? null] ?? null,
                        'adres' => $crematoriumRow[$columns['Адрес'] ?? null] ?? null,
                        'width' => $crematoriumRow[$columns['Latitude'] ?? null] ?? null,
                        'rating' => $crematoriumRow[$columns['Рейтинг'] ?? null] ?? null,
                        'longitude' => $crematoriumRow[$columns['Longitude'] ?? null] ?? null,
                        'city_id' => $city->id ?? null,
                        'phone' => normalizePhone($crematoriumRow[$columns['Телефоны'] ?? null] ?? null),
                        'content' => $crematoriumRow[$columns['SEO Описание'] ?? null] ?? ($crematoriumRow[$columns['Описание'] ?? null] ?? null),
                        'img_url' => $crematoriumRow[$columns['Логотип'] ?? null] ?? 'default',
                        'href_img' => 1,
                        'two_gis_link' => $crematoriumRow[$columns['URL'] ?? null] ?? null,
                        'time_difference' => $time_difference,
                        'url_site' => $crematoriumRow[$columns['Сайт'] ?? null] ?? null,
                    ];

                    // Обработка логотипа
                    if (isset($columns['Логотип']) && $crematoriumRow[$columns['Логотип']] != 'default') {
                        if ($crematoriumRow[$columns['Логотип']] != null && !isBrokenLink($crematoriumRow[$columns['Логотип']])) {
                            $crematoriumData['img_url'] = $crematoriumRow[$columns['Логотип']];
                        } else {
                            $crematoriumData['img_url'] = 'default';
                        }
                    }

                    if ($importAction === 'create') {
                        // Для создания - если нет ID, пропускаем
                        if (!$objectId) {
                            $skippedRows++;
                            continue;
                        }

                        // Проверяем, существует ли уже запись с таким ID
                        if (Crematorium::find($objectId)) {
                            $skippedRows++;
                            continue;
                        }

                        // Создаем новую запись
                        $crematoriumData['id'] = $objectId;
                        $crematorium = Crematorium::create($crematoriumData);
                        $createdCrematoriums++;

                        // Обработка режима работы
                        if (isset($columns['Режим работы']) && !empty($crematoriumRow[$columns['Режим работы']])) {
                            $workHours = $crematoriumRow[$columns['Режим работы']];
                            $days = parseWorkingHours($workHours);
                            
                            foreach ($days as $day) {
                                $holiday = ($day['time_start_work'] == 'Выходной') ? 1 : 0;
                                WorkingHoursCrematorium::create([
                                    'day' => $day['day'],
                                    'time_start_work' => $day['time_start_work'],
                                    'time_end_work' => $day['time_end_work'],
                                    'holiday' => $holiday,
                                    'crematorium_id' => $crematorium->id,
                                ]);
                            }
                        }

                        // Обработка фотографий
                        if (isset($columns['Фотографии']) && !empty($crematoriumRow[$columns['Фотографии']])) {
                            $urls_array = explode(', ', $crematoriumRow[$columns['Фотографии']]);
                            foreach ($urls_array as $img) {
                                if ($img != null && !isBrokenLink($img)) {
                                    ImageCrematorium::create([
                                        'img_url' => $img,
                                        'href_img' => 1,
                                        'crematorium_id' => $crematorium->id,
                                    ]);
                                }
                            }
                        }
                    } elseif ($importAction === 'update') {
                        // Для обновления - находим существующую запись
                        $crematorium = Crematorium::find($objectId);
           
                        if ($crematorium) {
                            // Обновляем только указанные поля
                            $dataToUpdate = [];
                            foreach ($updateFields as $field) {
                                if (array_key_exists($field, $crematoriumData) && !is_null($crematoriumData[$field])) {
                                    $dataToUpdate[$field] = $crematoriumData[$field];
                                }
                            }

                            if (!empty($dataToUpdate)) {
                                $crematorium->update($dataToUpdate);
                                $updatedCrematoriums++;
                            }

                            // Обработка режима работы при обновлении
                            if (in_array('working_hours', $updateFields) && isset($columns['Режим работы']) && !empty($crematoriumRow[$columns['Режим работы']])) {
                                WorkingHoursCrematorium::where('crematorium_id', $crematorium->id)->delete();
                                
                                $workHours = $crematoriumRow[$columns['Режим работы']];
                                $days = parseWorkingHours($workHours);
                                
                                foreach ($days as $day) {
                                    $holiday = ($day['time_start_work'] == 'Выходной') ? 1 : 0;
                                    WorkingHoursCrematorium::create([
                                        'day' => $day['day'],
                                        'time_start_work' => $day['time_start_work'],
                                        'time_end_work' => $day['time_end_work'],
                                        'holiday' => $holiday,
                                        'crematorium_id' => $crematorium->id,
                                    ]);
                                }
                            }

                            // Обработка фотографий при обновлении
                            if (in_array('galerey', $updateFields) && isset($columns['Фотографии']) && !empty($crematoriumRow[$columns['Фотографии']])) {
                                ImageCrematorium::where('crematorium_id', $crematorium->id)->delete();
                                
                                $urls_array = explode(', ', $crematoriumRow[$columns['Фотографии']]);
                                foreach ($urls_array as $img) {
                                    if ($img != null && !isBrokenLink($img)) {
                                        ImageCrematorium::create([
                                            'img_url' => $img,
                                            'href_img' => 1,
                                            'crematorium_id' => $crematorium->id,
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

    $message = "Импорт крематориев завершен. " .
               "Файлов обработано: $processedFiles, " .
               "Создано крематориев: $createdCrematoriums, " .
               "Обновлено крематориев: $updatedCrematoriums, " .
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
                    ReviewCrematorium::create([
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