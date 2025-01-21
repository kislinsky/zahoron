<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    protected $guarded =[];

    public function urlImgBefore(){
        return asset('storage/'.$this->imgBefore);
    }

    public function urlImgAfter(){
        return asset('storage/'.$this->imgBefore);
    }
}
