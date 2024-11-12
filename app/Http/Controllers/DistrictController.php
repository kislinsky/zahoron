<?php

namespace App\Http\Controllers;

use App\Services\District\DistrictService;
use Illuminate\Http\Request;

class DistrictController extends Controller
{
    public static function ajaxDistrict(Request $request){
        $data=request()->validate([
            'city_id'=>['required'],
        ]);
        return DistrictService::ajaxDistrict($data['city_id']);
    }
}
