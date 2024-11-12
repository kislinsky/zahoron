<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Basket\BasketBurialService;

class BasketBurialContoller extends Controller
{
    public static function addToCart($id){
        return BasketBurialService::addToCart($id);
    }
    public static function checkout(){
        return BasketBurialService::checkout();
    }

    public static function deleteFromCart($id){
        return BasketBurialService::deleteFromCart($id);
    }
    
}
