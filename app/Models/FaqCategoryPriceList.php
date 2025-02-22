<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaqCategoryPriceList extends Model
{
    use HasFactory;
    protected $guarded =[];

    function category(){
        return $this->belongsTo(CategoryProductPriceList::class,'category_id');
    }

}
