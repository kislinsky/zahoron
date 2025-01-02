<?php

namespace App\Services\Parser;

use App\Models\Cemetery;
use App\Models\City;

use App\Models\Edge;
use App\Models\ImageMortuary;
use App\Models\Mortuary;
use App\Models\ReviewMortuary;
use App\Models\WorkingHoursMortuary;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ParserMortuaryService
{
    public static function index($request){
        $spreadsheet = new Spreadsheet();
        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file);
        // Получение данных из первого листа
        $sheet = $spreadsheet->getActiveSheet();
        $mortuaries = array_slice($sheet->toArray(),1);
        foreach($mortuaries as $mortuary){
            $city=createCity($mortuary[7],$mortuary[6]);
            if($city!=null && $mortuary[12]!=null && $mortuary[13]!=null){
                $content=$mortuary[38];
                if($content==null){
                    $content=$mortuary[31];
                }
                $mortuary_create=Mortuary::create([
                    'title'=>$mortuary[3],
                    'village'=>$mortuary[8],
                    'adres'=>$mortuary[10],
                    'width'=>$mortuary[12],
                    'longitude'=>$mortuary[13],
                    'phone'=>phoneImport($mortuary[15]),
                    'email'=>$mortuary[19],
                    'img'=>$mortuary[34],
                    'city_id'=>$city->id,
                    'rating'=>$mortuary[26],
                    'mini_content'=>$mortuary[31],
                    'href_img'=>1,
                    'content'=>$content
                ]);
                if($mortuary[35]!=null){
                    $imgs=explode(',',$mortuary[35]);
                    foreach($imgs as $img){
                        ImageMortuary::create([
                            'title'=>$img,
                            'href_img'=>1,
                            'mortuary_id'=>$mortuary_create->id,
                        ]);
                    }
                }
                if($mortuary[17]!=null){
                    $worktime=explode(',',$mortuary[17]);
                    foreach($worktime as $days){
                        $days=parseWorkingHours($days);
                        foreach($days as $day){
                            $holiday=0;
                            if($day['time_start_work']=='Выходной'){
                                $holiday=1;
                            }
                            WorkingHoursMortuary::create([
                                'day'=>$day['day'],
                                'time_start_work'=>$day['time_start_work'],
                                'time_end_work'=>$day['time_end_work'],
                                'holiday'=>$holiday,
                                'mortuary_id'=>$mortuary_create->id,
                            ]);
                        }

                    }
                }
            }
        }
        return redirect()->back()->with("message_cart", 'Морги успешно добавлены');
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