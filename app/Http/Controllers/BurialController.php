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

    public static function searchBurialResult(Request $request){
        $data=request()->validate([
            'name'=>['nullable','string'],
            'surname'=>['nullable','string'],
            'date_birth'=>['nullable','string'],
            'date_death'=>['nullable','string'],
            'surname'=>['nullable','string'],
            'patronymic'=>['nullable','string'],
        ]);
        return SearchBurialService::searchBurialResult($data);
    }


    public static function searchBurial(){
        return SearchBurialService::searchBurial();
    }

    public static function singleProduct($slug){
        return IndexBurial::singleProduct($slug);
    }

    public static function searchProductRequest(){
        $page=3;

        SEOTools::setTitle(formatContent(getSeo('page-search-request','title')));
        SEOTools::setDescription(formatContent(getSeo('page-search-request','description')));
        $title_h1=formatContent(getSeo('page-search-request','h1'));

        return view('burial.search-big-product',compact('page','title_h1'));
    }

   public static function searchProductRequestAdd(Request $request)
    {
        
        $data = request()->validate([
            'g-recaptcha-response' => ['required', new RecaptchaRule],
            'surname' => ['required', 'string'],
            'name' => ['required', 'string'],
            'patronymic' => ['required', 'string'],
            'date_birth' => ['required', 'date'],
            'date_death' => ['required', 'date'],
            'landmark' => ['nullable', 'string', 'max:500'], // Новое поле
            'location' => ['required', 'string'],
            'name_customer' => ['required', 'string'],
            'email_customer' => ['required', 'email'],
            'phone_customer' => ['required', 'string'],
            'photos' => ['nullable', 'array', 'max:5'], // Новое поле для фото
            'photos.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'], // 5MB
        ]);
    
        // Добавляем файлы в данные
        if ($request->hasFile('photos')) {
            $data['photos'] = $request->file('photos');
        } else {
            $data['photos'] = [];
        }
    
        return SearchBurialService::searchProductRequestAdd($data);
    }
    
    public static function favoriteAdd($id){
        return FavoriteBurial::favoriteAdd($id);
    }
    
    public static function favoriteDelete($id){
        return FavoriteBurial::favoriteDelete($id);
    }
}
