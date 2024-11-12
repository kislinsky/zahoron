<?php

namespace App\Http\Controllers;

use App\Models\Cemetery;
use Illuminate\Http\Request;
use App\Services\Cemetery\CemeteryService;

class CemeteriesController extends Controller
{
    public static function index(){
        return CemeteryService::index();
    }

    public static function singleCemetery($id){
        return CemeteryService::singleCemetery($id);
    }

    public static function ajaxCemetery(Request $request){
        $data=request()->validate([
            'city_id'=>['required','string'],
        ]);
        return CemeteryService::ajaxCemetery($data['city_id']);
    }

    public static function addReview(Request $request){
        
        $data=request()->validate([
            'cemetery_id'=>['required','integer'],
            'rating'=>['nullable','integer'],
            'content_review'=>['required','string'],
            'name'=>['required','string'],
           ]);

        return CemeteryService::addReview($data);
    } 
}
