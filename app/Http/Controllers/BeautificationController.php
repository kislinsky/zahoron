<?php

namespace App\Http\Controllers;

use App\Rules\RecaptchaRule;
use App\Services\Beautification\BeautificationService;
use Illuminate\Http\Request;

class BeautificationController extends Controller
{
    public static function sendBeautification(Request $request){
        $data=request()->validate([
            'products_beautification'=>['required'],
            'g-recaptcha-response' => ['required', new RecaptchaRule],
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
