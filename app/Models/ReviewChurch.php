<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewChurch extends Model
{
    use HasFactory;
    protected $guarded = [];

    function church(){
        return $this->belongsTo(Church::class);
    }

}
