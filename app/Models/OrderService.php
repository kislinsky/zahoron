<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderService extends Model
{
    use HasFactory;
    protected $guarded =[];
    
    function burial(){
        return $this->belongsTo(Burial::class);
    }

    function services(){
        return Service::whereIn('id',json_decode($this->services_id))->get();
    }

    function priceForAgent(){
        $procent=get_acf(13,'procent');
        return $this->price-$this->burial->cemetery->price_burial_location-($this->price*$procent)/100;
    }


}
