<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeApplication extends Model
{
    use HasFactory;
    protected $guarded =[];


    function typeService(){
        return $this->hasMany(TypeService::class);
    }

    function priceAplication(){
        return $this->hasMany(PriceAplication::class);
    }
}
