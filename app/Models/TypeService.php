<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeService extends Model
{
    use HasFactory;
    protected $guarded =[];


    function typeApplication(){
        return $this->belongsTo(TypeApplication::class);
    }

    function priceAplication(){
        return $this->hasMany(PriceAplication::class);
    }
}
