<?php

namespace App\Models;

use App\Models\ActivityCategoryOrganization;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; 

class CategoryProduct extends Model
{
    use HasFactory;
    protected $guarded =[];

    function priceCategoryOrganization($organization){
        $price_category=ActivityCategoryOrganization::where('organization_id',$organization->id)->where('category_children_id',$this->id)->get()->first()->price;
        return $price_category;
    }

    public function parent(){
        return $this->belongsTo(CategoryProduct::class, 'parent_id');
    }

    // Отношение к дочерним категориям
    public function children(){
        return $this->hasMany(CategoryProduct::class, 'parent_id');
    }

    public  function routeCatalog(){
        return route('organizations.category', $this->slug);
    }
    
    public function tags(): HasMany // Теперь HasMany будет найден
    {
        return $this->hasMany(Tag::class, 'entity_id')
            ->where('entity_type', 'category_product');
    }
}