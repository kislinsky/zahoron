<?php

namespace App\Http\Controllers;

use App\Models\Burial;
use App\Models\OrderBurial;
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

    public static function orderAddWithPay(Burial $burial,Request $request){
        $orders=OrderBurial::where('burial_id',$burial->id)->where('user_id',user()->id)->get();
        if($orders->count()>0){
            return redirect()->back()->with('error','У вас уже есть эта геолокация в заказах аккаунта');
        }
        return OrderBurialService::orderAddWithPay($burial);
    }

    public static function sendCode(Request $request){
        return OrderBurialService::sendCode($request);
    }

    public static function verifyCode(Request $request){
        return OrderBurialService::verifyCode($request);
    }
    

}
