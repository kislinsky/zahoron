<?php

namespace App\Services\Order;

use App\Models\Burial;
use App\Models\OrderBurial;
use App\Models\OrderService;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OrderServiceService
{
    public static function orderAdd($data){
        if(isset($_COOKIE['add_to_cart_service'])){
            $cart_items = json_decode($_COOKIE['add_to_cart_service']);            
            if(Auth::check()){
                $user=Auth::user();
            }else{
                $user=createUserWithPhone($data['phone'],$data['name']); 
                if($user==null){
                    return redirect()->back()->with('error','Пользователь с таким номером телефона уже существует');           
                }
            }
            
            $total_price = 0;
            $has_unpurchased_locations = false;
            
            foreach($cart_items as $cart_item){
                $burial = Burial::find($cart_item[0]);
                $services = Service::whereIn('id',$cart_item[1])->get();
                
                // Рассчитываем стоимость услуг
                $services_price = 0;
                foreach($services as $service){
                    $services_price += $service->getPriceForCemetery($burial->cemetery->id);
                }
                
                // Проверяем, куплена ли геолокация
                $order_product = OrderBurial::where('burial_id', $cart_item[0])
                    ->where('user_id', $user->id)
                    ->first();
                
                if($order_product && $order_product->status == 1){
                    // Геолокация уже куплена - добавляем только стоимость услуг
                    $order_price = $services_price;
                } else {
                    // Геолокация не куплена - добавляем стоимость геолокации + услуг
                    $order_price = $services_price + $burial->cemetery->price_burial_location;
                    $has_unpurchased_locations = true;
                }
                
                $total_price += $order_price;
                
                // Создаем заказ услуги
                OrderService::create([
                    'burial_id' => $cart_item[0],
                    'user_id' => $user->id,
                    'services_id' => json_encode($cart_item[1]),
                    'size' => $cart_item[2],
                    'customer_comment' => $data['message'],
                    'cemetery_id' => $cart_item[3],
                    'price' => $order_price,
                ]);
            }
            
            sendMessage('pokupka-uslug-po-uxodu-za-zaxoroneniem', [], $user);

            setcookie('add_to_cart_service', '', -1, '/');
            
            $message = 'Ваш заказ успешно оформлен, вы можете оплатить его в личном кабинете';
            if($has_unpurchased_locations){
                $message .= '. В стоимость заказа включена цена геолокации, так как она не была приобретена ранее.';
            }
            
            return redirect()->back()->with('message_order_burial', $message);
        }

        return redirect()->back();
    }
}