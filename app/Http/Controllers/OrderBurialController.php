<?php

namespace App\Http\Controllers;

use App\Rules\RecaptchaRule;
use App\Services\Order\OrderBurialService;
use Illuminate\Http\Request;

class OrderBurialController extends Controller
{
    public static function orderAdd(Request $request){
        $data=request()->validate([
            'g-recaptcha-response' => ['required', new RecaptchaRule],
            'name'=>['required','string'],
            'surname'=>['required','string'],
            'phone'=>['required','string'],
            'message'=>['string','nullable'],
            'choose_pay'=>['required'],
            'aplication'=>['required'],
        ]);
        return OrderBurialService::orderAdd($data);
    }
}
