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
}
