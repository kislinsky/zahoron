<?php

namespace App\Services\Order;

use App\Models\Burial;
use App\Models\User;
use App\Models\OrderBurial;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;



class OrderBurialService
{
    public static function orderAdd($data){
        if(Auth::check()){
            $user=Auth::user();
        }else{
            $user=createUserWithPhone($data['phone'],$data['name']); 
        }
        if(isset($_COOKIE['add_to_cart_burial'])){
            $cart_items = json_decode($_COOKIE['add_to_cart_burial']);            
                $isset_orderds_burials=OrderBurial::whereIn('burial_id',$cart_items)->where($user->id)->get();
                if($isset_orderds_burials->count()==0){
                    foreach($cart_items as $cart_item){
                        $price=Burial::find($cart_item)->cemetery->price_burial_location;
                        OrderBurial::create([
                            'burial_id'=>$cart_item,
                            'user_id'=>$user->id,
                            'customer_comment'=>$data['message'],
                            'price'=>$price,
                        ]);
                    }
                } else{
                    return redirect()->back()->with("error", 'В вашем заказе уже есть купленные геолокации');
                }
                setcookie('add_to_cart_burial', '', -1, '/');
                $message='Ваш заказ успешно оформлен,вы можете оплатить его в личном кабинете';
                return redirect()->back()->with('message_order_burial',$message);
            }
        return redirect()->back();

    }


    
    public static function burialDelete($id){
        $order=OrderBurial::findOrFail($id);
        if($order->status==0){
            OrderBurial::findOrFail($id)->delete();
            return redirect()->back();
        }
        return redirect()->back()->with("error", 'Эта геолокация уже оплачена');
       

    }

   
}