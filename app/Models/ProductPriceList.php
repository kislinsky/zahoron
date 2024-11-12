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
        return AdviceProductPriceList::orderBy('id','asc')->where('product_id',$this->id)->get();
    }

    function reviews(){
        return ReviewProductPriceList::orderBy('id','asc')->where('product_id',$this->id)->get();
    }

    function variants(){
        return VariantProductPriceList::orderBy('id','asc')->where('product_id',$this->id)->get();
    }

    function city(){
        return City::findOrFail($this->city_id);
    }

    function faqs(){
        return FaqProductPriceList::orderBy('id','asc')->where('product_id',$this->id)->get();
    }

    function stages(){
        return StageProductPriceList::orderBy('id','asc')->where('product_id',$this->id)->get();
    }

    function imgsService(){
        return ImageProductPriceList::orderBy('id','desc')->where('product_id',$this->id)->get();
    }

    function advantages(){
        return AdvantagesProductPriceList::orderBy('id','desc')->where('product_id',$this->id)->get();
    }

    


}
