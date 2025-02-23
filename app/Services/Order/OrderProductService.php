<?php

namespace App\Services\Order;


use App\Models\User;
use App\Models\Product;
use App\Models\ProductAplication;
use App\Models\OrderProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;



class OrderProductService
{
    public static function orderAdd($data){
        if(isset($_COOKIE['add_to_cart_product'])){
            $cart_items = json_decode($_COOKIE['add_to_cart_product']);            
            if(Auth::check()){ 
                $user=Auth::user();
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
                }
                else{
                    return redirect()->back()->with("error", 'Такой пользователь уже существует');
                }
            }
            foreach($cart_items as $cart_item){
                $product=Product::findOrFail($cart_item[0]);
                if($product->type=='memorial-menu'){
                    OrderProduct::create([
                        'additional'=>json_encode($cart_item[1]),
                        'product_id'=>$product->id,
                        'user_id'=>$user->id,
                        'customer_comment'=>$data['message'],
                        'count'=>$cart_item[2],
                        'price'=>priceProductOrder($cart_item),
                        'date'=>$cart_item[4],
                        'time'=>$cart_item[5],
                        'organization_id'=>$product->organization->id
                    ]);
                }else{
                    OrderProduct::create([
                        'additional'=>json_encode($cart_item[1]),
                        'product_id'=>$product->id,
                        'user_id'=>$user->id,
                        'customer_comment'=>$data['message'],
                        'count'=>$cart_item[2],
                        'price'=>priceProductOrder($cart_item),
                        'size'=>$cart_item[3],
                        'cemetery_id'=>$product->cemetery_id,
                        'organization_id'=>$product->organization->id

                    ]);
                }
               
            }

            setcookie('add_to_cart_product', '', -1, '/');
            $message='Ваш заказ успешно оформлен,вы можете оплатить его в личном кабинете';
            return redirect()->back()->with('message_order_burial',$message); 
        }
        return redirect()->back();
    }

    public static function addOrderOne($data){
        if(Auth::check()){
            $user=Auth::user();
        }else{
            $user=createUserWithPhone($data['phone'],$data['name']);
        }
        
        $product=Product::find($data['product_id']);
        $price_product=priceProduct($product);
        $mortuary=null;
        if(isset($data['no_have_mortuary']) || !isset($data['mortuary_id'])){
             $mortuary=null;
        }else{
            $mortuary=$data['mortuary_id'];
        }
        $additionals=null;
        if(isset($data['additionals'])){
            $additionals=json_encode($data['additionals']);
        }
        if($product->category->slug=='organizacia-pohoron'){
           $order=OrderProduct::create([
            'product_id'=>$product->id,
            'user_id'=>$user->id,
            'customer_comment'=>$data['message'],
            'count'=>1,
            'price'=>$price_product,
            'cemetery_id'=>$data['cemetery_id'],
            'mortuary_id'=>$mortuary,
            'additional'=>$additionals,
            'organization_id'=>$product->organization->id

           ]);
           $message='Ваш заказ успешно оформлен,вы можете оплатить его в личном кабинете';
            return redirect()->back()->with('message_order_burial',$message); 
        }
        if($product->category->slug=='organizacia-kremacii'){
            $order=OrderProduct::create([
             'product_id'=>$product->id,
             'user_id'=>$user->id,
             'customer_comment'=>$data['message'],
             'count'=>1,
             'price'=>$price_product,
             'mortuary_id'=>$mortuary,
             'additional'=>$additionals,
            'organization_id'=>$product->organization->id

            ]);
            $message='Ваш заказ успешно оформлен,вы можете оплатить его в личном кабинете';
            return redirect()->back()->with('message_order_burial',$message); 
        }
        if($product->category->slug=='otpravka-gruz-200'){
            $order=OrderProduct::create([
             'product_id'=>$product->id,
             'user_id'=>$user->id,
             'city_from'=>$data['city_from'],
             'city_to'=>$data['city_to'],
             'customer_comment'=>$data['message'],
             'count'=>1,
             'price'=>$price_product,
             'mortuary_id'=>$mortuary,
             'additional'=>$additionals,
            'organization_id'=>$product->organization->id

            ]);
            $message='Ваш заказ успешно оформлен,вы можете оплатить его в личном кабинете';
            return redirect()->back()->with('message_order_burial',$message); 
        }
        if($product->category->slug=='knopka-mogil' || $product->category->slug=='pominal-nye-zaly'){
            $order=OrderProduct::create([
             'product_id'=>$product->id,
             'user_id'=>$user->id,
             'customer_comment'=>$data['message'],
             'count'=>1,
             'price'=>$price_product,
             'cemetery_id'=>$data['cemetery_id'],
             'additional'=>$additionals,
            'organization_id'=>$product->organization->id

            ]);
            $message='Ваш заказ успешно оформлен,вы можете оплатить его в личном кабинете';
            return redirect()->back()->with('message_order_burial',$message); 
        }
        else{
            $order=OrderProduct::create([
                'product_id'=>$product->id,
                'user_id'=>$user->id,
                'customer_comment'=>$data['message'],
                'count'=>1,
                'price'=>$price_product,
                'cemetery_id'=>$data['cemetery_id'],
                'size'=>$data['size'],
                'additional'=>$additionals,
                'organization_id'=>$product->organization->id
            ]);
            $message='Ваш заказ успешно оформлен,вы можете оплатить его в личном кабинете';
            return redirect()->back()->with('message_order_burial',$message); 
        }
        
    }

}