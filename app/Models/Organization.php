<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;
    protected $guarded =[];

    public function views(){
        return $this->hasMany(View::class, 'entity_id')->where('entity_type', 'organization');
    }


    function images(){
        return $this->hasMany(ImageOrganization::class);
    }

    function reviews(){
        return $this->hasMany(ReviewsOrganization::class)->orderBy('id','desc')->where('status',1);
    }

    function user(){
        return $this->belongsTo(User::class);
    }
    
    public function city(){
        return $this->belongsTo(City::class);
    }

    public function openOrNot(){
        $day=addHoursAndGetDay($this->time_difference);
        $time=strtotime(addHoursAndGetTime($this->time_difference));
        $get_hours=WorkingHoursOrganization::where('organization_id',$this->id)->where('day',$day)->first();
        if($get_hours!=null){
           if($get_hours->holiday!=1 && $time<strtotime($get_hours->time_end_work) && $time>strtotime($get_hours->time_start_work)){
                return 'Открыто';
           }
        }
        return 'Закрыто';
    }

    public function countReviews(){
        return $this->hasMany(ReviewsOrganization::class)->where('status',1)->count();
    }

    
    public function route(){
        return route('organization.single',$this->slug);
    }

    public function urlImg(){
        if($this->href_img==0){
            return asset('storage/'.$this->img_file);
        }
        return $this->img_url;
    }


    public function urlImgMain(){
        if($this->href_main_img==0){
            return asset('storage/'.$this->img_main_file);
        }
        return $this->img_main_url;
    }

    public function timeEndWorkingNow(){
        $day=addHoursAndGetDay($this->time_difference);
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
        $time=addHoursAndGetTime($this->time_difference);
        return $time;
    }

    function updateRating(){
        $rating=raitingOrganization($this);
        $this->update([
            'rating'=>$rating,
        ]);   
    }

    function ordersNew(){
        return $this->hasMany(OrderProduct::class)->orderBy('id','desc')->where('status',0);
    }

    function ordersCompleted(){
        return $this->hasMany(OrderProduct::class)->orderBy('id','desc')->where('status',2);
    }

    function ordersInWork(){
        return $this->hasMany(OrderProduct::class)->orderBy('id','desc')->where('status',1);
    }
    
    function products(){
        return $this->hasMany(Product::class);
    }

    function workingHours(){
        return $this->hasMany(WorkingHoursOrganization::class);
    }

    function activityCategories(){
        return $this->hasMany(ActivityCategoryOrganization::class);
    }

    function userRequestCount(){
        return $this->hasMany(UserRequestsCount::class);
    }


    function beatifications(){
        return $this->hasMany(Beautification::class);
    }

    function deadAplications(){
        return $this->hasMany(DeadApplication::class);
    }

    function funeralServices(){
        return $this->hasMany(FuneralService::class);
    }

    function memorials(){
        return $this->hasMany(Memorial::class);
    }

    function orderPorducts(){
        return $this->hasMany(OrderProduct::class);
    }
}
