<?php

namespace App\Services\Beautification;

use App\Jobs\CloseApplicationJob;
use App\Models\User;
use App\Models\Burial;
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


        

        $time=60*30;
        $time_now=convertToCarbon($data['time_now']);

        $beautification=Beautification::create([
            'products_id'=>json_encode($data['products_beautification']),
            'user_id'=>$user->id,
            'city_id'=>$data['city_beautification'],
            'cemetery_id'=>$data['cemetery_beautification'],
            'call_time'=>$time_now->format('H:i'),
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
                $time=secondsUntilAM($data['call_time'],$time_now)+30*60;
            }
        }
        if(isset($data['call_tomorrow'])){
            $d = strtotime("+1 day");
            $beautification->update(['call_time'=>date("d.m.Y", $d)]);
            $time=secondsUntilEndOfTomorrow($time_now)+30*60;
        }
        
        CloseApplicationJob::dispatch($beautification)->delay($time);

        return redirect()->back()->with("message_words_memory", 'В ближайшее время в личном кабинете вы сможете оплатить услугу');
    }
}
