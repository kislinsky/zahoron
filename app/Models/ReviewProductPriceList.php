<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewProductPriceList extends Model
{
    use HasFactory;

    public function urlImgBefore(){
        return asset('storage/'.$this->img_before);
    }

    public function urlImgAfter(){
        return asset('storage/'.$this->img_after);
    }

    function user(){
        return $this->belongsTo(User::class);
    }

    function product_price_lists(){
        return $this->belongsTo(ProductPriceList::class);
    }
}
