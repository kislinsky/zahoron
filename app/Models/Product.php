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
        return $this->hasMany(ImageProduct::class);
    }

    public function getParam(){
        return $this->hasMany(ProductParameters::class);
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
        return $this->belongsTo(District::class);
    }

    public function memorialMenu(){
        return $this->hasMany(MemorialMenu::class);
    }

    public function organization(){
        return $this->belongsTo(Organization::class);
    }

    public function reviews(){
        return  CommentProduct::where('product_id',$this->id)->where('status',1)->get();
    }


    public function route(){
        return route('product.single',$this->slug);
    }

    function updateRating(){
        $rating=raitingProduct($this);
        $this->update([
            'rating'=>$rating,
        ]);   
    }

   
}
