<?php

namespace App\Http\Controllers;

use App\Rules\RecaptchaRule;
use App\Services\Memorial\MemorialService;
use Illuminate\Http\Request;
use Nutnet\LaravelSms\Providers\SmsRu;

class MemorialController extends Controller
{
    public static function memorialAdd(Request $request){
        $data=request()->validate([
        'g-recaptcha-response' => ['required', new RecaptchaRule],
         'city_memorial'=>['required','integer'],
         'district_memorial'=>['required','integer'],
         'date_memorial'=>['required','date'],
         'time_memorial'=>['required','string'],
         'count_people'=>['required','integer'],
         'name_memorial'=>['required','string'],
         'phone_memorial'=>['required','string'],
         'count_time'=>['required','integer'],
         'call_time'=>['nullable'],
         'call_tomorrow'=>['nullable'],
         'time_now'=>['required',]
        ]);
        return MemorialService::memorialAdd($data);
    }
}
