<?php

namespace App\Services\Memorial;

use App\Models\Memorial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemorialService {
    
    public static function memorialAdd($data){
       if(Auth::check()){
            $memorial=Memorial::create([
            'city_id'=>$data['city_memorial'] ,
            'district_id'=>$data['district_memorial'] ,
            'date'=>$data['date_memorial'] ,
            'time'=>$data['time_memorial'] ,
            'count'=>$data['count_people'] ,
            'count_time'=>$data['count_time'] ,
            'user_id'=>Auth::user()->id,
            ]);
            if(isset($data['call_time'])){
                if($data['call_time']!=null){
                    $memorial->update(['call_time'=>$data['call_time']]);
                }
            }
            if(isset($data['call_tomorrow'])){
                $d = strtotime("+1 day");
                $memorial->update(['call_time'=>date("d.m.Y", $d)]);
            }
            return redirect()->back()->with("message_words_memory", 'Заявка отправлена');
       }else{
            
       }
    }
}