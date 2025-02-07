<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AdvantagesProductPriceList;
use App\Models\AdviceProductPriceList;
use App\Models\City;
use App\Models\FaqProductPriceList;
use App\Models\ImageProductPriceList;
use App\Models\ReviewProductPriceList;
use App\Models\StageProductPriceList;
use App\Models\VariantProductPriceList;



class ProductPriceList extends Model
{
    use HasFactory;
    protected $guarded =[];

    function advices(){
        return $this->hasMany(AdviceProductPriceList::class);
    }

    function reviews(){
        return $this->hasMany(ReviewProductPriceList::class);
    }

    function variants(){
        return $this->hasMany(VariantProductPriceList::class);
    }

    function faqs(){
        return $this->hasMany(FaqProductPriceList::class);
    }

    function stages(){
        return $this->hasMany(StageProductPriceList::class);
    }

    function imgsService(){
        return $this->hasMany(ImageProductPriceList::class);
    }

    function advantages(){
        return $this->hasMany(AdvantagesProductPriceList::class);
    }

    function priceProductPriceList(){
        return $this->hasMany(PriceProductPriceList::class);
    }

    // В модели Service
    public function getPriceForCity($cityId)
    {
        return $this->priceProductPriceList->where('city_id', $cityId)->first();
    }


    function category(){
        return $this->belongsTo(CategoryProductPriceList::class);
    }
}
