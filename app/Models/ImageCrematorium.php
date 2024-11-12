<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageCrematorium extends Model
{
    use HasFactory;
    protected $guarded =[];

    public function urlImg(){
        if($this->href_img==0){
            return asset('storage/uploads_crematorium/'.$this->title);
        }
        return $this->title;
    }
}
