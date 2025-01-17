<?php

namespace App\Services\Parser;

use App\Models\Cemetery;
use App\Models\City;
use App\Models\Columbarium;
use App\Models\Edge;
use App\Models\ImageCrematorium;
use App\Models\ReviewColumbarium;
use App\Models\WorkingHoursColumbarium;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ParserColumbariumService
{
    public static function index($request){
        $spreadsheet = new Spreadsheet();
        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file);
        // Получение данных из первого листа
        $sheet = $spreadsheet->getActiveSheet();
        $columbariums = array_slice($sheet->toArray(),1);
        foreach($columbariums as $columbarium){
            $city=createCity($columbarium[7],$columbarium[6]);
            if($city!=null && $columbarium[12]!=null && $columbarium[13]!=null){
                $content=$columbarium[31];
                $timezone=getTimeByCoordinates($columbarium[12],$columbarium[13])['timezone'];                
                $columbarium_create=Columbarium::create([
                    'title'=>$columbarium[3],
                    'village'=>$columbarium[8],
                    'adres'=>$columbarium[10],
                    'width'=>$columbarium[12],
                    'longitude'=>$columbarium[13],
                    'phone'=>phoneImport($columbarium[15]),
                    'email'=>$columbarium[19],
                    'img'=>$columbarium[34],
                    'city_id'=>$city->id,
                    'rating'=>$columbarium[26],
                    'mini_content'=>$columbarium[31],
                    'href_img'=>1,
                    'content'=>$content,
                    'time_difference'=>differencetHoursTimezone($timezone),
                ]);
                if($columbarium[35]!=null){
                    $imgs=explode(',',$columbarium[35]);
                    foreach($imgs as $img){
                        ImageCrematorium::create([
                            'title'=>$img,
                            'href_img'=>1,
                            'columbarium_id'=>$columbarium_create->id,
                        ]);
                    }
                }
                if($columbarium[17]!=null){
                    $worktime=explode(',',$columbarium[17]);
                    foreach($worktime as $days){
                        $days=parseWorkingHours($days);
                        foreach($days as $day){
                            $holiday=0;
                            if($day['time_start_work']=='Выходной'){
                                $holiday=1;
                            }
                            WorkingHoursColumbarium::create([
                                'day'=>$day['day'],
                                'time_start_work'=>$day['time_start_work'],
                                'time_end_work'=>$day['time_end_work'],
                                'holiday'=>$holiday,
                                'columbarium_id'=>$columbarium_create->id,
                            ]);
                        }

                    }
                }
            }
        }
        return redirect()->back()->with("message_cart", 'Колумабрии успешно добавлены');
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