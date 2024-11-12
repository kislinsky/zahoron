<?php

namespace App\Services\Basket;


use App\Models\Burial;
use App\Models\Product;
use App\Models\Service;
use App\Functions\Functions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



class BasketProductService {

    public static function addToCart($data){
        $id=$data['id_product'];
        $product=Product::findOrFail($id);
        if(isset($_COOKIE['add_to_cart_product'])){
            $cookies_array = json_decode($_COOKIE['add_to_cart_product']);
            $k=0;
            $i=0;
            foreach($cookies_array as $product_cart){
                if($product_cart[0]==$id){
                    $cookies_array[$k][2]=$product_cart[2]+1;
                    $i++;
                }
                $k++;
            }
            if($i==0){
                if(isset($data['additionals'])){
                    $cookies_array[]=[$id,$data['additionals'],1,explode('|',$product->size)[0]];
                }else{
                    $cookies_array[]=[$id,[],1,explode('|',$product->size)[0]];
                }
            }

            setcookie('add_to_cart_product', '', -1, '/');
            setcookie("add_to_cart_product", json_encode($cookies_array), time()+20*24*60*60,'/');
            return response()->json(['price'=> priceProduct($product)]);
        }else{
            $cookies_array=[];
            if(isset($data['additionals'])){
                $cookies_array[]=[$id,$data['additionals'],1,explode('|',$product->size)[0]];
            }else{
                $cookies_array[]=[$id,[],1,explode('|',$product->size)[0]];
            }
            setcookie("add_to_cart_product", json_encode($cookies_array), time()+20*24*60*60,'/');
            return response()->json(['price'=> priceProduct($product)]);

        }
        
    }


    public static function cartItems(){
        if(isset($_COOKIE['add_to_cart_product'])){
            $cart_items = json_decode($_COOKIE['add_to_cart_product']);
            if(Auth::check()){
                $user=Auth::user();
                return view('product.checkout',compact('cart_items','user'));
            }return view('product.checkout',compact('cart_items'));
        }
        $no_items='Добавьте товары в корзину';
        if(Auth::check()){
            $user=Auth::user();
            return view('product.checkout',compact('no_items','user'));

        }
        return view('product.checkout',compact('no_items'));

    }


    public static function changeCountCart($data){
        $product=Product::findOrFail($data['id_product']);
        if(isset($_COOKIE['add_to_cart_product'])){
            $cookies_array = json_decode($_COOKIE['add_to_cart_product']);
            $k=0;
            foreach($cookies_array as $product_cart){
                if($product_cart[0]==$data['id_product']){
                    if(!isset($data['count'])){
                        $new_res=[];
                            foreach($cookies_array as $key=>$item){
                                if ($key!=$k){
                                    $new_res[]=$item;
                                }
                            }
                        if (count($cookies_array)>0){
                            setcookie('add_to_cart_product', '', -1, '/');
                            setcookie("add_to_cart_product", json_encode($cookies_array), time()+20*24*60*60,'/');
                            $price=$product->price;
                            if(count($product_cart[1])>0){
                                $price+=priceAdditionals($product_cart[1]);
                            }
                            $cart_items=$cookies_array;
                            return view("product.components.products-basket", compact("cart_items"));
                        }else{
                            setcookie('add_to_cart_product', '', -1, '/');
                            $cart_items=[];
                            return view("product.components.products-basket", compact("cart_items"));
                        }
                       
                    }else{
                        if($data['count']==0){
                            $new_res=[];
                            foreach($cookies_array as $key=>$item){
                                if ($key!=$k){
                                    $new_res[]=$item;
                                }
                            }
                            if (count($new_res)>0){
                                setcookie('add_to_cart_product', '', -1, '/');
                                setcookie("add_to_cart_product", json_encode($new_res), time()+20*24*60*60,'/');
                                $price=$product->price;
                                if(count($product_cart[1])>0){
                                    $price+=priceAdditionals($product_cart[1]);
                                }
                                $cart_items=$new_res;
                                return view("product.components.products-basket", compact("cart_items"));
                            }else{
                                setcookie('add_to_cart_product', '', -1, '/');
                                $cart_items=[];
                                return view("product.components.products-basket", compact("cart_items"));
                            }
                        }
                        $cookies_array[$k][2]=$data['count'];
                        $price=$product->price*$data['count'];
                        if(count($product_cart[1])>0){
                            $price+=(priceAdditionals($product_cart[1])*$data['count']);
                        }
                        setcookie('add_to_cart_product', '', -1, '/');
                        setcookie("add_to_cart_product", json_encode($cookies_array), time()+20*24*60*60,'/');
                        $cart_items=$cookies_array;
                        return view("product.components.products-basket", compact("cart_items"));
                    }
                }
                $k++;
            }
        } 
        $cart_items=[];
        return view("product.components.products-basket", compact("cart_items"));
    }


    public static function deleteFromCart($id){
        $product=Product::findOrFail($id);
        if(isset($_COOKIE['add_to_cart_product'])){
            
            $cookies_array = json_decode($_COOKIE['add_to_cart_product']);
            $new_res=[];
            $k=0;
            foreach($cookies_array as $product_cart){
                if($product_cart[0]!=$id){
                    $new_res[]=$product_cart;
                }$k++;
            }

            if(count($new_res)>0){
                setcookie("add_to_cart_product", json_encode($new_res), time()+20*24*60*60,'/');
                return redirect()->back();
            }else{
                setcookie('add_to_cart_product', '', -1, '/');
                return redirect()->back();
            }
        }
        return redirect()->back();
    }


    public static function addToCartDetails($data){
        $id=$data['id_product'];
        $product=Product::findOrFail($id);
        if(isset($_COOKIE['add_to_cart_product'])){
            $cookies_array = json_decode($_COOKIE['add_to_cart_product']);
            $k=0;
            $i=0;
            foreach($cookies_array as $product_cart){
                if($product_cart[0]==$id){
                    if(isset($data['count'])){
                        $cookies_array[$k][2]=$product_cart[2]+$data['count'];
                    }else{
                        $cookies_array[$k][2]=$product_cart[2]+1;
                    }
                    $i++;
                }
                $k++;
            }
            if($i==0){
                if(isset($data['additionals'])){
                    $res=[$id,$data['additionals'],1];
                }else{
                    $res=[$id,[],1];
                }
                if(isset($data['size'])){
                    $res[]=$data['size'];
                }else{
                    $res[]=explode('|',$product->size)[0];
                }
                if(isset($data['count'])){
                    $res[2]=$data['count'];
                }
                if(isset($data['date'])){
                    $res[]=$data['date'];
                }
                if(isset($data['time'])){
                    $res[]=$data['time'];
                }
                $cookies_array[]=$res;
            }

            setcookie('add_to_cart_product', '', -1, '/');
            setcookie("add_to_cart_product", json_encode($cookies_array), time()+20*24*60*60,'/');
            return redirect()->back()->with("message_cart", "Вы успешно добавили товар в заказ");

        }else{
            $cookies_array=[];
            if(isset($data['additionals'])){
                $res=[$id,$data['additionals'],1];
            }else{
                $res=[$id,[],1];
            }
            if(isset($data['size'])){
                $res[]=$data['size'];
            }else{
                $res[]=explode('|',$product->size)[0];
            }
            if(isset($data['count'])){
                $res[2]=$data['count'];
            }
            if(isset($data['date'])){
                $res[]=$data['date'];
            }
            if(isset($data['time'])){
                $res[]=$data['time'];
            }
            $cookies_array[]=$res;
            
            setcookie("add_to_cart_product", json_encode($cookies_array), time()+20*24*60*60,'/');
            return redirect()->back()->with("message_cart", "Вы успешно добавили товар в заказ");
        }
        
    }


    

    
}