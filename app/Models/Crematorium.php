<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Crematorium extends Model
{
    use HasFactory;
    protected $guarded =[];

    public function views(){
        return $this->hasMany(View::class, 'entity_id')->where('entity_type', 'crematorium');
    }


    public function city(){
        return $this->belongsTo(City::class);
    }

    public function district(){
        return $this->belongsTo(District::class);
    }

    public function ulWorkingDaysForShema(){    
        return $days=WorkingHoursCrematorium::where('crematorium_id',$this->id)->orderByRaw("FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')")->get();
    }

    public function images(){
        return $this->hasMany(ImageCrematorium::class);
    }

    public function services(){
        return $this->hasMany(ServiceCrematorium::class);
    }


    public function route(){
        return route('crematorium.single',$this->id);
    }

    public function openOrNot(){
        $day=addHoursAndGetDay($this->time_difference);
        $time=strtotime(addHoursAndGetTime($this->time_difference));
        $get_hours=WorkingHoursCrematorium::where('crematorium_id',$this->id)->where('day',$day)->first();
        if($get_hours!=null){
           if($get_hours->holiday!=1 && $time<strtotime($get_hours->time_end_work) && $time>strtotime($get_hours->time_start_work)){
                return 'Открыто';
           }
        }
        return 'Закрыто';
    }

    public function countReviews(){
        return ReviewCrematorium::where('crematorium_id',$this->id)->where('status',1)->count();
    }

    public function urlImg(){
        if($this->href_img==0){
            return asset('storage/'.$this->img_file);
        }
        return $this->img_url;
    }

    public function ulWorkingDays(){    
        $days=WorkingHoursCrematorium::where('crematorium_id',$this->id)->orderByRaw("FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')")->get();
        if($days->count()>0){
            foreach($days as $day){
                $day_now=addHoursAndGetDay($this->time_difference);
                if($day->holiday!=1){
                    $text_day=translateDayOfWeek($day->day).': '."{$day->time_start_work}-{$day->time_end_work}";
                }else{
                    $text_day=translateDayOfWeek($day->day).': Выходной';
                }
                if($day_now==$day->day){
                    echo " <div class='li_working_day text_black li_working_day_active'>{$text_day}</div>";
                }else{
                    echo "<div class='li_working_day text_black'>{$text_day}</div>";
                }
            }
        }else{
            echo 'Не указано';
        }
    }
    public function timeNow(){
        $day=addHoursAndGetDay($this->time_difference);
        $get_hours=WorkingHoursCrematorium::where('crematorium_id',$this->id)->where('day',$day)->first();
        if( $get_hours==null){
            return 'Не указано';
        }
        elseif($get_hours->holiday!=1 ){
            return "{$get_hours->time_start_work}-{$get_hours->time_end_work}";
        }
        return 'Выходной';
    }

    function workingHours(){
        return $this->hasMany(WorkingHoursCrematorium::class);
    }
}
