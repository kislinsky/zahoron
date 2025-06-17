<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkingHoursMosque extends Model
{
    use HasFactory;
    protected $guarded =[];


    function mosque(){
        return $this->belongsTo(Mosque::class);
    }
}
