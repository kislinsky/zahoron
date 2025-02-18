<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeService extends Model
{
    use HasFactory;
    protected $guarded =[];


    function typeApplication(){
        return $this->belongsTo(TypeApplication::class);
    }

    function priceAplication(){
        return $this->hasMany(PriceAplication::class);
    }

    function count(){
        $organization=user()->organization();
        if($organization!=null){
            $service_count=UserRequestsCount::where('organization_id',$organization->id)->where('type_service_id',$this->id)->get();
            if(isset($service_count[0])){
                return $service_count->first()->count;
            }
        }
        return 0;
    }

    function updateCount($count){
        $organization=user()->organization();
        if($organization!=null){
            $service_count=UserRequestsCount::where('organization_id',$organization->id)->where('type_service_id',$this->id)->get();
            if(isset($service_count[0])){
                return $service_count->first()->update(['count'=>$count]);
            }
        }
        return null;
    }

}
