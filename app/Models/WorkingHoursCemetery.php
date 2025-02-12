<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkingHoursCemetery extends Model
{
    use HasFactory;
    protected $guarded =[];


    function cemetery(){
        return $this->belongsTo(Cemetery::class);
    }
}
