<?php

namespace App\Services\Account;


use App\Models\User;
use App\Models\Burial;
use App\Models\Service;
use App\Models\OrderBurial;
use App\Models\OrderProduct;
use App\Models\OrderService;
use App\Models\SearchBurial;
use Illuminate\Http\Request;
use App\Models\FavouriteBurial;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserService {
    public static function index(){
        $page=1;
        $user=Auth::user();
        $last_orders_products=OrderBurial::orderBy('id', 'desc')->where('user_id',$user->id)->take(3)->get();
        $last_orders_services=OrderService::orderBy('id', 'desc')->where('user_id',$user->id)->take(3)->get();
        return view('home',compact('user','last_orders_products','last_orders_services','page'));
    }

    public static function serviceIndex(){
        $page=3;
        $user=Auth::user();
        $orders=OrderService::orderBy('id', 'desc')->where('user_id',$user->id)->get();
        return view('account.user.services.index',compact('orders','page'));
    }

    public static function serviceFilter($status){
        $page=3;
        $user=Auth::user();
        if($status==1){
            $orders=OrderService::orderBy('id', 'desc')->where('user_id',$user->id)->whereIn('status',[1,2,3])->get();
            return view('account.user.services.index',compact('orders','status'));

        }
        $orders=OrderService::orderBy('id', 'desc')->where('user_id',$user->id)->where('status',$status)->get();
        return view('account.user.services.index',compact('orders','status','page'));
    }


    
    public static function burialRequestIndex(){
        $page=4;
        $user=Auth::user();
        $search_burial=SearchBurial::orderBy('id', 'desc')->where('user_id',$user->id)->get();
        return view('account.user.search-burial.index',compact('search_burial','page'));
    }

    public static function burialRequestFilter($status){
        $page=4;
        $user=Auth::user();
        $search_burial=SearchBurial::orderBy('id', 'desc')->where('user_id',$user->id)->where('status',$status)->get();
        return view('account.user.search-burial.index',compact('search_burial','status','page'));
    }

    public static function burialIndex(){
        $page=2;
        $user=Auth::user();
        $orders_products=OrderBurial::orderBy('id', 'desc')->where('user_id',$user->id)->get();
        return view('account.user.burial.index',compact('orders_products','page'));
    }


    public static function favoriteProduct(){
        $page=2;
        $user=Auth::user();
        $orders_products=FavouriteBurial::orderBy('id', 'desc')->where('user_id',$user->id)->get();
        return view('account.user.burial.favorite',compact('orders_products','page'));
    }
    
    public static function burialFilter($status){
        $page=2;
        $user=Auth::user();
        $orders_products=OrderBurial::orderBy('id', 'desc')->where('user_id',$user->id)->where('status',$status)->get();
        return view('account.user.burial.index',compact('orders_products','status','page'));
    }
    

    public static function userSettings(){
        $page=5;
        $user=Auth::user();
        return view('account.user.settings',compact('user','page'));
    }

    public static function userSettingsUpdate($data){
        $page=5;
        $user=Auth::user();
        $user_email=User::where('email',$data['email'])->where('id','!=',$user->id)->get();
        $user_phone=User::where('phone',$data['phone'])->where('id','!=',$user->id)->get();
        if(count($user_email)<1 && count($user_phone)<1){
            User::find($user->id)->update([
                'name'=>$data['name'],
                'surname'=>$data['surname'],
                'patronymic'=>$data['patronymic'],
                'phone'=>$data['phone'],
                'city'=>$data['city'],
                'adres'=>$data['adres'],
                'email'=>$data['email'],
                'whatsapp'=>$data['whatsapp'],
                'telegram'=>$data['telegram'],
                'language'=>$data['language'],
                'theme'=>$data['theme'],
            ]);
            if($data['password']!=null){
                if(Hash::check($data['password'], $user->password)==true){
                    if($data['password_new']==$data['password_new_2'] && strlen($data['password_new'])>7){
                        User::find($user->id)->update([
                            'password'=>Hash::make($data['password_new'])
                        ]);
                        
                        return redirect()->back();

                    }
                    return redirect()->back()->with("error", 'Новые пароли не совпадают');
                }
                return redirect()->back()->with("error", 'Неверный пароль');
            }
            if(!isset($data['email_notifications'])){
                User::find($user->id)->update([
                    'email_notifications'=>0
                ]);
            }else{
                User::find($user->id)->update([
                    'email_notifications'=>1
                ]);
            }
            if(!isset($data['sms_notifications'])){
                User::find($user->id)->update([
                    'sms_notifications'=>0
                ]);
            }else{
                User::find($user->id)->update([
                    'sms_notifications'=>1
                ]);
            }
            return redirect()->back();
        }
        return redirect()->back()->with("error", 'Такой телефон или email уже существует');
        
    }

    public static function products(){
        $user=Auth::user();
        $cemeteries=ulCemeteries($user->id);
        $orders_products=OrderProduct::orderBy('id','desc')->where('user_id',$user->id)->get();
        return view('account.user.product.index',compact('user','orders_products','cemeteries'));
    }
    
    public static function productDelete($id){
        OrderProduct::findOrFail($id)->delete();
        return redirect()->back();
    }

    public static function productFilter($status){
        $user=Auth::user();
    
        $cemeteries=ulCemeteries($user->id);
        $orders_products=OrderProduct::orderBy('id','desc')->where('user_id',$user->id)->where('status',$status)->get();
        return view('account.user.product.index',compact('user','orders_products','cemeteries','status'));
    }
}


