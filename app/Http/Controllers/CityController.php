<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Cemetery;
use App\Models\City;
use App\Models\Edge;
use Illuminate\Http\Request;
use App\Services\City\CityService;

class CityController extends Controller
{
    public static function selectCity($id){
        return CityService::selectCity($id);
    }

    public static function ajaxCity(Request $request){
        $data=request()->validate([
            'city_id'=>['required','string'],
            'url'=>['required','string'],
        ]);
        return CityService::ajaxCity($data);
    }

    public static function ajaxCityFromEdge(Request $request){
        $data=request()->validate([
            'edge_id'=>['required','string'],
        ]);
        return CityService::ajaxCityFromEdge($data['edge_id']);
    }

    public static function ajaxCityInInput(Request $request){
        $data=request()->validate([
            's'=>['required','string'],
        ]);
        return CityService::ajaxCityInInput($data['s']);
    }


    public static function ajaxCitySearchInInput(Request $request){
        $data=request()->validate([
            's'=>['nullable','string'],
        ]);
        return CityService::ajaxCitySearchInInput($data);
    }


    public function index(Request $request)
    {
        $city = $request->get('city'); // Текущий город доступен через middleware
        redirect()->back();

    }

    public function changeCity(Request $request, $currentCity, $selectedCity)
    {
        // Проверяем, что выбранный город существует в системе
        $city = City::where('slug', $selectedCity)->first();


        if (!$city) {
            abort(404, 'City not found');
        }

        setcookie("city", $city->id, time()+20*24*60*60,'/');

        // Перенаправляем пользователя на новый префикс
        return redirect()->route('city.index', ['city' => $city->slug]);
    }

    public static function ajaxGeo(Request $request){
        $data=request()->validate([
            'type_object'=>['required','string'],
            'id'=>['required','integer'],
            'type_request'=>['required','string'],
        ]);
        return CityService::ajaxGeo($data);
    }

    public function ajaxGetEdges() {
        return CityService::getEdgesForSelector();
    }

    public function ajaxGetAreas(Request $request) {
        $data = $request->validate([
            'edge_id' => ['required', 'integer'],
            'selected_cemetery_ids' => ['nullable', 'string']
        ]);

        $selectedCemeteryIds = array_filter(explode(',', $data['selected_cemetery_ids']), 'is_numeric');

        return CityService::getAreasForSelector($data['edge_id'], $selectedCemeteryIds);
    }

    public function ajaxGetCemeteries(Request $request) {
        $data = $request->validate(['area_id' => ['required', 'integer']]);
        return CityService::getCemeteriesForSelector($data['area_id']);
    }

}
