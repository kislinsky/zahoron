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

    public static function ajaxCityFromEdge($edge_id){
        $cities=City::orderBy('title','asc')->where('edge_id',$edge_id)->get();
        return view('components.city.city-form-edge',compact('cities'));
    }

    public static function ajaxCityInInput($city){
        $cities=City::orderBy('title','asc')->where('title','like',$city.'%')->get();
        return view('components.components_form.cities-input',compact('cities'));
    }


    public static function ajaxCitySearchInInput($data){
        $citites=[];
        if(isset($data['s']) && $data['s']!=null){
            $cities=City::orderBy('title','asc')->where('title','like',$data['s'].'%')->get();
        }
        return view('components.components_form.cities-input-search',compact('cities'));
    }
}