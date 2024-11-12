<?php

namespace App\Services\City;



use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



class CityService {
    public static function selectCity($id){
        $city=City::findOrFail($id);
        $new_url = insert_city_into_url(url()->previous(), $city->slug);
        return redirect($new_url);
        
    }

    public static function ajaxCity($city){
        $cities=City::orderBy('title','asc')->where('title','like',$city.'%')->get();
        return view('components.components_form.cities',compact('cities'));
    }

    
}