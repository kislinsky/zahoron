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
use App\Models\ReviewCrematorium;
use App\Models\ServiceCemetery;
use App\Models\ServiceMortuary;
use App\Models\WorkingHoursCemetery;
use App\Models\WorkingHoursColumbarium;
use App\Models\WorkingHoursCrematorium;
use App\Models\WorkingHoursMortuary;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ParserCrematoriumService
{
    public static function index($request){
        $spreadsheet = new Spreadsheet();
        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file);
        // Получение данных из первого листа
        $sheet = $spreadsheet->getActiveSheet();
        $crematoriums = array_slice($sheet->toArray(),1);
        foreach($crematoriums as $crematorium){
            $city=createCity($crematorium[7],$crematorium[6]);
            if($city!=null && $crematorium[12]!=null && $crematorium[13]!=null){
                $content=$crematorium[31];
                $crematorium_create=Crematorium::create([
                    'title'=>$crematorium[3],
                    'village'=>$crematorium[8],
                    'adres'=>$crematorium[10],
                    'width'=>$crematorium[12],
                    'longitude'=>$crematorium[13],
                    'phone'=>phoneImport($crematorium[15]),
                    'email'=>$crematorium[19],
                    'img'=>$crematorium[34],
                    'city_id'=>$city->id,
                    'rating'=>$crematorium[26],
                    'mini_content'=>$crematorium[31],
                    'href_img'=>1,
                    'content'=>$content
                ]);
                if($crematorium[35]!=null){
                    $imgs=explode(',',$crematorium[35]);
                    foreach($imgs as $img){
                        ImageCrematorium::create([
                            'title'=>$img,
                            'href_img'=>1,
                            'crematorium_id'=>$crematorium_create->id,
                        ]);
                    }
                }
                if($crematorium[17]!=null){
                    $worktime=explode(',',$crematorium[17]);
                    foreach($worktime as $days){
                        $days=parseWorkingHours($days);
                        foreach($days as $day){
                            $holiday=0;
                            if($day['time_start_work']=='Выходной'){
                                $holiday=1;
                            }
                            WorkingHoursCrematorium::create([
                                'day'=>$day['day'],
                                'time_start_work'=>$day['time_start_work'],
                                'time_end_work'=>$day['time_end_work'],
                                'holiday'=>$holiday,
                                'crematorium_id'=>$crematorium_create->id,
                            ]);
                        }

                    }
                }
            }
        }
        return redirect()->back()->with("message_cart", 'Крематории успешно добавлены');
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