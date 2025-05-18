<?php

namespace App\Services\City;



use App\Models\Area;
use App\Models\Cemetery;
use App\Models\City;
use App\Models\Edge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;



class CityService {
    
    public static function selectCity($id){
        $city=City::findOrFail($id);
        $new_url = insert_city_into_url(url()->previous(), $city->slug);
        return redirect($new_url);
        
    }

    public static function ajaxCity($city){
        $cities = DB::table('cities')
            ->join('organizations', 'organizations.city_id', '=', 'cities.id')
            ->where('cities.title', 'like', '%' . $city . '%')
            ->select('cities.*')
            ->groupBy('cities.id')
            ->orderBy('cities.title', 'asc')
            ->get();
        return view('components.components_form.cities',compact('cities'));
    }

    public static function ajaxCityFromEdge($edge_id){
        $cities=City::orderBy('title','asc')->where('edge_id',$edge_id)->get();
        return view('components.city.city-form-edge',compact('cities'));
    }

    public static function ajaxCityInInput($city){
         $cities = DB::table('cities')
            ->join('organizations', 'organizations.city_id', '=', 'cities.id')
            ->where('cities.title', 'like', '%' . $city . '%')
            ->select('cities.*')
            ->groupBy('cities.id')
            ->orderBy('cities.title', 'asc')
            ->get();
        return view('components.components_form.cities-input',compact('cities'));
    }


    public static function ajaxCitySearchInInput($data){
        $cities=[];
        if(isset($data['s']) && $data['s']!=null){
              $cities = DB::table('cities')
            ->join('organizations', 'organizations.city_id', '=', 'cities.id')
            ->where('cities.title', 'like', '%' . $data['s'] . '%')
            ->select('cities.*')
            ->groupBy('cities.id')
            ->orderBy('cities.title', 'asc')
            ->get();
        }
        return view('components.components_form.cities-input-search',compact('cities'));
    }


    public static function ajaxGeo($data){
        if($data['type_request']=='children'){
            if($data['type_object']=='edge'){
                $objects=Area::where('edge_id',$data['id'])->orderBy('title','asc')->get();
                $type='area';
            }
            if($data['type_object']=='area'){
                $objects=City::where('area_id',$data['id'])->orderBy('title','asc')->get();
                $type='city';
            }
            if($data['type_object']=='city'){
                $objects=Cemetery::where('city_id',$data['id'])->orderBy('title','asc')->get();
                $type='cemetery';
            }
        }
        else{
            if($data['type_object']=='cemetery'){
                $parent_id=Cemetery::find($data['id'])->city->area_id;
                $objects=City::where('area_id',$parent_id)->orderBy('title','asc')->get();
                $type='city';
            }
            if($data['type_object']=='city'){
                $parent_id=City::find($data['id'])->area->edge_id;
                $objects=Area::where('edge_id',$parent_id)->orderBy('title','asc')->get();
                $type='area';
            }
            if($data['type_object']=='area'){
                $objects=Edge::orderBy('title','asc')->get();
                $type='edge';
            }
        }
        
        return view('components.components_form.ul-location',compact('objects','type'));
    }
}