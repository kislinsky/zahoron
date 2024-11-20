<?php

namespace App\Models;

use App\Models\ImageProduct;
use App\Models\ProductParameters;
use App\Models\CategoryProduct;
use App\Models\District;
use App\Models\MemorialMenu;
use App\Models\CommentProduct;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $guarded =[];

    public function getImages(){
        return ImageProduct::where("product_id", $this->id)->get();
    }

    public function getParam(){
        return ProductParameters::where('product_id',$this->id)->get();
    }

    public function category(){
        return CategoryProduct::find($this->category_id);
    }

    public function parentCategory(){
        $cat= CategoryProduct::find($this->category_id);
        $parent_cat=CategoryProduct::find($cat->parent_id);
        return $parent_cat;
    }
    
    

    public function district(){
        return District::find($this->district_id);
    }

    public function memorialMenu(){
        return  MemorialMenu::where('product_id',$this->id)->get();
    }

    public function organization(){
        return  Organization::find($this->organization_id);
    }

    public function reviews(){
        return  CommentProduct::where('product_id',$this->id)->where('status',1)->get();
    }


    public function route(){
        return route('product.single',$this->slug);
    }

   
}
