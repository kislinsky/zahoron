<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Acf extends Model
{
    use HasFactory;
    protected $guarded =[];

    function page(){
        return $this->belongsTo(Page::class);
    }

}
