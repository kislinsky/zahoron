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
        if($this->href_img!=1){
            return asset('storage/'.$this->title);
        }
        return $this->title;
    }

}
