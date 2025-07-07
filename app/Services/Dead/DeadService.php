<?php

namespace App\Services\Dead;

use App\Models\DeadApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeadService {
    
    public static function deadAdd($data){
        if(Auth::check()){
            $user=Auth::user();
        }else{
            $user=createUserWithPhone($data['phone_dead'],$data['name_dead']);
        }

        $dead=DeadApplication::create([
            'city_id'=>$data['city_dead'] ,
            'fio'=>$data['fio_dead'] ,
            'user_id'=> $user->id,
            ]);
            if(isset($data['call_time'])){
                if($data['call_time']!=null){
                    $dead->update(['call_time'=>$data['call_time']]);
                }
            }
            if(isset($data['mortuary_dead'])){
                if($data['mortuary_dead']!=null){
                    $dead->update(['mortuary_id'=>$data['mortuary_dead']]);
                }
            }
            if(isset($data['call_tomorrow'])){
                $d = strtotime("+1 day");
                $dead->update(['call_time'=>date("d.m.Y", $d)]);
            }

            sendMessage('soobshhenie-pri-zaiavke-pop-up-umersii',['name'=>$user->name],$user);

        return redirect()->back()->with("message_words_memory", 'Заявка отправлена');
    }
}