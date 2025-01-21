<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryProductProvider extends Model
{
    use HasFactory;
    
    function priceCategoryOrganization($organization){
        $price_category=ActivityCategoryOrganization::where('organization_id',$organization->id)->where('category_children_id',$this->id)->get()->first()->price;
        return $price_category;
    }

    public function parent(){
        return $this->belongsTo(CategoryProductProvider::class, 'parent_id');
    }

    // Отношение к дочерним категориям
    public function children(){
        return $this->hasMany(CategoryProductProvider::class, 'parent_id');
    }
}
