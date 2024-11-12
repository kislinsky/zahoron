<?php

namespace App\Http\Controllers;

use App\Services\Mortuary\MortuaryService;
use Illuminate\Http\Request;

class MortuaryController extends Controller
{

    public static function index(){
        return MortuaryService::index();
    }

    public static function ajaxMortuary(Request $request){
        $data=request()->validate([
            'city_id'=>['required'],
        ]);
        return MortuaryService::ajaxMortuary($data['city_id']);
    }
    
    public static function single($id){
        return MortuaryService::single($id);
    }
   
    public static function addReview(Request $request){
        
        $data=request()->validate([
            'mortuary_id'=>['required','integer'],
            'rating'=>['nullable','integer'],
            'content_review'=>['required','string'],
            'name'=>['required','string'],
           ]);

        return MortuaryService::addReview($data);
    } 
}
