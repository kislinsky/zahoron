<?php

namespace App\Services\Parser;

use App\Models\Cemetery;
use App\Models\City;
use App\Models\Columbarium;
use App\Models\Crematorium;
use App\Models\Edge;
use App\Models\ImageCemetery;
use App\Models\ImageCrematorium;
use App\Models\ImageMortuary;
use App\Models\Mortuary;
use App\Models\ReviewCemetery;
use App\Models\ServiceCemetery;
use App\Models\ServiceMortuary;
use App\Models\WorkingHoursCemetery;
use App\Models\WorkingHoursColumbarium;
use App\Models\WorkingHoursCrematorium;
use App\Models\WorkingHoursMortuary;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ParserCemeteryService
{
    public static function index($request){
        $spreadsheet = new Spreadsheet();
        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file);
        // Получение данных из первого листа
        $sheet = $spreadsheet->getActiveSheet();
        $cemeteries = array_slice($sheet->toArray(),1);
        foreach($cemeteries as $cemetery){
            $city=createCity($cemetery[7],$cemetery[6]);
            if($city!=null && $cemetery[12]!=null && $cemetery[13]!=null){
                $content=$cemetery[37];
                if($content==null){
                    $content=$cemetery[31];
                }
                $cemetery_create=Cemetery::create([
                    'title'=>$cemetery[3],
                    'village'=>$cemetery[8],
                    'adres'=>$cemetery[10],
                    'width'=>$cemetery[12],
                    'longitude'=>$cemetery[13],
                    'phone'=>phoneImport($cemetery[15]),
                    'email'=>$cemetery[19],
                    'img'=>$cemetery[34],
                    'city_id'=>$city->id,
                    'rating'=>$cemetery[26],
                    'mini_content'=>$cemetery[31],
                    'href_img'=>1,
                    'content'=>$content
                ]);
                if($cemetery[35]!=null){
                    $imgs=explode(',',$cemetery[35]);
                    foreach($imgs as $img){
                        ImageCemetery::create([
                            'title'=>$img,
                            'href_img'=>1,
                            'cemetery_id'=>$cemetery_create->id,
                        ]);
                    }
                }
                if($cemetery[17]!=null){
                    $worktime=explode(',',$cemetery[17]);
                    foreach($worktime as $days){
                        $days=parseWorkingHours($days);

                        foreach($days as $day){
                            $holiday=0;
                            if($day['time_start_work']=='Выходной'){
                                $holiday=1;
                            }
                            WorkingHoursCemetery::create([
                                'day'=>$day['day'],
                                'time_start_work'=>$day['time_start_work'],
                                'time_end_work'=>$day['time_end_work'],
                                'holiday'=>$holiday,
                                'cemetery_id'=>$cemetery_create->id,
                            ]);
                        }

                    }
                }

                // if($cemetery[37]!=null){
                //     $services=extractServiceNames($cemetery[37]);
                //     foreach($services as $service){
                //         ServiceCemetery::create([
                //             'title'=>$service,
                //             'cemetery_id'=>$cemetery_create->id,
                //         ]);
                //     }
                // }
            }
        }
        return redirect()->back()->with("message_cart", 'Кладбища успешно добавлены');
       
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