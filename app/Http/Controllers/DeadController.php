<?php

namespace App\Http\Controllers;

use App\Services\Dead\DeadService;
use Illuminate\Http\Request;

class DeadController extends Controller
{
    public static function deadAdd(Request $request){
        $data=request()->validate([
         'city_dead'=>['required','integer'],
         'none_mortuary'=>['nullable'],
         'mortuary_dead'=>['nullable','string'],
         'fio_dead'=>['required','string'],
         'name_dead'=>['required','string'],
         'phone_dead'=>['required','integer'],
         'call_time'=>['nullable'],
         'call_tomorrow'=>['nullable'],
         'time_now'=>['required',]

        ]);
        
        return DeadService::deadAdd($data);
    }
}
