<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRequestsCount extends Model
{
    use HasFactory;
    protected $guarded =[];


    function typeApplication(){
        return $this->belongsTo(TypeApplication::class);
    }

    function typeService(){
        return $this->belongsTo(TypeService::class);
    }
}
