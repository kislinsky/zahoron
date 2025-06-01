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
    // public static function index($request){
    //     $spreadsheet = new Spreadsheet();
    //     $file = $request->file('file');
    //     $spreadsheet = IOFactory::load($file);
    //     // Получение данных из первого листа
    //     $sheet = $spreadsheet->getActiveSheet();
    //     $mortuaries = array_slice($sheet->toArray(),1);
    //     foreach($mortuaries as $mortuary){
    //         $city=createCity($mortuary[7],$mortuary[6]);
    //         if($city!=null && $mortuary[12]!=null && $mortuary[13]!=null){
    //             $content=$mortuary[38];
    //             if($content==null){
    //                 $content=$mortuary[31];
    //             }
                
    //             $timezone=getTimeByCoordinates($mortuary[12],$mortuary[13])['timezone'];

    //             $mortuary_create=Mortuary::create([
    //                 'title'=>$mortuary[3],
    //                 'village'=>$mortuary[8],
    //                 'adres'=>$mortuary[10],
    //                 'width'=>$mortuary[12],
    //                 'longitude'=>$mortuary[13],
    //                 'phone'=>phoneImport($mortuary[15]),
    //                 'email'=>$mortuary[19],
    //                 'img'=>$mortuary[34],
    //                 'city_id'=>$city->id,
    //                 'rating'=>$mortuary[26],
    //                 'mini_content'=>$mortuary[31],
    //                 'href_img'=>1,
    //                 'content'=>$content,
    //                 'time_difference'=>differencetHoursTimezone($timezone),

    //             ]);
    //             if($mortuary[35]!=null){
    //                 $imgs=explode(',',$mortuary[35]);
    //                 foreach($imgs as $img){
    //                     ImageMortuary::create([
    //                         'title'=>$img,
    //                         'href_img'=>1,
    //                         'mortuary_id'=>$mortuary_create->id,
    //                     ]);
    //                 }
    //             }
    //             if($mortuary[17]!=null){
    //                 $worktime=explode(',',$mortuary[17]);
    //                 foreach($worktime as $days){
    //                     $days=parseWorkingHours($days);
    //                     foreach($days as $day){
    //                         $holiday=0;
    //                         if($day['time_start_work']=='Выходной'){
    //                             $holiday=1;
    //                         }
    //                         WorkingHoursMortuary::create([
    //                             'day'=>$day['day'],
    //                             'time_start_work'=>$day['time_start_work'],
    //                             'time_end_work'=>$day['time_end_work'],
    //                             'holiday'=>$holiday,
    //                             'mortuary_id'=>$mortuary_create->id,
    //                         ]);
    //                     }

    //                 }
    //             }
    //         }
    //     }
    //     return redirect()->back()->with("message_cart", 'Морги успешно добавлены');
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

            // Проверка наличия обязательных колонок
            $requiredColumns = ['Название организации', 'Latitude', 'Longitude', 'ID','Адрес'];
            foreach ($requiredColumns as $col) {
                if (!isset($columns[$col])) {
                    continue 2;
                }
            }
           

            foreach ($mortuariesData as $rowIndex => $mortuaryRow) {
                try {
                    // Проверка обязательных полей
                    if (empty($mortuaryRow[$columns['Название организации']])) {
                        $skippedRows++;
                        continue;
                    }

                    if (empty($mortuaryRow[$columns['Адрес']])) {
                        $skippedRows++;
                        continue;
                    }

                    if (empty($mortuaryRow[$columns['ID']])) {
                        $skippedRows++;
                        continue;
                    }

                    // Проверка координат
                    if (empty($mortuaryRow[$columns['Latitude']])) {
                        $skippedRows++;
                        continue;
                    }

                    if (empty($mortuaryRow[$columns['Longitude']])) {
                        $skippedRows++;
                        continue;
                    }

                    $objects = linkRegionDistrictCity(
                        $mortuaryRow[$columns['Регион'] ?? null],
                        $mortuaryRow[$columns['Район'] ?? null],
                        $mortuaryRow[$columns['Населённый пункт'] ?? null]
                    );
                    $area = $objects['district'] ?? null;
                    $city = $objects['city'] ?? null;

                    if (!$city || !$area) {
                        $skippedRows++;
                        continue;
                    }

                    $objectId = rtrim($mortuaryRow[$columns['ID']] ?? '', '!');

                    $time_difference = $city->utc_offset ?? null;
                    if($time_difference==null && env('API_WORK')=='true'){
                        $time_difference=differencetHoursTimezone(getTimeByCoordinates($mortuaryRow[$columns['Latitude']],$mortuaryRow[$columns['Longitude']])['timezone']);
                        $city->update(['utc_offset'=> $time_difference]);
                    }

                    $mortuaryData = [
                        'id'=>$objectId,
                        'title' => $mortuaryRow[$columns['Название организации']],
                        'adres' => $mortuaryRow[$columns['Адрес']],
                        'width' => $mortuaryRow[$columns['Latitude']],
                        'rating'=>$mortuaryRow[$columns['Рейтинг']],
                        'longitude' => $mortuaryRow[$columns['Longitude']],
                        'city_id' => $city->id,
                        'phone' => normalizePhone($mortuaryRow[$columns['Телефоны'] ?? null]),
                        'content'=>$mortuaryRow[$columns['SEO Описание']] ??  $mortuaryRow[$columns['Описание']],
                        'img_url' => $mortuaryRow[$columns['Логотип']] ?? 'default',
                        'href_img' => 1,
                        'two_gis_link'=> $crematoriumRow[$columns['URL']]  ?? null,
                        'time_difference' => $time_difference,
                        'url_site' => $mortuaryRow[$columns['Сайт']] ?? null,
                    ];

                    if($mortuaryRow[$columns['Логотип']]!='default') {
                        if($mortuaryRow[$columns['Логотип']]!=null && !isBrokenLink($mortuaryRow[$columns['Логотип']])){
                            $mortuaryData['img_url'] = $mortuaryRow[$columns['Логотип']];
                        }else{
                            $mortuaryData['img_url'] = 'default';
                        }
                    }

                    if ($importAction === 'create' && Mortuary::find($objectId)==null) {
                        $mortuary = Mortuary::create($mortuaryData);
                        $createdMortuaries++;

                        // Обработка режима работы при создании
                        if(isset($columns['Режим работы']) && $mortuaryRow[$columns['Режим работы']] != null) {
                            $workHours = $mortuaryRow[$columns['Режим работы']];
                            $days = parseWorkingHours($workHours);
                            
                            foreach($days as $day) {
                                $holiday = ($day['time_start_work'] == 'Выходной') ? 1 : 0;
                                ImageMortuary::create([
                                    'day' => $day['day'],
                                    'time_start_work' => $day['time_start_work'],
                                    'time_end_work' => $day['time_end_work'],
                                    'holiday' => $holiday,
                                    'mortuary_id' => $mortuary->id,
                                ]);
                            }
                        }


                        if(isset($columns['Фотографии']) && $mortuaryRow[$columns['Фотографии']] != null) {
                            ImageMortuary::where('mortuary_id', $mortuary->id)->delete();
                            
                            $urls_array = explode(', ', $mortuaryRow[$columns['Фотографии']]);
                            foreach($urls_array as $img) {
                                if($img!=null && !isBrokenLink($img)){
                                    ImageMortuary::create([
                                        'img_url' => $img,
                                        'href_img' => 1,
                                        'mortuary_id' => $mortuary->id,
                                    ]);
                                }
                            }
                        }

                    } elseif ($importAction === 'update') {
                        $mortuary = Mortuary::find($objectId);
                        
                        if ($mortuary) {
                            $updateData = [];
                            foreach ($updateFields as $field) {
                                if (isset($mortuaryData[$field])) {
                                    $updateData[$field] = $mortuaryData[$field];
                                }
                            }
                            
                            if (!empty($updateData)) {
                                $mortuary->update($updateData);
                                $updatedMortuaries++;
                            }

                            // Обработка режима работы при обновлении
                            if(in_array('working_hours', $updateFields) && isset($columns['Режим работы'])) {
                                $workHours = $mortuaryRow[$columns['Режим работы']] ?? null;
                                if($workHours) {
                                    // Удаляем старые записи о рабочем времени
                                    WorkingHoursMortuary::where('mortuary_id', $mortuary->id)->delete();
                                    
                                    // Создаем новые записи
                                    $days = parseWorkingHours($workHours);
                                    foreach($days as $day) {
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
                            }
                            if(in_array('galerey', $updateFields) && isset($columns['Фотографии'])) {
                                ImageMortuary::where('mortuary_id', $mortuary->id)->delete();
                                
                                $urls_array = explode(', ', $mortuaryRow[$columns['Фотографии']]);
                                foreach($urls_array as $img) {
                                    if($img!=null && !isBrokenLink($img)){
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