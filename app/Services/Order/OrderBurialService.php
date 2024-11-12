<?php

namespace App\Services\Order;


use App\Models\User;
use App\Models\Burial;
use App\Models\OrderBurial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;



class OrderBurialService
{
    public static function orderAdd($data){
        if(isset($_COOKIE['add_to_cart_burial'])){
            $cart_items = json_decode($_COOKIE['add_to_cart_burial']);            
            if(Auth::check()){ 
                $isset_orderds_burials=OrderBurial::whereIn('product_id',$cart_items)->where('user_id',Auth::user()->id)->get();
                if(count($isset_orderds_burials)==0){
                    foreach($cart_items as $cart_item){
                        OrderBurial::create([
                            'product_id'=>$cart_item,
                            'user_id'=>Auth::user()->id,
                            'customer_comment'=>$data['message']
                        ]);
                    }
                } else{
                    return redirect()->back()->with("error", 'В вашем заказе уже есть купленные геолокации');
                }
                setcookie('add_to_cart_burial', '', -1, '/');
                $message='Ваш заказ успешно оформлен,вы можете оплатить его в личном кабинете';
                return redirect()->back()->with('message_order_burial',$message);
            }else{
                $user=User::where('email',$data['email'])->where('phone',$data['phone'])->get();
                if(!isset($user[0])){
                    $password=generateRandomString(8);
                    // mail($data['email'], 'Ваш пароль', $password);
                    $last_id=User::create([
                    'name'=>$data['name'],
                    'surname'=>$data['surname'],
                    'phone'=>$data['phone'],
                    'email'=>$data['email'],
                    'password'=>Hash::make($password),
                    ]);
                    foreach($cart_items as $cart_item){
                        OrderBurial::create([
                            'product_id'=>$cart_item,
                            'user_id'=>$last_id->id,
                            'customer_comment'=>$data['message']
                        ]);
                    }
                   
                    setcookie('add_to_cart_burial', '', -1, '/');
                    $message='Ваш заказ успешно оформлен,вы можете оплатить его в личном кабинете, ваш пароль отправлен на почту и телефон';
                    return redirect()->back()->with('message_order_burial',$message);
    
                }
                else{
                    return redirect()->back()->with("error", 'Такой пользователь уже существует');
                }
            }
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