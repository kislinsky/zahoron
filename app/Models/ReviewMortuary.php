<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewMortuary extends Model
{
    use HasFactory;
    protected $guarded =[];


    function mortuary(){
        return $this->belongsTo(Mortuary::class);
    }
}
