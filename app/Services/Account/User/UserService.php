<?php

namespace App\Services\Account\User;


use App\Models\User;

use App\Models\OrderService;
use Illuminate\Support\Facades\Hash;

class UserService {

    
    public static function index(){
        $user=user();
        $orders_burials=$user->newBurials();
        $orders_services=$user->newServices();
        return view('account.user.index',compact('user','orders_burials','orders_services'));
    }



    // public static function services($data){
    //     $user=user();
    //     $status=null;
    //     if(isset($data['status']) && $data['status']!=null ){
    //         $orders_services=$user->orderServices($data['status'])->orderBy('id','desc')->paginate(6);
    //         $status=$data['status'];
    //     }else{
    //         $orders_services=$user->orderServices()->orderBy('id','desc')->paginate(6);
    //     }
    //     return view('account.user.services.index',compact('orders_services','status'));

    // }

    public static function services($data){
        $user=user();
        $status=null;
        if(isset($data['status']) && $data['status']!=null ){
            if($data['status']==2){
                $orders_services=OrderService::orderBy('id','desc')->where('paid',1)->where('user_id',$user->id)->paginate(6);
            }
            
            else{
                $orders_services=OrderService::orderBy('id','desc')->where('status',$data['status'])->where('user_id',$user->id)->paginate(6);
            }
            $status=$data['status'];
        }else{
            $orders_services=OrderService::orderBy('id','desc')->where('user_id',$user->id)->paginate(6);
        }
        return view('account.user.services.index',compact('orders_services','status'));

    }


    
    public static function burialRequestIndex($data){
        $user=user();
        $status=null;
        if(isset($data['status']) && $data['status']!=null ){
            $search_burials=$user->searchBurials($data['status'])->orderBy('id','desc')->paginate(6);
            $status=$data['status'];
        }else{
            $search_burials=$user->searchBurials()->orderBy('id','desc')->paginate(6);
        }
        return view('account.user.search-burial.index',compact('search_burials','status'));
    }

    public static function burialRequestDelete($burial_request){
        $burial_request->delete();
        return redirect()->back()->with('message_cart','Заявка успешно отменена.');
    }



    public static function burials($data){
        $user=user();
        $status=null;
        if(isset($data['status']) && $data['status']!=null ){
            $orders_burials=$user->orderBurials($data['status'])->paginate(6);
            $status=$data['status'];
        }else{
            $orders_burials=$user->orderBurials()->paginate(6);
        }
        
        return view('account.user.burial.index',compact('orders_burials','status'));
    }

    public static function favoriteProduct(){
        $user=user();
        $favorite_burials=$user->favoriteBurial()->getQuery()->paginate(6);
        return view('account.user.burial.favorite',compact('favorite_burials'));
    }
    


    public static function userSettings(){
        $user=user();
        return view('account.user.settings.index',compact('user'));
    }



    public static function userSettingsUpdate($data){
        $user=user();
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

    public static function products($data){
        $user=user();
        $cemeteries=ulCemeteries($user->id);
        $status=null;
        if(isset($data['status']) && $data['status']!=null){
            $orders_products=$user->orderProducts($data['status'])->paginate(6);
            $status=$data['status'];
        }else{
            $orders_products=$user->orderProducts()->paginate(6);
        }
        return view('account.user.product.index',compact('user','orders_products','cemeteries','status'));
    }
    
    public static function productDelete($order){
        $order->delete();
        return redirect()->back();
    }


    public static function payBurial($order){
        
        $result = createPayment($order->price,'Покупка геолокации',route('account.user.burial.callback',$order->id));

        if ($result['success']) { 
            // Перенаправляем пользователя на страницу оплаты
            return redirect()->away($result['redirect_url']);
        } else {
            // Обработка ошибки
            return redirect()->back()->with('error','Ошибка оплаты');
        }

    }

    public static function callbackPayBurial($request,$order){
        $order->update([
            'status'=>1,
            'date_pay'=>now()
            ]);
            
        return redirect()->route('account.user.burial');
        
    }

    public static function payService($order){
        
        $result = createPayment($order->price,'Покупка услуг по облогораживанию',route('account.user.service.callback',$order->id));

        if ($result['success']) { 
            // Перенаправляем пользователя на страницу оплаты
            return redirect()->away($result['redirect_url']);
        } else {
            // Обработка ошибки
            return redirect()->back()->with('error','Ошибка оплаты');
        }

    }

    public static function callbackPayService($request,$order){
        $order->update([
            'status'=>2,
            'date_pay'=>now()
            ]);
            
        return redirect()->route('account.user.services.index');
    }

    public static function payBurialRequest($order){

        $result = createPayment($order->price,'Покупка геолокации',route('account.user.burial-request.callback',$order->id));

        if ($result['success']) { 
            // Перенаправляем пользователя на страницу оплаты
            return redirect()->away($result['redirect_url']);
        } else {
            // Обработка ошибки
            return redirect()->back()->with('error','Ошибка оплаты');
        }
    }

    public static function callbackPayBurialRequest($request,$order){
        $order->update([
            'status'=>4,
            'date_pay'=>now()
            ]);
            
        return redirect()->route('account.user.burial-request.index');
    }

    
}



