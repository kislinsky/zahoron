<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;
    protected $guarded =[];

    public function route(){
        return route('news.single',$this->slug);
    }

    function category(){
        return $this->belongsTo(CategoryNews::class,'category_id');
    }
}
