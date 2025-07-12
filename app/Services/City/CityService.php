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
    ->select('cities.*')
    ->join('organizations', 'organizations.city_id', '=', 'cities.id')
    ->join('areas', 'cities.area_id', '=', 'areas.id')
    ->join('edges', 'areas.edge_id', '=', 'edges.id')
    ->where('cities.title', 'like', $city . '%') // Используем начало строки для индекса
    ->where('edges.is_show', 1)
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
    ->select('cities.*')
    ->join('organizations', 'organizations.city_id', '=', 'cities.id')
    ->join('areas', 'cities.area_id', '=', 'areas.id')
    ->join('edges', 'areas.edge_id', '=', 'edges.id')
    ->where('cities.title', 'like', $city . '%') // Используем начало строки для индекса
    ->where('edges.is_show', 1)
    ->groupBy('cities.id')
    ->orderBy('cities.title', 'asc')
    ->get();
        return view('components.components_form.cities-input',compact('cities'));
    }


    public static function ajaxCitySearchInInput($data){
        $cities=[];
        if(isset($data['s']) && $data['s']!=null){
             $cities = DB::table('cities')
            ->select('cities.*')
            ->join('organizations', 'organizations.city_id', '=', 'cities.id')
            ->join('areas', 'cities.area_id', '=', 'areas.id')
            ->join('edges', 'areas.edge_id', '=', 'edges.id')
            ->where('cities.title', 'like', $data['s'] . '%') // Используем начало строки для индекса
            ->where('edges.is_show', 1)
            ->groupBy('cities.id')
            ->orderBy('cities.title', 'asc')
            ->get();
        }
        return view('components.components_form.cities-input-search',compact('cities'));
    }


    public static function ajaxGeo($data){
       if ($data['type_request'] == 'children') {
            if ($data['type_object'] == 'edge') {
                // Для краёв: только те, у которых есть области с городами, где есть кладбища
                $objects = Area::whereHas('cities', function($query) {
                        $query->whereHas('cemeteries');
                    })
                    ->where('edge_id', $data['id'])
                    ->orderBy('title', 'asc')
                    ->get();
                $type = 'area';
            }
            elseif ($data['type_object'] == 'area') {
                // Для областей: только те города, у которых есть кладбища
                $objects = City::whereHas('cemeteries')
                    ->where('area_id', $data['id'])
                    ->orderBy('title', 'asc')
                    ->get();
                $type = 'city';
            }
            elseif ($data['type_object'] == 'city') {
                // Для городов: просто все кладбища (поскольку мы уже фильтровали города)
                $objects = Cemetery::where('city_id', $data['id'])
                    ->orderBy('title', 'asc')
                    ->get();
                $type = 'cemetery';
            }
        }
        else {
            if ($data['type_object'] == 'cemetery') {
                // Получаем родительский город (city) для кладбища
                $city = Cemetery::find($data['id'])->city;
                $parent_id = $city->area_id;
                
                // Получаем города в этой области (area), у которых есть кладбища
                $objects = City::whereHas('cemeteries')
                    ->where('area_id', $parent_id)
                    ->orderBy('title', 'asc')
                    ->get();
                $type = 'city';
            }
            elseif ($data['type_object'] == 'city') {
                // Получаем родительскую область (area) для города
                $area = City::find($data['id'])->area;
                $parent_id = $area->edge_id;
                
                // Получаем области в этом крае (edge), у которых есть города с кладбищами
                $objects = Area::whereHas('cities', function($query) {
                        $query->whereHas('cemeteries');
                    })
                    ->where('edge_id', $parent_id)
                    ->orderBy('title', 'asc')
                    ->get();
                $type = 'area';
            }
            elseif ($data['type_object'] == 'area') {
                // Получаем все края (edge), у которых есть области с городами, где есть кладбища
                $objects = Edge::whereHas('area', function($query) {
                        $query->whereHas('cities', function($query) {
                            $query->whereHas('cemeteries');
                        });
                    })
                    ->orderBy('title', 'asc')
                    ->get();
                $type = 'edge';
            }
        }
        
        return view('components.components_form.ul-location',compact('objects','type'));
    }
}