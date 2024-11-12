<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Crematorium extends Model
{
    use HasFactory;
    protected $guarded =[];

    public function city(){
        $cemetery=City::find($this->city_id);
        return $cemetery;
    }
    
    public function district(){
        $district=District::find($this->district_id);
        return $district;
    }

    public function route(){
        return route('crematorium.single',$this->id);
    }

    public function openOrNot(){
        //$day=strtotime(getTimeByCoordinates($this->width,$this->longitude)['dayOfTheWeek']);
        // $time=strtotime(getTimeByCoordinates($this->width,$this->longitude)['current_time']);
        $time=strtotime('23:00');
        $day='Saturday';
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
            return asset('storage/uploads_crematorium/'.$this->img);
        }
        return $this->img;
    }

    public function ulWorkingDays(){    
        $days=WorkingHoursCrematorium::where('crematorium_id',$this->id)->orderByRaw("FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')")->get();
        if($days->count()>0){
            foreach($days as $day){
                //$day_now=strtotime(getTimeByCoordinates($this->width,$this->longitude)['dayOfTheWeek']);
                $day_now='Tuesday';
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
        //$day=strtotime(getTimeByCoordinates($this->width,$this->longitude)['dayOfTheWeek']);
        $day='Tuesday';
        $get_hours=WorkingHoursCrematorium::where('crematorium_id',$this->id)->where('day',$day)->first();
        if( $get_hours==null){
            return 'Не указано';
        }
        elseif($get_hours->holiday!=1 ){
            return "{$get_hours->time_start_work}-{$get_hours->time_end_work}";
        }
        return 'Выходной';
    }
}