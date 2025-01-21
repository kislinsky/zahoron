<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewColumbarium extends Model
{
    use HasFactory;
    protected $guarded =[];


    function columbarium(){
        return $this->belongsTo(Columbarium::class);
    }
}