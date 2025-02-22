<?php

namespace App\Services\Order;


use App\Models\User;
use App\Models\Product;
use App\Models\OrderBurial;
use App\Models\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;



class OrderServiceService
{
    public static function orderAdd($data){
        if(isset($_COOKIE['add_to_cart_service'])){
            $cart_items = json_decode($_COOKIE['add_to_cart_service']);            
            if(Auth::check()){ 
                
                foreach($cart_items as $cart_item){
                    $order_product=OrderBurial::where('burial_id',$cart_item[0])->where('user_id',Auth::user()->id)->get();
                    if($order_product->count()>0){
                        if($order_product[0]->status==1){
                            $product=getBurial($cart_item[0]);
                            $services=servicesBurial($cart_item[1]);
                            $price=0;
                            foreach($services as $service){
                                $price+=$service->getPriceForCemetery($product->cemetery->id);
                            }

                            OrderService::create([
                                'burial_id'=>$cart_item[0],
                                'user_id'=>Auth::user()->id,
                                'services_id'=>json_encode($cart_item[1]),
                                'size'=>$cart_item[2],
                                'customer_comment'=>$data['message'],
                                'cemetery_id'=>$cart_item[3],
                                'price'=>$price,
                            ]);
                           
                            
                        }else{
                            return redirect()->back()->with("error", 'У вас есть не купленные геолокации');
                        }
                    }else{
                        return redirect()->back()->with("error", 'У вас есть не купленные геолокации');
                    }

                }
                setcookie('add_to_cart_service', '', -1, '/');
                $message='Ваш заказ успешно оформлен,вы можете оплатить его в личном кабинете';
                return redirect()->back()->with('message_order_burial',$message);
            }else{
                return redirect()->back()->with("error", 'Зарегистрируйтесь  и приобретите геолокации, для которых вы выбрали услуги');
            }
        }
        return redirect()->back();
    }
}