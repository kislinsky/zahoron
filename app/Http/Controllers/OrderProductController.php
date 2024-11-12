<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Order\OrderProductService;

class OrderProductController extends Controller
{
    public static function orderAdd(Request $request){
        $data=request()->validate([
            'name'=>['required','string'],
            'surname'=>['required','string'],
            'phone'=>['required','string'],
            'email'=>['required','email'],
            'message'=>['string','nullable'],
        ]);
        return OrderProductService::orderAdd($data);
    }

    public static function addOrderOne(Request $request){
        if($request['category_id']==32){
            $data=request()->validate([
                'name'=>['required','string'],
                'phone'=>['required','string'],
                'email'=>['required','email'],
                'message'=>['string','nullable'],
                'additionals'=>['nullable'],
                'cemetery_id'=>['required','integer'],
                'no_have_mortuary'=>['nullable'],
                'mortuary_id'=>['integer','nullable'],
                'product_id'=>['required','integer'],
            ]);

            return OrderProductService::addOrderOne($data);
        }
        if($request['category_id']==33){
            $data=request()->validate([
                'name'=>['required','string'],
                'phone'=>['required','string'],
                'email'=>['required','email'],
                'message'=>['string','nullable'],
                'additionals'=>['nullable'],
                'no_have_mortuary'=>['nullable'],
                'mortuary_id'=>['integer','nullable'],
                'product_id'=>['required','integer'],
            ]);

            return OrderProductService::addOrderOne($data);
        }
        if($request['category_id']==34){
            $data=request()->validate([
                'city_from'=>['required','string'],
                'city_to'=>['required','string'],
                'name'=>['required','string'],
                'phone'=>['required','string'],
                'email'=>['required','email'],
                'message'=>['string','nullable'],
                'additionals'=>['nullable'],
                'no_have_mortuary'=>['nullable'],
                'mortuary_id'=>['integer','nullable'],
                'product_id'=>['required','integer'],
            ]);

            return OrderProductService::addOrderOne($data);
        }
        if($request['category_id']==35){
            $data=request()->validate([
                'name'=>['required','string'],
                'phone'=>['required','string'],
                'email'=>['required','email'],
                'message'=>['string','nullable'],
                'additionals'=>['nullable'],
                'cemetery_id'=>['integer','required'],
                'product_id'=>['required','integer'],
            ]);

            return OrderProductService::addOrderOne($data);
        }
        
    }
    
}
