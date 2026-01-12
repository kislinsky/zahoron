<?php

namespace App\Http\Controllers;

use App\Models\CategoryProduct;
use App\Models\Product;
use App\Rules\RecaptchaRule;
use App\Services\Product\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public static function singleProduct($slug){
        return ProductService::singleProduct($slug);
    }

    public static function redirectToCategoryMarketplace(){
        return routeMarketplace(CategoryProductChoose());
    }
    
    public static function marketplace($slug,Request $request){

        $data=request()->validate([
            'layering'=>['string','nullable'],
            'cemetery_id'=>['integer','nullable'],
            'category'=>['integer','nullable'],
            'size'=>['string','nullable'],
            'district_id'=>['integer','nullable'],
            'sort'=>['string','nullable'],
            'material'=>['string','nullable'],
        ]);
        return ProductService::marketplace($slug,$data);
    }

    public static function ajaxTitle(Request $request){
        $data=request()->validate([
            'cemetery_id'=>['integer','nullable'],
            'category'=>['integer','nullable'],
            'district_id'=>['integer','nullable'],
        ]);
        return ProductService::ajaxTitle($data);
    }
    

    public static function filterShow(Request $request){
        $data=request()->validate([
            'layering'=>['string','nullable'],
            'cemetery_id'=>['integer','nullable'],
            'category'=>['integer','nullable'],
            'size'=>['string','nullable'],
            'sort'=>['string','nullable'],
            'district_id'=>['integer','nullable'],
            'material'=>['string','nullable'],
        ]);
        return ProductService::filterShow($data);
    }

    public static function ajaxProductCat(Request $request){
        $data=request()->validate([
            'category'=>['integer','required'],
        ]);
        return ProductService::ajaxProductCat($data);
    }


    public static function ajaxCemeteryCat(Request $request){
        $data=request()->validate([
            'cemetery_id'=>['integer','nullable'],
        ]);
        return ProductService::ajaxCemeteryCat($data);
    }

    public static function ajaxCatContent(Request $request){
        $data=request()->validate([
            'category'=>['integer','required'],
        ]);
        return ProductService::ajaxCatContent($data);
    }
    public static function ajaxCatManual(Request $request){
        $data=request()->validate([
            'category'=>['integer','required'],
        ]);
        return ProductService::ajaxCatManual($data);
    }


    public static function ajaxCatReviews(Request $request){
        $data=request()->validate([
            'category'=>['integer','required'],
        ]);
        return ProductService::ajaxCatReviews($data);
    }



    
    public static function addReview(Request $request){
        $data=request()->validate([
            'g-recaptcha-response' => ['required', new RecaptchaRule],
            'name'=>['string','required'],
           'surname'=>['string','required'],
            'product_id'=>['integer','required'],
            'message'=>['string','required'],
        ]);
        return ProductService::addReview($data);
    }
    
    public static function search(Request $request){
        return ProductService::search($request);
    }
    
}
