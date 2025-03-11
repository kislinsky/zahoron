<?php

namespace App\Http\Controllers;


use App\Rules\RecaptchaRule;
use App\Services\Burial\FavoriteBurial;
use App\Services\Burial\IndexBurial;
use App\Services\Burial\SearchBurialService;
use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\Http\Request;

class BurialController extends Controller
{

    
    public static function searchProductFilter(Request $request){
        $data=request()->validate([
            'name'=>['required','string'],
            'surname'=>['required','string'],
            'patronymic'=>['required','string'],
            'who'=>['required','string'],
        ]);
        return SearchBurialService::searchProductFilter($data);
    }

    public static function searchProduct(Request $request){
        $data=request()->validate([
            'name'=>['nullable','string'],
            'surname'=>['nullable','string'],
            'patronymic'=>['nullable','string'],
        ]);
        return SearchBurialService::searchProduct($data);
    }

    public static function singleProduct($slug){
        return IndexBurial::singleProduct($slug);
    }

    public static function searchProductRequest(){
        $page=3;

        $seo="Поиск захоронения";

        SEOTools::setTitle($seo);
        SEOTools::setDescription($seo);

        return view('burial.search-big-product',compact('page'));
    }

    public static function searchProductRequestAdd(Request $request){
        $data=request()->validate([
            'g-recaptcha-response' => ['required', new RecaptchaRule],
            'surname'=>['required','string'],
            'name'=>['required','string'],
            'patronymic'=>['required','string'],
            'date_birth'=>['required','date'],
            'date_death'=>['required','date'],
            'location'=>['required','string'],
            'name_customer'=>['required','string'],
            'email_customer'=>['required','email'],
            'phone_customer'=>['required','string'],
        ]);
        return SearchBurialService::searchProductRequestAdd($data);
    }
    
    public static function favoriteAdd($id){
        return FavoriteBurial::favoriteAdd($id);
    }
    
    public static function favoriteDelete($id){
        return FavoriteBurial::favoriteDelete($id);
    }
}
