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


            $city=createArea($organization[8],$organization[7]);
            $city=createCity($organization[9],$organization[7]);
            
            if($city!=null || $organization[14]==null){
                $ids_cemeteries=Cemetery::where('city_id',$city->id);
                if($ids_cemeteries!=null){
                    $ids_cemeteries=$ids_cemeteries->pluck('id');
                }
                $cemeteries='';
                $organization_find=Organization::find(rtrim($organization[1], '!'));
                if($organization_find==null){
                $time_difference=differencetHoursTimezone(getTimeByCoordinates($organization[12],$organization[13])['timezone']);
                $img_url=$organization[22];
                if($img_url==null){
                    $img_url='https://ams2-cdn.2gis.com/previews/1113196871553122307/62479f56-4baf-46f1-8439-e32a2f053ceb/3/656x340?api-version=2.0';
                }
                $img_main_url=$organization[23];
                if($img_main_url==null){
                    $img_main_url='https://ams2-cdn.2gis.com/previews/1113196871553122307/62479f56-4baf-46f1-8439-e32a2f053ceb/3/656x340?api-version=2.0';
                }
                $organization_create=Organization::create([
                        'id'=>rtrim($organization[1], '!'),
                        'title'=>$organization[4],
                        'adres'=>$organization[10],
                        'nearby'=>$organization[11],
                        'width'=>$organization[12],
                        'longitude'=>$organization[13],
                        'phone'=>phoneImport($organization[14]),
                        'email'=>trim(trim($organization[17], '('),')'),
                        'img_url'=>$img_url,
                        'content'=>$organization[25],
                        'city_id'=>$city->id,
                        'rating'=>$organization[3],
                        'href_img'=>1,
                        'href_main_img'=>1,
                        'img_main_url'=>$img_main_url,
                        'slug'=>slugOrganization($organization[4]),
                        'cemetery_ids'=>$cemeteries,
                        'name_type'=>$organization[5],
                        'time_difference'=>$time_difference,
                        'whatsapp'=>$organization[18],
                        'telegram'=>$organization[19],
                      
                    ]);
                    $area = $organization_create->city->area;

                    if($area!=null){
                        $cemeteries = implode(',',$area->cities->flatMap(function ($city) {
                            return $city->cemeteries->pluck('id');
                        })->unique()->toArray()).','; // Убираем дубликаты
                        $organization_create->update([
                            'cemetery_ids' => $cemeteries
                        ]);
                    }
                    

                    if($organization[24]!=null){
                        // $imgs=preg_match_all('/\((.*?)\)/', $organization[24],$matches);

                        $urls_array = explode(', ', $organization[24]);
                        // $urls_array = $matches[1];
                        foreach($urls_array as $img){
                            ImageOrganization::create([
                                'img_url'=>$img,
                                'href_img'=>1,
                                'organization_id'=>$organization_create->id,
                            ]);
                        }
                    }
                    if($organization[15]!=null){
                        $days=$organization[15];
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

                    if($organization[6]!=null){
                        addActiveCategory($organization[6],['Кнопка могил'],$organization_create);
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