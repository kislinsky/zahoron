<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryNews extends Model
{
    use HasFactory;
    protected $guarded =[];

    function news(){
        return $this->hasMany(News::class,'category_id');
    }
}
