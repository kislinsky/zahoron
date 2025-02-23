<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Order\OrderProductService;
use App\Models\CategoryProduct;

class OrderProductController extends Controller
{
    public static function orderAdd(Request $request){
        $data=request()->validate([
            'name'=>['required','string'],
            'surname'=>['required','string'],
            'phone'=>['required','string'],
            
            'message'=>['string','nullable'],
        ]);
        return OrderProductService::orderAdd($data);
    }

    public static function addOrderOne(Request $request){
        $category=CategoryProduct::find($request['category_id']);

        if($category->slug=='organizacia-pohoron'){
            $data=request()->validate([
                'name'=>['required','string'],
                'phone'=>['required','string'], 
                'message'=>['string','nullable'],
                'additionals'=>['nullable'],
                'cemetery_id'=>['required','integer'],
                'no_have_mortuary'=>['nullable'],
                'mortuary_id'=>['integer','nullable'],
                'product_id'=>['required','integer'],
            ]);

            return OrderProductService::addOrderOne($data);
        }
        if($category->slug=='organizacia-kremacii'){
            $data=request()->validate([
                'name'=>['required','string'],
                'phone'=>['required','string'], 
                'message'=>['string','nullable'],
                'additionals'=>['nullable'],
                'no_have_mortuary'=>['nullable'],
                'mortuary_id'=>['integer','nullable'],
                'product_id'=>['required','integer'],
            ]);

            return OrderProductService::addOrderOne($data);
        }
        if($category->slug=='otpravka-gruz-200'){
            $data=request()->validate([
                'city_from'=>['required','string'],
                'city_to'=>['required','string'],
                'name'=>['required','string'],
                'phone'=>['required','string'],    
                'message'=>['string','nullable'],
                'additionals'=>['nullable'],
                'no_have_mortuary'=>['nullable'],
                'mortuary_id'=>['integer','nullable'],
                'product_id'=>['required','integer'],
            ]);

            return OrderProductService::addOrderOne($data);
        }
        if($category->slug=='kopka-mogil'){
            $data=request()->validate([
                'name'=>['required','string'],
                'phone'=>['required','string'],
                'message'=>['string','nullable'],
                'additionals'=>['nullable'],
                'cemetery_id'=>['integer','required'],
                'product_id'=>['required','integer'],
            ]);

            return OrderProductService::addOrderOne($data);
        }
        $data=request()->validate([
            'name'=>['required','string'],
            'phone'=>['required','string'],
            'message'=>['string','nullable'],
            'additionals'=>['nullable'],
            'cemetery_id'=>['required','integer'],
            'size'=>['required','string'],
            'product_id'=>['required','integer'],
        ]);
        return OrderProductService::addOrderOne($data);        
    }
    
}
