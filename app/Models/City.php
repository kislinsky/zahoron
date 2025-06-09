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


    public function churches(){
        return $this->hasMany(Church::class);
    }

    public function mosques(){
        return $this->hasMany(Mosque::class);
    }

    public function cemeteries(){
        return $this->hasMany(Cemetery::class);
    }

    public function mortuaries(){
        return $this->hasMany(Mortuary::class);
    }

    public function columbariums(){
        return $this->hasMany(Columbarium::class);
    }

    public function crematoriums(){
        return $this->hasMany(Crematorium::class);
    }

    function edge(){
        return $this->belongsTo(Edge::class);
    }

    function area(){
        return $this->belongsTo(Area::class);
    }

    public function districts(){
        return $this->hasMany(District::class);
    }

    public function organizations(){
        return $this->hasMany(Organization::class);
    }


    public function route(){
        return route('city.select',$this->id);
    }

    public function edgeCities($nameObject){
        return $cities=City::orderBy('title','asc')->where('edge_id', selectCity()->edge_id)->whereHas($nameObject)->get();
    }

    function priceProductPriceList(){
        return $this->hasMany(PriceProductPriceList::class);
    }


    function users(){
        return $this->hasMany(User::class);
    }
    
  
}
