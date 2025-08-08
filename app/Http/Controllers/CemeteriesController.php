<?php

namespace App\Http\Controllers;

use App\Models\Cemetery;
use App\Rules\RecaptchaRule;
use App\Services\Cemetery\CemeteryService;
use Illuminate\Http\Request;

class CemeteriesController extends Controller
{
    public static function index(){
        return CemeteryService::index();
    }

    public static function singleCemetery($slug){
        return CemeteryService::singleCemetery($slug);
    }

    public static function ajaxCemetery(Request $request){
        $data=request()->validate([
            'city_id'=>['required','string'],
        ]);
        return CemeteryService::ajaxCemetery($data['city_id']);
    }

    public static function addReview(Request $request){
        
        $data=request()->validate([
            'g-recaptcha-response' => ['required', new RecaptchaRule],
            'cemetery_id'=>['required','integer'],
            'rating'=>['nullable','integer'],
            'content_review'=>['required','string'],
            'name'=>['required','string'],
           ]);

        return CemeteryService::addReview($data);
    } 
}
