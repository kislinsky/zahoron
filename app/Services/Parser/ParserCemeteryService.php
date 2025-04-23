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
    // public static function index($request){
    //     $spreadsheet = new Spreadsheet();
    //     $file = $request->file('file');
    //     $spreadsheet = IOFactory::load($file);
    //     // Получение данных из первого листа
    //     $sheet = $spreadsheet->getActiveSheet();
    //     $cemeteries = array_slice($sheet->toArray(),1);
    //     foreach($cemeteries as $cemetery){
    //         $city=createCity($cemetery[7],$cemetery[6]);
    //         if($city!=null && $cemetery[12]!=null && $cemetery[13]!=null){
    //             $content=$cemetery[37];
    //             if($content==null){
    //                 $content=$cemetery[31];
    //             }
    //             $cemetery_create=Cemetery::create([
    //                 'title'=>$cemetery[3],
    //                 'village'=>$cemetery[8],
    //                 'adres'=>$cemetery[10],
    //                 'width'=>$cemetery[12],
    //                 'longitude'=>$cemetery[13],
    //                 'phone'=>normalizePhone($cemetery[15]),
    //                 'email'=>$cemetery[19],
    //                 'img'=>$cemetery[34],
    //                 'city_id'=>$city->id,
    //                 'rating'=>$cemetery[26],
    //                 'mini_content'=>$cemetery[31],
    //                 'href_img'=>1,
    //                 'content'=>$content
    //             ]);
    //             if($cemetery[35]!=null){
    //                 $imgs=explode(',',$cemetery[35]);
    //                 foreach($imgs as $img){
    //                     ImageCemetery::create([
    //                         'title'=>$img,
    //                         'href_img'=>1,
    //                         'cemetery_id'=>$cemetery_create->id,
    //                     ]);
    //                 }
    //             }
    //             if($cemetery[17]!=null){
    //                 $worktime=explode(',',$cemetery[17]);
    //                 foreach($worktime as $days){
    //                     $days=parseWorkingHours($days);

    //                     foreach($days as $day){
    //                         $holiday=0;
    //                         if($day['time_start_work']=='Выходной'){
    //                             $holiday=1;
    //                         }
    //                         WorkingHoursCemetery::create([
    //                             'day'=>$day['day'],
    //                             'time_start_work'=>$day['time_start_work'],
    //                             'time_end_work'=>$day['time_end_work'],
    //                             'holiday'=>$holiday,
    //                             'cemetery_id'=>$cemetery_create->id,
    //                         ]);
    //                     }

    //                 }
    //             }

    //             // if($cemetery[37]!=null){
    //             //     $services=extractServiceNames($cemetery[37]);
    //             //     foreach($services as $service){
    //             //         ServiceCemetery::create([
    //             //             'title'=>$service,
    //             //             'cemetery_id'=>$cemetery_create->id,
    //             //         ]);
    //             //     }
    //             // }
    //         }
    //     }
    //     return redirect()->back()->with("message_cart", 'Кладбища успешно добавлены');
       
    // }




    public static function index($request) {
        $files = $request->file('files');
        $price = $request->input('price_geo');
        $importAction = $request->input('import_type', 'create');
        $updateFields = $request->input('columns_to_update', []);
        
        $processedFiles = 0;
        $createdCemeteries = 0;
        $updatedCemeteries = 0;
        $skippedRows = 0;
    
        foreach ($files as $file) {
            if (!$file->isValid()) {
                continue;
            }
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $titles = $sheet->toArray()[0];
            $cemeteriesData = array_slice($sheet->toArray(), 1);
            
            $columns = array_flip($titles);

            foreach ($cemeteriesData as $cemeteryRow) {
                
                // Проверка обязательных полей
                if (empty($cemeteryRow[$columns['Наименование кладбища'] ?? null]) || 
                    empty($cemeteryRow[$columns['Latitude'] ?? null]) || 
                    empty($cemeteryRow[$columns['Longitude'] ?? null])) {
                    $skippedRows++;
                    continue;
                }


                $objects=linkRegionDistrictCity($cemeteryRow[$columns['Край/Область'] ?? null],$cemeteryRow[$columns['Муниципального округа'] ?? null],$cemeteryRow[$columns['Населенный пункт'] ?? null],);


                $area = $objects['district'];
                $city =  $objects['city'];

    
                if (!$city || !$area) {

                    $skippedRows++;
                    continue;
                }

                $status=1;
                if($cemeteryRow[$columns['Статус кладбища']]=='Открыто'){
                    $status=1;
                }else{
                    $status=0;
                }
                $cemeteryData = [
                    'title' => $cemeteryRow[$columns['Наименование кладбища']],
                    'adres' => $cemeteryRow[$columns['Ориентир'] ?? null],
                    'width' => $cemeteryRow[$columns['Latitude']],
                    'longitude' => $cemeteryRow[$columns['Longitude']],
                    'city_id' => $city->id,
                    'area_id' => $area->id,
                    'phone' => normalizePhone($cemeteryRow[$columns['Тел. Ответственного'] ?? null]),
                    'square' => $cemeteryRow[$columns['Общая площадь (га)'] ?? null],
                    'responsible' => $cemeteryRow[$columns['Ответственный'] ?? null],
                    'cadastral_number' => $cemeteryRow[$columns['кадастровый номер'] ?? null],
                    'status' => $status,
                    // Дефолтные значения
                    'img_url' => 'https://api.selcdn.ru/v1/SEL_266534/Images/main/Petropavlovsk-Kamchatsky/Cemeteries/70000001057067323!/Funeral-Services.jpg',
                    'href_img' => 1,
                    'price_burial_location'=>$price ?? null,
                    'time_difference' => 10,
                ];
    
                if ($importAction === 'create') {

                    $cemetery = Cemetery::create($cemeteryData);

                    $createdCemeteries++;
                } elseif ($importAction === 'update') {
                    $identifier =  $cemeteryData['title'];
                    
                    $cemetery = Cemetery::where('title', $identifier)
                        ->first();
                    
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
                    } else {
                        $skippedRows++;
                    }
                }
            }
            
            $processedFiles++;
        }
    
        $message = "Импорт кладбищ завершен. " .
                   "Файлов обработано: $processedFiles, " .
                   "Создано кладбищ: $createdCemeteries, " .
                   "Обновлено кладбищ: $updatedCemeteries, " .
                   "Пропущено строк: $skippedRows";
    
        return redirect()->back()->with("message_cart", $message);
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
                    ReviewCemetery::create([
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