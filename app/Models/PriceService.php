<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceService extends Model
{
    use HasFactory;
    protected $guarded =[];

    function cemetery(){
        return $this->belongsTo(Cemetery::class);
    }

    function service(){
        return $this->belongsTo(Service::class);
    }

}
