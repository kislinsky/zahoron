<?php

namespace App\Http\Controllers\Account\Agency;

use App\Http\Controllers\Controller;
use App\Models\ProductRequestToSupplier;
use App\Models\RequestsCostProductsSupplier;
use App\Services\Account\Agency\AgencyOrganizationProviderService;
use App\Services\Account\Agency\AgencyOrganizationService;
use Illuminate\Http\Request;

class AgencyOrganizationProviderController extends Controller
{

    public function requestsCostProductSuppliers(){
        return AgencyOrganizationProviderService::requestsCostProductSuppliers();
    }
    

    public static function addRequestsCostProductSuppliers(Request $request){
        $data=request()->validate([
            'lcs'=>['nullable'],
            'products'=>['required'],
            'count'=>['required'],
            'all_lcs'=>['nullable'],
        ]);
        return AgencyOrganizationProviderService::addRequestsCostProductSuppliers($data);
    }

    public static function deletRequest(RequestsCostProductsSupplier $request){        
        return AgencyOrganizationProviderService::deletRequest($request);
    }


    public static function answerRequestsCostProductSuppliers(){
        return AgencyOrganizationProviderService::answerRequestsCostProductSuppliers();
    }

    public static function likeOrganizations(){
        return AgencyOrganizationProviderService::likeOrganizations();
    }

    public static function stocksOrganizationProviders(){
        return AgencyOrganizationProviderService::stocksOrganizationProviders();
    }

    public static function discountsOrganizationProviders(){
        return AgencyOrganizationProviderService::discountsOrganizationProviders();
    }

    public static function addOfferToProvider(){
        return AgencyOrganizationProviderService::addOfferToProvider();
    }


    public static function createOfferToProvider(Request $request){
        $data=request()->validate([
            'title'=>['required','string'],
            'content'=>['required','string'],
            "images" => ["required", "array"],
            "images.*" => [
                "required",
                'image',
                'mimes:jpeg,jpg,png,gif,svg,webp,bmp,tiff,ico,heic,heif',
                'max:2048'
            ],
            'category'=>['required','integer'],
            'none_category'=>['nullable'],
            'delivery'=>['nullable'],
        ]);
        return AgencyOrganizationProviderService::createOfferToProvider($data);

    }

    public static function createdOfferToProvider(Request $request){
        $data=request()->validate([
            'category'=>['nullable','integer'],
        ]);
        
        return AgencyOrganizationProviderService::createdOfferToProvider($data);
    }

    public static function deleteOffer(ProductRequestToSupplier $offer){        
        return AgencyOrganizationProviderService::deleteOffer($offer);
    }

    public static function answerOfferToProvider(Request $request){
        $data=request()->validate([
            'category'=>['nullable','integer'],
        ]);
        
        return AgencyOrganizationProviderService::answerOfferToProvider($data);
    }


    public static function filterCategoryAnswerOfferToProvider(Request $request){
        $data=request()->validate([
            'category'=>['required','integer'],
        ]);
        
        return AgencyOrganizationProviderService::filterCategoryAnswerOfferToProvider($data);
    }

    public static function filterCategoryCreatedOfferToProvider(Request $request){
        $data=request()->validate([
            'category'=>['required','integer'],
        ]);
        
        return AgencyOrganizationProviderService::filterCategoryCreatedOfferToProvider($data);
    }
}