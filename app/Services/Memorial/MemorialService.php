<?php

namespace App\Services\Memorial;

use App\Jobs\CloseApplicationJob;
use App\Models\Memorial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemorialService {
    
    public static function memorialAdd($data){
        if(Auth::check()){
            $user=Auth::user();
        }else{
            $user=createUserWithPhone($data['phone_memorial'],$data['name_memorial']);
        }


        $time=60*30;
        $time_now=convertToCarbon($data['time_now']);
        $memorial=Memorial::create([
        'city_id'=>$data['city_memorial'] ,
        'district_id'=>$data['district_memorial'] ,
        'date'=>$data['date_memorial'] ,
        'time'=>$data['time_memorial'] ,
        'count'=>$data['count_people'] ,
        'count_time'=>$data['count_time'] ,
        'user_id'=>Auth::user()->id,
        'call_time'=>$time_now->format('H:i'),

        ]);

        if(isset($data['call_time'])){
            if($data['call_time']!=null){
                $memorial->update(['call_time'=>$data['call_time']]);
                $time=secondsUntilAM($data['call_time'],$time_now);

            }
        }
        if(isset($data['call_tomorrow'])){
            $d = strtotime("+1 day");
            $memorial->update(['call_time'=>date("d.m.Y", $d)]);
            $time=secondsUntilEndOfTomorrow($time_now);

        }

        sendMessage('soobshhenie-pri-zaiavke-pop-up-pominki',['name'=>$user->name],$user);

        CloseApplicationJob::dispatch($memorial)->delay($time);

        return redirect()->back()->with("message_words_memory", 'Заявка отправлена');
    }
}