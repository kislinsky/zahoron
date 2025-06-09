<?php

namespace App\Http\Controllers;

use App\Rules\RecaptchaRule;
use App\Services\Church\ChurchService;
use Illuminate\Http\Request;

class ChurchController extends Controller
{
    public static function index(){
        return ChurchService::index();
    }

    
    public static function single($id){
        return ChurchService::single($id);
    }

    public static function addReview(Request $request){
        
        $data=request()->validate([
            'g-recaptcha-response' => ['required', new RecaptchaRule],
            'church_id'=>['required','integer'],
            'rating'=>['nullable','integer'],
            'content_review'=>['required','string'],
            'name'=>['required','string'],
           ]);
        return ChurchService::addReview($data);
    } 

}
