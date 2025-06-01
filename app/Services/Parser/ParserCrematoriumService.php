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
            // Проверка наличия обязательных колонок
            $requiredColumns = ['Название организации', 'Latitude', 'Longitude', 'ID','Адрес'];
            foreach ($requiredColumns as $col) {
                if (!isset($columns[$col])) {
                    continue 2;
                }
            }

            foreach ($crematoriumsData as $rowIndex => $crematoriumRow) {
                try {
                    // Проверка обязательных полей
                    if (empty($crematoriumRow[$columns['Название организации']])) {
                        $skippedRows++;
                        continue;
                    }

                    if (empty($crematoriumRow[$columns['ID']])) {
                        $skippedRows++;
                        continue;
                    }

                     if (empty($crematoriumRow[$columns['Адрес']])) {
                        $skippedRows++;
                        continue;
                    }
                    // Проверка координат
                    if (empty($crematoriumRow[$columns['Latitude']])) {
                        $skippedRows++;
                        continue;
                    }

                    if (empty($crematoriumRow[$columns['Longitude']])) {
                        $skippedRows++;
                        continue;
                    }
                    $objects = linkRegionDistrictCity(
                        $crematoriumRow[$columns['Регион'] ?? null],
                        $crematoriumRow[$columns['Район'] ?? null],
                        $crematoriumRow[$columns['Населённый пункт'] ?? null]
                    );
                    
                    $area = $objects['district'] ?? null;
                    $city = $objects['city'] ?? null;

                    if (!$city || !$area) {
                        $skippedRows++;
                        continue;
                    }

                    $objectId = rtrim($crematoriumRow[$columns['ID']] ?? '', '!');

                    $time_difference = $city->utc_offset ?? null;
                    if($time_difference==null && env('API_WORK')=='true'){
                        $time_difference=differencetHoursTimezone(getTimeByCoordinates($crematoriumRow[$columns['Latitude']],$crematoriumRow[$columns['Longitude']])['timezone']);
                        $city->update(['utc_offset'=> $time_difference]);
                    }

                    $crematoriumData = [
                        'id' => $objectId,
                        'title' => $crematoriumRow[$columns['Название организации']],
                        'adres' => $crematoriumRow[$columns['Адрес']],
                        'width' => $crematoriumRow[$columns['Latitude']],
                        'longitude' => $crematoriumRow[$columns['Longitude']],
                        'city_id' => $city->id,
                        'phone' => normalizePhone($crematoriumRow[$columns['Телефоны'] ?? null]),
                        'content'=>$crematoriumRow[$columns['SEO Описание']] ?? $crematoriumRow[$columns['Описание']]  ,
                        'img_url' => $crematoriumRow[$columns['Логотип']] ?? 'default',
                        'href_img' => 1,
                        'rating'=>$crematoriumRow[$columns['Рейтинг']],
                        'two_gis_link'=> $crematoriumRow[$columns['URL']]  ?? null,
                        'time_difference' => $time_difference,
                        'url_site' => $crematoriumRow[$columns['Сайт'] ?? null] ?? null,
                    ];


                    
                    if($crematoriumRow[$columns['Логотип']]!='default') {
                        if($crematoriumRow[$columns['Логотип']]!=null && !isBrokenLink($crematoriumRow[$columns['Логотип']])){
                            $mortuaryData['img_url'] = $crematoriumRow[$columns['Логотип']];
                        }else{
                            $mortuaryData['img_url'] = 'default';
                        }
                    }

                    if ($importAction === 'create' && Crematorium::find($objectId)==null) {
                        $crematorium = Crematorium::create($crematoriumData);

                        $createdCrematoriums++;

                        // Обработка режима работы при создании
                        if(isset($columns['Режим работы']) && $crematoriumRow[$columns['Режим работы']] != null) {
                            $workHours = $crematoriumRow[$columns['Режим работы']];
                            $days = parseWorkingHours($workHours);
                            
                            foreach($days as $day) {
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

                        if(isset($columns['Фотографии']) && $crematoriumRow[$columns['Фотографии']] != null) {
                            ImageCrematorium::where('crematorium_id', $crematorium->id)->delete();
                            
                            $urls_array = explode(', ', $crematoriumRow[$columns['Фотографии']]);
                            foreach($urls_array as $img) {
                                if($img!=null && !isBrokenLink($img)){
                                    ImageCrematorium::create([
                                        'img_url' => $img,
                                        'href_img' => 1,
                                        'crematorium_id' => $crematorium->id,
                                    ]);
                                }
                            }
                        }


                    } elseif ($importAction === 'update') {
                        $crematorium = Crematorium::find($objectId);
                        
                        if ($crematorium) {
                            $updateData = [];
                            foreach ($updateFields as $field) {
                                if (isset($crematoriumData[$field])) {
                                    $updateData[$field] = $crematoriumData[$field];
                                }
                            }
                            
                            if (!empty($updateData)) {
                                $crematorium->update($updateData);
                                $updatedCrematoriums++;
                            }

                            // Обработка режима работы при обновлении
                            if(in_array('working_hours', $updateFields) && isset($columns['Режим работы'])) {
                                $workHours = $crematoriumRow[$columns['Режим работы']] ?? null;
                                if($workHours) {
                                    // Удаляем старые записи о рабочем времени
                                    WorkingHoursCrematorium::where('crematorium_id', $crematorium->id)->delete();
                                    
                                    // Создаем новые записи
                                    $days = parseWorkingHours($workHours);
                                    foreach($days as $day) {
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
                            }
                            if(in_array('galerey', $updateFields) && isset($columns['Фотографии'])) {
                                ImageCrematorium::where('crematorium_id', $crematorium->id)->delete();
                                
                                $urls_array = explode(', ', $crematoriumRow[$columns['Фотографии']]);
                                foreach($urls_array as $img) {
                                    if($img!=null && !isBrokenLink($img)){
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