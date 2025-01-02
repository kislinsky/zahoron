<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Beautification\BeautificationService;

class BeautificationController extends Controller
{
    public static function sendBeautification(Request $request){
        $data=request()->validate([
            'products_beautification'=>['required'],
            'name_beautification'=>['required','string'],
            'phone_beautification'=>['required','string'],
            'city_beautification'=>['required','integer'],
            'cemetery_beautification'=>['required','integer'],
            'burial_id_beautification'=>['nullable'],
            'aplication'=>['required'],
            'call_time'=>['nullable'],
            'call_tomorrow'=>['nullable'],
            'time_now'=>['required',]
        ]);
        return BeautificationService::sendBeautification($data);
    }
}
