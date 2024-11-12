<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ImageCatPriceList;
use App\Models\FaqCategoryPriceList;
use App\Models\ProductPriceList;

class CategoryProductPriceList extends Model
{
    use HasFactory;
    protected $guarded =[];

    function ourWorks(){
        return ImageCatPriceList::orderBy('id','desc')->where('category_id',$this->id)->get();
    }

    function faqs(){
        return FaqCategoryPriceList::orderBy('id','desc')->where('category_id',$this->id)->get();
    }

    function services(){
        return ProductPriceList::orderBy('id', 'desc')->where('category_id',$this->id)->get();
    }


    function route(){
        return route('service.category',$this->slug);
    }
}
