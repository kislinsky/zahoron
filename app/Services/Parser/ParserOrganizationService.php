<?php

namespace App\Services\Parser;

use App\Models\ActivityCategoryOrganization;
use App\Models\CategoryProduct;
use App\Models\Cemetery;
use App\Models\City;
use App\Models\Edge;
use App\Models\ImageOrganization;
use App\Models\Organization;
use App\Models\ReviewsOrganization;
use App\Models\WorkingHoursOrganization;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ParserOrganizationService
{
    public static function index($request){
        $spreadsheet = new Spreadsheet();
        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file);
        // Получение данных из первого листа
        $sheet = $spreadsheet->getActiveSheet();
        $organizations = array_slice($sheet->toArray(),1);
        foreach($organizations as $organization){
            $city=createCity($organization[7],$organization[5]);
            $distrcit=createDistrict($organization[6],$organization[7]);
            
            if($city!=null){
                $ids_cemeteries=Cemetery::where('city_id',$city->id);
                if($ids_cemeteries!=null){
                    $ids_cemeteries=$ids_cemeteries->pluck('id');
                }
                $cemeteries='';
                $organization_find=Organization::find(rtrim($organization[1], '!'));
                if($organization_find==null){
                    $timezone=getTimeByCoordinates($organization[2],$organization[3])['timezone'];
                    $organization_create=Organization::create([
                        'id'=>rtrim($organization[1], '!'),
                        'title'=>$organization[9],
                        'adres'=>$organization[8],
                        'width'=>$organization[2],
                        'longitude'=>$organization[3],
                        'phone'=>phoneImport($organization[18]),
                        'email'=>trim(trim($organization[11], '('),')'),
                        'logo'=>$organization[20],
                        'city_id'=>$city->id,
                        'rating'=>$organization[15],
                        'href_img'=>1,
                        'slug'=>slugOrganization($organization[9]),
                        'cemetery_ids'=>$cemeteries,
                        'name_type'=>$organization[10],
                        'district_id'=>$distrcit,
                        'time_difference'=>differencetHoursTimezone($timezone),
                    ]);
                    if($organization[21]!=null){
                        $imgs=preg_match_all('/\((.*?)\)/', $organization[21],$matches);
                        $urls_array = $matches[1];
                        foreach($urls_array as $img){
                            ImageOrganization::create([
                                'title'=>$img,
                                'href_img'=>1,
                                'organization_id'=>$organization_create->id,
                            ]);
                        }
                    }
                    if($organization[22]!=null){
                        $worktime=explode(',',$organization[22]);
                        foreach($worktime as $days){
                            $days=parseWorkingHours($days);
        
                            foreach($days as $day){
                                $holiday=0;
                                if($day['time_start_work']=='Выходной'){
                                    $holiday=1;
                                }
                                WorkingHoursOrganization::create([
                                    'day'=>$day['day'],
                                    'time_start_work'=>$day['time_start_work'],
                                    'time_end_work'=>$day['time_end_work'],
                                    'holiday'=>$holiday,
                                    'organization_id'=>$organization_create->id,
                                ]);
                            }
        
                        }
                    }
                }
            } 
        }

        return redirect()->back()->with("message_cart", 'Организации успешно добавлены');
       
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
                $organization=Organization::find($review[0]);
                if($cities!=null && $organization!=null){
                    ReviewsOrganization::create([
                        'name'=>$review[4],
                        'rating'=>$review[6],
                        'content'=>$review[7],
                        'created_at'=>$review[5],
                        'organization_id'=>$organization->id,
                        'status'=>1,
                        'city_id'=>$organization->city->id,
                        
                    ]);
                    $organization->updateRating();
                }
            }
        }
        return redirect()->back()->with("message_cart", 'Отзывы успешно добавлены');

    }


    public static function importPrices($request){
        $spreadsheet = new Spreadsheet();
        $file = $request->file('file_prices');
        $spreadsheet = IOFactory::load($file);
        // Получение данных из первого листа
        $sheet = $spreadsheet->getActiveSheet();
        $prices = array_slice($sheet->toArray(),1);
        foreach($prices as $price){
                $city=City::where('title',$price[8])->first();
                if($city!=null){
                    $organization=Organization::where('title',$price[3])->where('city_id',$city->id)->first();
                    $category_product=CategoryProduct::where('title',$price[5])->first();
                    if($organization!=null && $category_product!=null){
                        $active_category=ActivityCategoryOrganization::where('organization_id',$organization->id)->where('category_children_id',$category_product->id)->where('category_main_id',$category_product->parent_id)->first();
                        if( $organization!=null && $category_product!=null){
                            if($active_category==null){
                                ActivityCategoryOrganization::create([
                                    'rating'=>$price[6],
                                    'category_children_id'=>$category_product->id,
                                    'category_main_id'=>$category_product->parent_id,
                                    'organization_id'=>$organization->id,
                                    'city_id'=>$organization->city->id,
                                    'role'=>'organization',
                                    'cemetery_ids'=>$organization->cemetery_ids,
                                    'price'=>0,
                                    'rating'=>$organization->rating,
                                ]);
                            }
                        }
                    }
                   
                }            
        }
        return redirect()->back()->with("message_cart", 'Цены успешно добавлены');

    }

}