<?php

namespace App\Models;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CategoryProduct;

class ActivityCategoryOrganization extends Model
{
    use HasFactory;
    protected $guarded =[];

    public function organization(){
        return $this->belongsTo(Organization::class);
    }
      
    public function categoryMain(){
        return $this->belongsTo(CategoryProduct::class, 'category_main_id');
    }

    public function categoryProduct(){
        return $this->belongsTo(CategoryProduct::class, 'category_children_id');
    }


    public function categoryProductProvider(){
        return $this->belongsTo(CategoryProductProvider::class, 'category_children_id');
    }

    function city(){
        return $this->belongsTo(City::class);
    }

    function priceHtml(){
        if($this->price==null || $this->price==0){
            return 'Уточняйте';
        }
        return 'от '. $this->price . ' ₽';
    }
    
}

