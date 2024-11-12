<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Order\OrderBurialService;

class OrderBurialController extends Controller
{
    public static function orderAdd(Request $request){
        $data=request()->validate([
            'name'=>['required','string'],
            'surname'=>['required','string'],
            'phone'=>['required','string'],
            'email'=>['required','email'],
            'message'=>['string','nullable'],
            'choose_pay'=>['required'],
            'aplication'=>['required'],
        ]);
        return OrderBurialService::orderAdd($data);
    }
}
