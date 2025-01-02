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
        return Service::whereIn('id',json_decode($this->services_id));
    }


}
