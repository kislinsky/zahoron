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
    // public static function index($request){
    //     $spreadsheet = new Spreadsheet();
    //     $file = $request->file('file');
    //     $spreadsheet = IOFactory::load($file);
    //     // Получение данных из первого листа
    //     $sheet = $spreadsheet->getActiveSheet();
    //     $columbariums = array_slice($sheet->toArray(),1);
    //     foreach($columbariums as $columbarium){
    //         $city=createCity($columbarium[7],$columbarium[6]);
    //         if($city!=null && $columbarium[12]!=null && $columbarium[13]!=null){
    //             $content=$columbarium[31];
    //             $timezone=getTimeByCoordinates($columbarium[12],$columbarium[13])['timezone'];                
    //             $columbarium_create=Columbarium::create([
    //                 'title'=>$columbarium[3],
    //                 'village'=>$columbarium[8],
    //                 'adres'=>$columbarium[10],
    //                 'width'=>$columbarium[12],
    //                 'longitude'=>$columbarium[13],
    //                 'phone'=>phoneImport($columbarium[15]),
    //                 'email'=>$columbarium[19],
    //                 'img'=>$columbarium[34],
    //                 'city_id'=>$city->id,
    //                 'rating'=>$columbarium[26],
    //                 'mini_content'=>$columbarium[31],
    //                 'href_img'=>1,
    //                 'content'=>$content,
    //                 'time_difference'=>differencetHoursTimezone($timezone),
    //             ]);
    //             if($columbarium[35]!=null){
    //                 $imgs=explode(',',$columbarium[35]);
    //                 foreach($imgs as $img){
    //                     ImageCrematorium::create([
    //                         'title'=>$img,
    //                         'href_img'=>1,
    //                         'columbarium_id'=>$columbarium_create->id,
    //                     ]);
    //                 }
    //             }
    //             if($columbarium[17]!=null){
    //                 $worktime=explode(',',$columbarium[17]);
    //                 foreach($worktime as $days){
    //                     $days=parseWorkingHours($days);
    //                     foreach($days as $day){
    //                         $holiday=0;
    //                         if($day['time_start_work']=='Выходной'){
    //                             $holiday=1;
    //                         }
    //                         WorkingHoursColumbarium::create([
    //                             'day'=>$day['day'],
    //                             'time_start_work'=>$day['time_start_work'],
    //                             'time_end_work'=>$day['time_end_work'],
    //                             'holiday'=>$holiday,
    //                             'columbarium_id'=>$columbarium_create->id,
    //                         ]);
    //                     }

    //                 }
    //             }
    //         }
    //     }
    //     return redirect()->back()->with("message_cart", 'Колумабрии успешно добавлены');
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
            
            $columns = array_flip($titles);

            // Проверка наличия обязательных колонок
            $requiredColumns = ['Название организации', 'Latitude', 'Longitude', 'ID','Адрес'];
            foreach ($requiredColumns as $col) {
                if (!isset($columns[$col])) {
                    continue 2;
                }
            }

            foreach ($columbariumsData as $rowIndex => $columbariumRow) {
                try {
                    // Проверка обязательных полей
                    if (empty($columbariumRow[$columns['Название организации']])) {
                        $skippedRows++;
                        continue;
                    }

                    if (empty($columbariumRow[$columns['ID']])) {
                        $skippedRows++;
                        continue;
                    }

                     if (empty($columbariumRow[$columns['Адрес']])) {
                        $skippedRows++;
                        continue;
                    }
                    // Проверка координат
                    if (empty($columbariumRow[$columns['Latitude']])) {
                        $skippedRows++;
                        continue;
                    }

                    if (empty($columbariumRow[$columns['Longitude']])) {
                        $skippedRows++;
                        continue;
                    }

                    $objects = linkRegionDistrictCity(
                        $columbariumRow[$columns['Регион'] ?? null],
                        $columbariumRow[$columns['Район'] ?? null],
                        $columbariumRow[$columns['Населённый пункт'] ?? null]
                    );
                    $area = $objects['district'] ?? null;
                    $city = $objects['city'] ?? null;

                    if (!$city || !$area) {
                        $skippedRows++;
                        continue;
                    }

                    $objectId = rtrim($columbariumRow[$columns['ID']] ?? '', '!');

                    $time_difference = $city->utc_offset ?? null;
                    if($time_difference==null && env('API_WORK')=='true'){
                        $time_difference=differencetHoursTimezone(getTimeByCoordinates($columbariumRow[$columns['Latitude']],$columbariumRow[$columns['Longitude']])['timezone']);
                        $city->update(['utc_offset'=> $time_difference]);
                    }

                    $columbariumData = [
                        'id' => $objectId,
                        'title' => $columbariumRow[$columns['Название организации']],
                        'adres' => $columbariumRow[$columns['Адрес']],
                        'width' => $columbariumRow[$columns['Latitude']],
                        'longitude' => $columbariumRow[$columns['Longitude']],
                        'city_id' => $city->id,
                        'phone' => normalizePhone($columbariumRow[$columns['Телефоны'] ?? null]),
                        'content'=>$columbariumRow[$columns['Описание'] ?? $columbariumRow[$columns['SEO Описание']] ?? null],
                        'img_url' => $columbariumRow[$columns['Логотип']] ?? 'default',
                        'href_img' => 1,
                        'time_difference' => $time_difference,
                        'url_site' => $columbariumRow[$columns['Сайт'] ?? null] ?? null,
                    ];

                    if($columbariumRow[$columns['Логотип']]!='default') {
                        if(!isBrokenLink($columbariumRow[$columns['Логотип']])){
                            $mortuaryData['img_url'] = $columbariumRow[$columns['Логотип']];
                        }else{
                            $mortuaryData['img_url'] = 'default';
                        }
                    }

                    if ($importAction === 'create' && Columbarium::find($objectId)==null) {
                        $columbarium = Columbarium::create($columbariumData);
                        $createdColumbariums++;

                        // Обработка режима работы при создании
                        if(isset($columns['Режим работы']) && $columbariumRow[$columns['Режим работы']] != null) {
                            $workHours = $columbariumRow[$columns['Режим работы']];
                            $days = parseWorkingHours($workHours);
                            
                            foreach($days as $day) {
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

                        if(isset($columns['Фотографии']) && $columbariumRow[$columns['Фотографии']] != null) {
                            ImageColumbarium::where('columbarium_id', $columbarium->id)->delete();
                            
                            $urls_array = explode(', ', $columbariumRow[$columns['Фотографии']]);
                            foreach($urls_array as $img) {
                                if(!isBrokenLink($img)){
                                    ImageColumbarium::create([
                                        'img_url' => $img,
                                        'href_img' => 1,
                                        'columbarium_id' => $columbarium->id,
                                    ]);
                                }
                            }
                        }


                    } elseif ($importAction === 'update') {
                        $columbarium = Columbarium::find($objectId);
                        
                        if ($columbarium) {
                            $updateData = [];
                            foreach ($updateFields as $field) {
                                if (isset($columbariumData[$field])) {
                                    $updateData[$field] = $columbariumData[$field];
                                }
                            }
                            
                            if (!empty($updateData)) {
                                $columbarium->update($updateData);
                                $updatedColumbariums++;
                            }

                            // Обработка режима работы при обновлении
                            if(in_array('working_hours', $updateFields) && isset($columns['Режим работы'])) {
                                $workHours = $columbariumRow[$columns['Режим работы']] ?? null;
                                if($workHours) {
                                    // Удаляем старые записи о рабочем времени
                                    WorkingHoursColumbarium::where('columbarium_id', $columbarium->id)->delete();
                                    
                                    // Создаем новые записи
                                    $days = parseWorkingHours($workHours);
                                    foreach($days as $day) {
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