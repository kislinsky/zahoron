<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionsВiscountProvider extends Model
{
    use HasFactory;
    protected $guarded =[];

    function organization(){
        return Organization::find($this->organization_id);
    }

    function btnHoursUntilDate() {
        // Создаем объект Carbon для целевой даты и текущей даты
        $target = Carbon::createFromFormat('Y-m-d H:i:s', $this->time_action);
        $now = Carbon::now();
        
        // Считаем разницу в часах
        $hoursRemaining = $now->diffInHours($target);

        // Проверяем, не прошло ли время
        if ($now->greaterThan($target)) {
            return "<div class='red_btn text_center'>Время уже прошло!</div>";
        }
        return "<div class='light_blue_btn text_center'>Осталось <br>$hoursRemaining ч</div>" ;
    }

}
