<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceProductPriceList extends Model
{
    use HasFactory;
    protected $guarded =[];


    function city(){
        return $this->belongsTo(City::class);
    }

    function productPriceList(){
        return $this->belongsTo(ProductPriceList::class);
    }

    
}
