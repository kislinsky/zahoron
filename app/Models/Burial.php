<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Service;
use App\Models\WordsMemory;
use App\Models\ImageMonument;
use App\Models\ImagePersonal;
use App\Models\LifeStoryBurial;
class Burial extends Model
{
    use HasFactory;
    protected $guarded =[];

    function route(){
        return route('burial.single',$this->slug);
    }

    function cemetery(){
        return Cemetery::find($this->cemetery_id);
    }


    function imagesMonument(){
        return ImageMonument::where('burial_id',$this->id)->where('status',1)->get();
    }

    function imagesPersonal(){
        return ImagePersonal::where('burial_id',$this->id)->where('status',1)->get();
    }

    function services(){
        return Service::orderBy('id', 'desc')->where('cemetery_id', $this->cemetery_id)->get();
    }

    function lifeStory(){
        return LifeStoryBurial::orderBy('id', 'desc')->where('burial_id',$this->id)->get();
    }

    function productsNames(){
        return Burial::orderBy('id', 'desc')->where('id','!=',$this->id)->where('name',$this->name)->where('surname',$this->surname)->where('patronymic',$this->patronymic)->where('status',1)->take(5)->get();
    }

    function productsDates(){
        return Burial::orderBy('id', 'desc')->where('id','!=',$this->id)->where('date_death',$this->date_death)->where('status',1)->take(5)->get();
    }

    function memoryWords(){
        return WordsMemory::orderBy('id', 'desc')->where('product_id',$this->id)->where('status',1)->get();
    }

}
