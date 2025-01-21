<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImagePersonal extends Model
{
    use HasFactory;
    protected $guarded =[];

    public function burial()
    {
        return $this->belongsTo(Burial::class);
    }

    public function urlImg(){
        return asset('storage/'.$this->title);
    }

}
