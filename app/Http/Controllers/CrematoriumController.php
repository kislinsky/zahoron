<?php

namespace App\Http\Controllers;

use App\Rules\RecaptchaRule;
use App\Services\Crematorium\CrematoriumService;
use Illuminate\Http\Request;

class CrematoriumController extends Controller
{
     
    public static function index(){
        return CrematoriumService::index();
    }

    public static function single($slug){
        return CrematoriumService::single($slug);
    }

    public static function addReview(Request $request){
        $data=request()->validate([
            'g-recaptcha-response' => ['required', new RecaptchaRule],
            'crematorium_id'=>['required','integer'],
            'rating'=>['nullable','integer'],
            'content_review'=>['required','string'],
            'name'=>['required','string'],
           ]);
        return CrematoriumService::addReview($data);
    } 
}
