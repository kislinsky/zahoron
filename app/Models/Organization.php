<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;
    protected $guarded =[];

    
    public function city(){
        return $this->belongsTo(City::class);
    }

    public function openOrNot(){
        //$day=getTimeByCoordinates($this->width,$this->longitude)['dayOfTheWeek'];
        // $time=strtotime(getTimeByCoordinates($this->width,$this->longitude)['current_time']);
        $time=strtotime('12:00');
        $day='Sunday';
        $get_hours=WorkingHoursOrganization::where('organization_id',$this->id)->where('day',$day)->first();
        if($get_hours!=null){
           if($get_hours->holiday!=1 && $time<strtotime($get_hours->time_end_work) && $time>strtotime($get_hours->time_start_work)){
                return 'Открыто';
           }
        }
        return 'Закрыто';
    }

    public function countReviews(){
        return ReviewsOrganization::where('organization_id',$this->id)->where('status',1)->count();
    }

    public function route(){
        return route('organization.single',$this->slug);
    }

    public function urlImg(){
        if($this->href_img==0){
            return asset('storage/uploads_organization/'.$this->logo);
        }
        return $this->logo;
    }

    public function timeEndWorkingNow(){
        //$day=getTimeByCoordinates($this->width,$this->longitude)['dayOfTheWeek'];
        $day='Sunday';
        $get_hours=WorkingHoursOrganization::where('organization_id',$this->id)->where('day',$day)->first();
        if($get_hours!=null){
            if($get_hours->holiday!=1){
                return "Открыто до {$get_hours->time_end_work}";
            }
            return 'Выходной';
        }
        return 'Не указано';
    }


    public function ulWorkingDays(){    
        $days=WorkingHoursOrganization::where('organization_id',$this->id)->orderByRaw("FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')")->get();
        if($days->count()>0){
            foreach($days as $day){
                //$day_now=getTimeByCoordinates($this->width,$this->longitude)['dayOfTheWeek'];
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
        //$day=getTimeByCoordinates($this->width,$this->longitude)['dayOfTheWeek'];
        $day='Tuesday';
        $get_hours=WorkingHoursOrganization::where('organization_id',$this->id)->where('day',$day)->first();
        
        if( $get_hours==null){
            return 'Не указано';
        }
        elseif($get_hours->holiday!=1 ){
            return "{$get_hours->time_start_work}-{$get_hours->time_end_work}";
        }
        return 'Выходной';
    }

    public function timeCity(){
        //$time=getTimeByCoordinates($this->width,$this->longitude)['current_time'];
        $time='23:40';
        return $time;
    }

    function updateRating(){
        $rating=raitingOrganization($this);
        $this->update([
            'rating'=>$rating,
        ]);   
    }

    function ordersNew(){
        return $this->hasMany(OrderProduct::class)->orderBy('id','desc')->where('status',1);
    }

    function ordersCompleted(){
        return $this->hasMany(OrderProduct::class)->orderBy('id','desc')->where('status',2);
    }
    
}
