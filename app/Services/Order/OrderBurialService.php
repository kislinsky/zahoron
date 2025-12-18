<?php

namespace App\Services\Order;

use App\Models\Burial;
use App\Models\OrderBurial;
use App\Models\User;
use App\Services\YooMoneyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;



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


    public static function sendCode(Request $request)
    {
        $request->validate([
            'phone' => 'required|regex:/^7\d{10}$/'
        ]);
        
        $phone = normalizePhone($request->phone);
        $code = rand(1000, 9999);
        
        
        // Сохраняем код в сессии
        session([
            'auth_code' => $code, 
            'auth_phone' => $phone, 
            'auth_code_time' => now(),
            'burial_id' => $request->burial_id
        ]);
        
        // Отправляем SMS
        sendSms($phone, "Ваш код подтверждения: $code");
        
        return response()->json([
            'success' => true,
            'message' => 'Код отправлен на ваш телефон'
        ]);
    }
    
    public static function verifyCode(Request $request)
    {
        $request->validate([
            'phone' => 'required|regex:/^7\d{10}$/',
            'code' => 'required|digits:4',
            'burial_id' => 'required|exists:burials,id'
        ]);
        
        // Проверяем код
        $sessionCode = session('auth_code');
        $sessionPhone = session('auth_phone');
        
        if ($sessionCode != $request->code || $sessionPhone != normalizePhone($request->phone)) {
            return response()->json([
                'success' => false,
                'message' => 'Неверный код подтверждения'
            ], 422);
        }
        
        // Проверяем время действия кода
        $codeTime = session('auth_code_time');
        if (now()->diffInMinutes($codeTime) > 10) {
            return response()->json([
                'success' => false,
                'message' => 'Код устарел. Запросите новый код'
            ], 422);
        }
        
        // Находим или создаем пользователя
        $user = User::where('phone', normalizePhone($request->phone))->first();
        
        if (!$user) {
            // Генерируем случайный email для пользователя
            $email = "user_" . substr($request->phone, -6) . "@zahoron.ru";
            
            $user = User::create([
                'phone' => normalizePhone($request->phone),
                'email' => $email,
                'password' => Hash::make(Str::random(12)),
                'name' => 'Пользователь ' . substr($request->phone, -4)
            ]);
            
            // Если у вас есть поле для подтверждения email
            $user->email_verified_at = now();
            $user->save();
        }
        
        // Авторизуем пользователя
        Auth::login($user, true);
        
        // Получаем burial
        $burial = Burial::findOrFail($request->burial_id);
        
        // Очищаем сессию
        session()->forget(['auth_code', 'auth_phone', 'auth_code_time', 'burial_id']);
        
        // Проверяем, нужно ли платить
        if ($burial->cemetery->price_burial_location == 0 || 
            $burial->cemetery->price_burial_location == null || 
            $burial->userHave()) {
            
            // Если оплата не требуется
            return response()->json([
                'success' => true,
                'redirect' => route('burial.show', $burial->id)
            ]);
        }
        
        // Создаем данные для платежа
        $paymentData = [
            'burial_id' => $burial->id,
            'user_id' => $user->id,
            'count' => $burial->cemetery->price_burial_location,
            'type' => 'burial_buy'
        ];
        
        try {
            $object = new YooMoneyService();
            // Создаем платеж через YooMoney
            $paymentUrl = $object->createPayment(
                $burial->cemetery->price_burial_location,
                route('account.user.burial'), // URL возврата после оплаты
                'Оплата доступа к координатам захоронения',
                $paymentData,
                $user->email,
                null,
                false
            );
            
            return response()->json([
                'success' => true,
                'payment_url' => $paymentUrl
            ]);
            
        } catch (\Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка создания платежа. Пожалуйста, попробуйте позже.'
            ], 500);
        }
    }
    


   
}