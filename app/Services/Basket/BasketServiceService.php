<?php

namespace App\Services\Basket;


use App\Models\Burial;
use App\Models\Service;
use App\Models\Cemetery;
use App\Functions\Functions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



class BasketServiceService {

    public static function addToCart($id,$data){
       
        
        if(isset($_COOKIE['add_to_cart_service'])){
            $cookies_array = json_decode($_COOKIE['add_to_cart_service']);
            $t=0;
            $res=0;
            foreach($cookies_array as $item){
                if($item[0]===$id){
                    if ($item[2]!=$data['size']){
                        $cookies_array[$t][2]=$data['size'];
                    }
                    $i=0;
                    foreach($data['service'] as $id_service){
                        $k=0;
                        foreach($item[1] as $id_service_data){
                            if($id_service==$id_service_data){
                                $k++;
                                break;
                            }
                        }
                        if($k==0){
                            array_push($item[1],$id_service);
                        }
                    } 
                    $cookies_array[$t][1]=$item[1];
                    $res++;
                    break;
                }
                $t++;

            }
            if ($res==0){
                $cemetery=Cemetery::findOrFail(Burial::findOrFail($id)->cemetery_id);
                array_push($cookies_array,[$id,$data['service'],$data['size'],$cemetery->id]);
            }
            setcookie('add_to_cart_service', '', -1, '/');
            setcookie("add_to_cart_service", json_encode($cookies_array), time()+20*24*60*60,'/');
            return redirect()->back()->with("message_cart", "Вы успешно добавили услуги в заказ");
        }else{
            $cemetery=Cemetery::findOrFail(Burial::findOrFail($id)->cemetery_id);
            setcookie("add_to_cart_service", json_encode([[$id,$data['service'],$data['size'],$cemetery->id]]), time()+20*24*60*60,'/');
            return redirect()->back()->with("message_cart", "Вы успешно добавили услуги в заказ");
        }
    }

    public static function checkout(){
        $user=user();
        if(isset($_COOKIE['add_to_cart_service'])){
            $cart_items = json_decode($_COOKIE['add_to_cart_service']);
            return view('service.checkout',compact('cart_items','user'));
        }
        $no_items='Добавьте услуги в заказ';
        return view('service.checkout',compact('no_items','user'));
    }

    public static function deletefromCart($data){
        
        $cart_items = json_decode($_COOKIE['add_to_cart_service']);
        $k=0;
        foreach($cart_items as $cart_item){
            if($cart_item[0]==$data['product_id']){
                $ids_services=[];
                foreach($cart_item[1] as $id_cart_service){
                    if ($id_cart_service!=$data['service_id']){
                        $ids_services[]=$id_cart_service;
                    }
                }
                $cart_items[$k][1]=$ids_services;
            }
            $k++;
        }
        $k=0;
        $cart_items_new=[];
        foreach($cart_items as $cart_item){
            if(isset($cart_item[1][0])){

                $cart_items_new[]=$cart_item;
            }
        }
        if(count($cart_items_new)!=0){
            setcookie('add_to_cart_service', '', -1, '/');
            setcookie("add_to_cart_service", json_encode($cart_items_new), time()+20*24*60*60,'/');
            return redirect()->back();
        }
        setcookie('add_to_cart_service', '', -1, '/');
        return redirect()->back();
    }


}
