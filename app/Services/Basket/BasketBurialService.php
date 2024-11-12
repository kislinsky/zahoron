<?php

namespace App\Services\Basket;


use App\Models\Burial;
use App\Models\Service;
use App\Functions\Functions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



class BasketBurialService {

    public static function addToCart($id){
        
        if(isset($_COOKIE['add_to_cart_burial'])){
            $cookies_array = json_decode($_COOKIE['add_to_cart_burial']);
            $res=0;
            foreach($cookies_array as $item){
                if($item===$id){
                    $res++;
                }
            }
            if ($res==0){
                array_push($cookies_array,$id);
                setcookie('add_to_cart_burial', '', -1, '/');
                setcookie("add_to_cart_burial", json_encode($cookies_array), time()+20*24*60*60,'/');
                return redirect()->back()->with("message_cart", "Вы успешно добавили геолокацию в заказ");
            }
            setcookie('add_to_cart_burial', '', -1, '/');
            setcookie("add_to_cart_burial", json_encode($cookies_array), time()+20*24*60*60,'/');
            return redirect()->back()->with("message_cart", "Эта геолокация уже есть в заказе");
            
        }else{
            setcookie("add_to_cart_burial", json_encode([$id]), time()+20*24*60*60,'/');
            return redirect()->back()->with("message_cart", "Вы успешно добавили геолокацию в заказ");
        }
    }

    public static function checkout(){
        $user=user();
        if(isset($_COOKIE['add_to_cart_burial'])){
            $cart_items = json_decode($_COOKIE['add_to_cart_burial']);
            return view('burial.checkout',compact('cart_items','user'));
        }
        $no_items='Добавьте геолокации в заказ';
        return view('burial.checkout',compact('no_items','user'));
    }
    public static function deleteFromCart($id){
        $cookies_array = json_decode($_COOKIE['add_to_cart_burial']);
        $ids=[];
        foreach($cookies_array as $item){
            if($item!=$id){
                $ids[]=$item;
            }
        }
        if(count($ids)>0){
            setcookie('add_to_cart_burial', '', -1, '/');
            setcookie("add_to_cart_burial", json_encode($ids), time()+20*24*60*60,'/');
            return redirect()->back();
        }
        setcookie('add_to_cart_burial', '', -1, '/');
        return redirect()->back();
    }
    
}