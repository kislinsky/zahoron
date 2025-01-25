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
        return $this->hasMany(Organization::class);
    }

    public function cemeteries(){
        return $this->hasMany(Cemetery::class);
        // return Cemetery::where('city_id',$this->id)->get();
    }

    public function mortuaries(){
        return $this->hasMany(Mortuary::class);
        // return Mortuary::where('city_id',$this->id)->get();
    }

    function edge(){
        return $this->belongsTo(Edge::class);
    }

    function area(){
        return $this->belongsTo(Area::class);
    }

    public function districts(){
        return $this->hasMany(District::class);
        // return District::where('city_id',$this->id)->get();
    }

    public function route(){
        return route('city.select',$this->id);
    }

    public function edgeCities(){
        return City::orderBy('title','asc')->where('edge_id',$this->edge_id)->get();
    }

    function priceProductPriceList(){
        return $this->hasMany(PriceProductPriceList::class);
    }

    
  
}
