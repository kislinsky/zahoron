<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Basket\BasketServiceService;

class BasketServiceContoller extends Controller
{
    public static function addToCart($id,Request $request){
        $data=request()->validate([
            'size'=>['required','string'],
            'service'=>['required'],
        ]);
        return BasketServiceService::addToCart($id,$data);
    }
    public static function checkout(){
        return BasketServiceService::checkout();
    }
    public static function deletefromCart(Request $request){
        $data=request()->validate([
            'product_id'=>['required','string'],
            'service_id'=>['required','string'],
        ]);
        return BasketServiceService::deletefromCart($data);
    }

    
}
