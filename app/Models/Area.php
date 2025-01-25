<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;
    protected $guarded =[];

    function edge(){
        return $this->belongsTo(Edge::class);
    }

    function cemetery(){
        return $this->hasMnay(Cemetery::class);
    }

    function city(){
        return $this->hasMnay(City::class);
    }

}
