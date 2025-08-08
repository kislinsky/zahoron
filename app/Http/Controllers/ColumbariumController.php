<?php

namespace App\Http\Controllers;

use App\Rules\RecaptchaRule;
use App\Services\Columbarium\ColumbariumService;
use Illuminate\Http\Request;

class ColumbariumController extends Controller
{
    public static function index(){
        return ColumbariumService::index();
    }

    public static function single($slug){
        return ColumbariumService::single($slug);
    }

    public static function addReview(Request $request){
        $data=request()->validate([
            'g-recaptcha-response' => ['required', new RecaptchaRule],
            'columbarium_id'=>['required','integer'],
            'rating'=>['nullable','integer'],
            'content_review'=>['required','string'],
            'name'=>['required','string'],
           ]);
        return ColumbariumService::addReview($data);
    } 
}
