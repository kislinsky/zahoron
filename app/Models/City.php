<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Cemetery;
use App\Models\Mortuary;

class City extends Model
{
    use HasFactory;
    protected $guarded =[];

    public function cityOrganizations(){
        return $oganizations=Organization::where('city_id',$this->id)->where('role','organization')->get();
    }

    public function cemeteries(){
        return Cemetery::where('city_id',$this->id)->get();
    }

    public function mortuaries(){
        return Mortuary::where('city_id',$this->id)->get();
    }

    public function districts(){
        return District::where('city_id',$this->id)->get();
    }

    public function route(){
        return route('city.select',$this->id);
    }

    public function edgeCities(){
        return City::orderBy('title','asc')->where('edge_id',$this->edge_id)->get();
    }


    
  
}
