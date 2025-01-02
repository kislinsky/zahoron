<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Organization;

class Cemetery extends Model
{
    use HasFactory;
    protected $guarded =[];


    public function city(){
        return $this->belongsTo(City::class);
    }

    public function district(){
        return $this->belongsTo(District::class);
    }

    public function cemeteryOrganiaztions(){
        $organiazations=Organization::where('role','organization')->where(function($item) {
            $item->orWhere('cemetery_ids',"LIKE", "%,".$this->id.",%")->orWhere('cemetery_ids',"LIKE", $this->id.",%")->orWhere('cemetery_ids',"LIKE", "%,".$this->id);
        })->get();
        return $organiazations;
    }

    public function route(){
        return route('cemeteries.single',$this->id);
    }

    public function openOrNot(){
        //$day=getTimeByCoordinates($this->width,$this->longitude)['dayOfTheWeek'];
        // $time=getTimeByCoordinates($this->width,$this->longitude)['current_time'];
        $time=strtotime('23:00');
        $day='Saturday';
        $get_hours=WorkingHoursCemetery::where('cemetery_id',$this->id)->where('day',$day)->first();
        if($get_hours!=null){
           if($get_hours->holiday!=1 && $time<strtotime($get_hours->time_end_work) && $time>strtotime($get_hours->time_start_work)){
                return 'Открыто';
           }
        }
        return 'Закрыто';
    }


    public function timeNow(){
        //$day=getTimeByCoordinates($this->width,$this->longitude)['dayOfTheWeek'];
        $day='Tuesday';
        $get_hours=WorkingHoursCemetery::where('cemetery_id',$this->id)->where('day',$day)->first();
        if( $get_hours==null){
            return 'Не указано';
        }
        elseif($get_hours->holiday!=1 ){
            return "{$get_hours->time_start_work}-{$get_hours->time_end_work}";
        }
        return 'Выходной';
    }

    public function countReviews(){
        return ReviewCemetery::where('cemetery_id',$this->id)->where('status',1)->count();
    }

    public function urlImg(){
        if($this->href_img==0){
            return asset('storage/uploads_cemeteries/'.$this->img);
        }
        return $this->img;
    }
    
    public function ulWorkingDays(){    
        $days=WorkingHoursCemetery::where('cemetery_id',$this->id)->orderByRaw("FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')")->get();
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

    public function updateRating(){
        $rating=ReviewCemetery::where('cemetery_id',$this->id)->get();
        $rating=round( $rating->sum('rating')/$rating->count(),2);
        $this->update([
            'rating'=>$rating,
        ]);
        return $this->rating;
    }
   


}
