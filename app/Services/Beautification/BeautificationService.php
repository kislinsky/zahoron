<?php

namespace App\Services\Beautification;


use App\Models\News;
use App\Models\User;
use App\Models\Burial;
use App\Models\Service;
use App\Models\Cemetery;
use App\Models\CategoryNews;
use Illuminate\Http\Request;
use App\Models\Beautification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;



class BeautificationService {
    public static function sendBeautification($data){
        if(Auth::check()){
            $user=Auth::user();
        }else{
            $user_phone=User::where('phone',$data['phone_beautification'])->get();
            if( !isset($user_phone[0])){
                $password=generateRandomString(8);
                $user=User::create([
                'name'=>$data['name_beautification'],
                'phone'=>$data['phone_beautification'],
                'password'=>Hash::make($password),
                ]);
            }
            else{
                return redirect()->back()->with("error", 'Такой телефон уже зарегестрированы.');
            }
        }
        
            $beautification=Beautification::create([
                'products_id'=>json_encode($data['products_beautification']),
                'user_id'=>$user->id,
                'city_id'=>$data['city_beautification'],
                'cemetery_id'=>$data['cemetery_beautification'],
            ]);
            if(isset($data['burial_id'])){
                if($data['burial_id']!=null){
                    $burial=Burial::findOrFail($data['burial_id']);
                    $beautification->update(['burial_id'=>$burial->id]);
                }
            }
            if(isset($data['call_time'])){
                if($data['call_time']!=null){
                    $beautification->update(['call_time'=>$data['call_time']]);
                }
            }
            if(isset($data['call_tomorrow'])){
                $d = strtotime("+1 day");
                $beautification->update(['call_time'=>date("d.m.Y", $d)]);
            }

            return redirect()->back()->with("message_words_memory", 'В ближайшее время в личном кабинете вы сможете оплатить услугу');
    }
}
