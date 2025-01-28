<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Order\OrderServiceService;

class OrderServiceController extends Controller
{
    public static function orderAdd(Request $request){
        $data=request()->validate([
            'name'=>['required','string'],
            'surname'=>['required','string'],
            'phone'=>['required','string'],
            'message'=>['string','nullable'],
            'choose_pay'=>['required'],
            'aplication'=>['required'],
        ]);
        return OrderServiceService::orderAdd($data);
    }
}
