<?php

namespace App\Models;

use App\Models\CategoryProduct;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

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

// В моделях
protected static function booted()
{
    static::saved(function ($model) {
        if ($model instanceof Organization || $model instanceof ActivityCategoryOrganization) {
            $organization = $model instanceof Organization ? $model : $model->organization;
            
            if ($organization && $organization->city) {
                $cityId = $organization->city->id;
                
                // Очищаем все связанные кэши
                Cache::forget("halls_commemorations_full_{$cityId}_v3");
                Cache::forget("funeral_agencies_full_{$cityId}_v3");
                Cache::forget("uneral_bureaus_full_{$cityId}_v3");
                
                // Очищаем кэш вспомогательной функции
                $categories = [
                    [46],
                    [32, 33, 34],
                    [29, 30, 39]
                ];
                
                foreach ($categories as $categoryIds) {
                    $key = 'orgs_full_' . implode('_', $categoryIds) . '_city_' . $cityId;
                    Cache::forget($key);
                    Cache::forget($key . '_count_3');
                }
            }
        }
    });
}
    
}

