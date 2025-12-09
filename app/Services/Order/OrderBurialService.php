<?php

namespace App\Services\Order;

use App\Models\Burial;
use App\Models\OrderBurial;
use App\Models\User;
use App\Services\YooMoneyService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;



class OrderBurialService
{
    public static function orderAdd($data){
        if(Auth::check()){
            $user=Auth::user();
        }else{
            $user=createUserWithPhone($data['phone'],$data['name']); 
            if($user==null){
                return redirect()->back()->with('error','Пользователь с таким номером телефона уже существует');           
            }
        }
        if(isset($_COOKIE['add_to_cart_burial'])){
            $cart_items = json_decode($_COOKIE['add_to_cart_burial']);     
                $isset_orderds_burials=OrderBurial::whereIn('burial_id',$cart_items)->where('user_id',$user->id)->get();

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
                sendMessage('pokupka-geolokacii-zaxoroneniia',[],$user);
                setcookie('add_to_cart_burial', '', -1, '/');
                $message='Ваш заказ успешно оформлен,вы можете оплатить его в личном кабинете';
                return redirect()->back()->with('message_order_burial',$message);
            }


        return redirect()->back();

    }

    public static function orderAddWithPay($burial){
    $user = user(); // Получаем пользователя
    
    $data = [
        'burial_id' => $burial->id,
        'user_id' => $user->id,
        'count' => $burial->cemetery->price_burial_location,
        'type' => 'burial_buy'
    ];
    
    $object = new YooMoneyService();
    
    // Передаем email пользователя (обязательно для чека)
    return $object->createPayment(
        $burial->cemetery->price_burial_location,
        route('account.user.burial'),
        'Оплата места захоронения', // Исправил описание
        $data,
        $user->email // Передаем email пользователя
    );
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