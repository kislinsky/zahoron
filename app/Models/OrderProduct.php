<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory;
    protected $guarded =[];


    function product(){
        return $this->belongsTo(Product::class);
    }

    function additionals(){
        return AdditionProduct::whereIn('id',json_decode($this->additional))->get();
    }

    function user(){
        return $this->belongsTo(User::class);
    }

    function cemetery(){
        return $this->belongsTo(Cemetery::class);
    }

    function mortuary(){
        return $this->belongsTo(Mortuary::class);
    }

    function organization(){
        return $this->belongsTo(Organization::class);
    }
   
}
