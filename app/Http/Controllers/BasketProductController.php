<?php

namespace App\Http\Controllers;

use App\Rules\RecaptchaRule;
use App\Services\Basket\BasketProductService;
use Illuminate\Http\Request;

class BasketProductController extends Controller
{
    public static function addToCart(Request $request){
        $data=request()->validate([
            'id_product'=>['required','integer'],
            'additionals'=>['nullable'],
            'size'=>['nullable','string'],
        ]);
        
        return BasketProductService::addToCart($data);
    }

    public static function cartItems(){
        return  BasketProductService::cartItems();
    }

    public static function changeCountCart(Request $request){
        $data=request()->validate([
            'id_product'=>['required','integer'],
            'count'=>['nullable','integer'],
        ]);
        return  BasketProductService::changeCountCart($data);
    }
    
    public static function deleteFromCart($id){
        return BasketProductService::deleteFromCart($id);
    }

    public static function addToCartDetails(Request $request){
        $data=request()->validate([
            'date'=>['nullable'],
            'count'=>['nullable','integer'],
            'time'=>['nullable'],
            'id_product'=>['required','integer'],
            'additionals'=>['nullable'],
            'size'=>['nullable','string'],
        ]);
        
        return BasketProductService::addToCartDetails($data);
    }

    
    
    

    
}
