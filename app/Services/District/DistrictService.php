<?php

namespace App\Services\District;



use App\Models\City;
use App\Models\District;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



class DistrictService {

    public static function ajaxDistrict($city){
        $districts=District::orderBy('title','asc')->where('city_id',$city)->get();
        return view('components.components_form.district',compact('districts'));
    }
}