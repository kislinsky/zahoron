<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Service;
use App\Models\Cemetery;
use App\Models\WordsMemory;
use App\Models\ImageMonument;
use App\Models\ImagePersonal;
use App\Models\LifeStoryBurial;
use Illuminate\Support\Facades\Auth;

class Burial extends Model
{
    use HasFactory;
    protected $guarded =[];

    function route(){
        return route('burial.single',$this->slug);
    }

    function cemetery(){
        return $this->belongsTo(Cemetery::class);
    }

    public function urlImg(){
        if($this->href_img==0){
            return asset('storage/uploads_burials/'.$this->img);
        }
        return $this->img;
    }

    function imageMonument(){
        return $this->hasMany(ImageMonument::class);
    }

    function imagePersonal(){
        return $this->hasMany(ImagePersonal::class);
    }


    function imageMonumentAccept(){
        return $this->hasMany(ImageMonument::class)->where('status',1);
    }

    function imagePersonalAccept(){
        return $this->hasMany(ImagePersonal::class)->where('status',1);
    }

    function services(){
        return Service::orderBy('id', 'desc')->get();
    }

    function lifeStory(){
        return $this->hasMany(LifeStoryBurial::class)->orderBy('id', 'desc');
    }

    function productsNames(){
        return Burial::orderBy('id', 'desc')->where('id','!=',$this->id)->where('name',$this->name)->where('surname',$this->surname)->where('patronymic',$this->patronymic)->where('status',1)->take(5)->get();
    }

    function productsDates(){
        return Burial::orderBy('id', 'desc')->where('id','!=',$this->id)->where('date_death',$this->date_death)->where('status',1)->take(5)->get();
    }

    function wordsMemory(){
        return $this->hasMany(WordsMemory::class);
    }


    function wordsMemoryAccept(){
        return $this->hasMany(WordsMemory::class)->orderBy('id', 'desc')->where('status',1);
    }

    function userHave(){
        if(Auth::check()){
            $order=OrderBurial::where('user_id',user()->id)->where('burial_id',$this->id)->count();
            if($order>0){
                return true;
            }
            return null;
        }
        return null;
    }

}
