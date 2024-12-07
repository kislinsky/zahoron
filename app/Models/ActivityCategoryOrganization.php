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
        // return Organization::find($this->organization_id);
    }

    public function categoryProduct(){
        return CategoryProduct::find($this->category_children_id);
    }

    public function categoryProductProvider(){
        return CategoryProductProvider::find($this->category_children_id);
    }
}

