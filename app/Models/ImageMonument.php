<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageMonument extends Model
{
    use HasFactory;
    protected $guarded =[];

    public function burial()
    {
        return $this->belongsTo(Burial::class);
    }

    public function urlImg(){
        if($this->href_img==0){
            return asset('storage/'.$this->img_file);
        }
        return asset($this->img_url);
    }

}
