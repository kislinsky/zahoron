<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoObject extends Model
{
    use HasFactory;
    protected $guarded =[];

    function SEO(){
        return $this->hasMany(SEO::class);
    }

}
