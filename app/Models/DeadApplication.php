<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeadApplication extends Model
{
    use HasFactory;
    protected $guarded =[];


    function city(){
        return $this->belongsTo(City::class);
    }

    function user(){
        return $this->belongsTo(User::class);
    }

    function mortuary(){
        return $this->belongsTo(Mortuary::class);
    }


    function organization(){
        return $this->belongsTo(Organization::class);
    }
    
    function changeStatus($status){
        if($this->status==0 && $this->organization==null){
            $this->update(['status'=>$status]);
        }
    }

    function timeEnd(){
        if($this->call_time!=null){
            $timeFormatRegex = '/^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/';
            // Проверяем, соответствует ли строка регулярному выражению
            if (preg_match($timeFormatRegex, $this->call_time)) {
                $time = Carbon::createFromFormat('H:i', $this->call_time);
                // Добавляем 30 минут
                $time->addMinutes(30);
                // Возвращаем новое время в формате "H:i"
                return $time->format('H:i');
            } else {
                return $this->call_time;
            }  
        }
    }
}
