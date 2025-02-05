<?php

namespace App\Http\Controllers;

use App\Models\City;
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
        ]);
        return CityService::ajaxCity($data['city_id']);
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

}
