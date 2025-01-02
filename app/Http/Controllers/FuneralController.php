<?php

namespace App\Http\Controllers;

use App\Models\FuneralService;
use App\Services\FuneralService\FuneralServiceService;
use Illuminate\Http\Request;

class FuneralController extends Controller
{
    public static function funeralServiceAdd(Request $request){
        if($request['funeral_service']==1){
            $data=request()->validate([
                'funeral_service'=>['required','integer'],
                'city_funeral_service'=>['required','string'],
                'city_funeral_service_to'=>['nullable','integer'],
                'mortuary_funeral_service'=>['nullable','integer'],
                'status_death_people_funeral_service'=>['required','string'],
                'civilian_status_people_funeral_service'=>['required','string'],
                'funeral_service_church'=>['nullable'],
                'farewell_hall'=>['nullable'],
                'name_funeral_service'=>['required','string'],
                'phone_funeral_service'=>['required','string'],
                'call_time'=>['nullable'],
                'call_tomorrow'=>['nullable'],
                'time_now'=>['required',]

            ]);
            return FuneralServiceService::addFuneralService($data);
        }
        if($request['funeral_service']==2){
             $data=request()->validate([
                'funeral_service'=>['required','integer'],
                'none_mortuary'=>['nullable'],
                'city_funeral_service'=>['required','string'],
                'mortuary_funeral_service'=>['nullable','integer'],
                'status_death_people_funeral_service'=>['required','string'],
                'civilian_status_people_funeral_service'=>['required','string'],
                'funeral_service_church'=>['nullable'],
                'farewell_hall'=>['nullable'],
                'name_funeral_service'=>['required','string'],
                'phone_funeral_service'=>['required','string'],
                'call_time'=>['nullable'],
                'call_tomorrow'=>['nullable'],
                'time_now'=>['required',]

            ]);
            return FuneralServiceService::addFuneralService($data);
        
        }
        if($request['funeral_service']==3){
            $data=request()->validate([ 
                'funeral_service'=>['required','integer'],
                'city_funeral_service'=>['required','string'],
                'cemetery_funeral_service'=>['required','integer'],
                'mortuary_funeral_service'=>['nullable','integer'],
                'status_death_people_funeral_service'=>['required','string'],
                'civilian_status_people_funeral_service'=>['required','string'],
                'funeral_service_church'=>['nullable'],
                'farewell_hall'=>['nullable'],
                'name_funeral_service'=>['required','string'],
                'phone_funeral_service'=>['required','string'],
                'call_time'=>['nullable'],
                'call_tomorrow'=>['nullable'],
                'time_now'=>['required',]

            ]);
            return FuneralServiceService::addFuneralService($data);
        }
    }

    public static function ajaxMortuary(Request $request){
        $data=request()->validate([ 
            'city_id'=>['required','integer'],
        ]);
        return FuneralServiceService::ajaxMortuary($data['city_id']);
    }

    public static function ajaxCemetery(Request $request){
        $data=request()->validate([ 
            'city_id'=>['required','integer'],
        ]);
        return FuneralServiceService::ajaxCemetery($data['city_id']);
    }
        


}
