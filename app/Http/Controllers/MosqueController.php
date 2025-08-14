<?php

namespace App\Http\Controllers;

use App\Rules\RecaptchaRule;
use App\Services\Mosque\MosqueService;
use Illuminate\Http\Request;

class MosqueController extends Controller
{
   public static function index(){
    return abort('404');
        return MosqueService::index();
    }

    public static function single($slug){
        return abort('404');
        return MosqueService::single($slug);
    }

    public static function addReview(Request $request){
        $data=request()->validate([
            'g-recaptcha-response' => ['required', new RecaptchaRule],
            'mosque_id'=>['required','integer'],
            'rating'=>['nullable','integer'],
            'content_review'=>['required','string'],
            'name'=>['required','string'],
           ]);
        return MosqueService::addReview($data);
    } 
}
