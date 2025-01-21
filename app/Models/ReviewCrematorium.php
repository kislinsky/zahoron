<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewCrematorium extends Model
{
    use HasFactory;
    protected $guarded =[];

    function crematorium(){
        return $this->belongsTo(Crematorium::class);
    }
}
