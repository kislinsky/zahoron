<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\City\CityService;

class CityController extends Controller
{
    public static function selectCity($id){
        return CityService::selectCity($id);
    }
    
    public static function ajaxCity(Request $request){
        $data=request()->validate([
            'city_id'=>['required','string'],
        ]);
        return CityService::ajaxCity($data['city_id']);
    }

    public static function ajaxCityFromEdge(Request $request){
        $data=request()->validate([
            'edge_id'=>['required','string'],
        ]);
        return CityService::ajaxCityFromEdge($data['edge_id']);
    }

    public static function ajaxCityInInput(Request $request){
        $data=request()->validate([
            's'=>['required','string'],
        ]);
        return CityService::ajaxCityInInput($data['s']);
    }


    public static function ajaxCitySearchInInput(Request $request){
        $data=request()->validate([
            's'=>['nullable','string'],
        ]);
        return CityService::ajaxCitySearchInInput($data);
    }

}
