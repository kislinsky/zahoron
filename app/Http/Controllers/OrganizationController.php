<?php

namespace App\Http\Controllers;

use App\Rules\RecaptchaRule;
use App\Services\Organization\OrganizationProviderService;
use App\Services\Organization\OrganizationService;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{

    public static function sendCode(Request $request){
        $data=request()->validate([
            'organization_id'=>['required','integer'],
        ]);

        return OrganizationService::sendCode($data);
    }

    public static function acceptCode(Request $request){
        $data=request()->validate([
            'organization_id'=>['required','integer'],
            'code'=>['required','integer'],
        ]);

        return OrganizationService::acceptCode($data);
    }
    
    public static function single($slug){
        return OrganizationService::single($slug);
    }

    public static function addReview(Request $request){
        $data=request()->validate([
            'g-recaptcha-response' => ['required', new RecaptchaRule],
            'organization_id'=>['required','integer'],
            'rating'=>['nullable','integer'],
            'content_review'=>['required','string'],
            'name'=>['required','string'],
            'city_id'=>['required','integer'],
           ]);

        return OrganizationService::addReview($data);
    } 

    public static function ajaxProductsChildrenCat(Request $request){
        $data=request()->validate([
            'organization_id'=>['required','integer'],
            'category_id'=>['required','integer'],
        ]);

        return OrganizationService::ajaxProductsChildrenCat($data);
    }
    
    public static function ajaxProductsMainCat(Request $request){
        $data=request()->validate([
            'organization_id'=>['required','integer'],
            'category_id'=>['required','integer'],
        ]);

        return OrganizationService::ajaxProductsMainCat($data);
    }

    public static function ajaxProductsMainCatUlChildren(Request $request){
        $data=request()->validate([
            'category_id'=>['required','integer'],
        ]);

        return OrganizationService::ajaxProductsMainCatUlChildren($data);
    }
    
    public static function addLikeOrganization($id){
        return OrganizationService::addLikeOrganization($id);
    }

    public static function catalogOrganization(Request $request){
        // $data=request()->validate([
        //     'cemetery_id'=>['nullable'],
        //     'filter_work'=>['nullable','string'],
        //     'district_id'=>['nullable'],
        //     'sort'=>['nullable'],
        //     'category_id'=>['nullable','integer'],
        // ]);

        // return OrganizationService::catalogOrganization($data);
        return redirect()->route('organizations.category',categoryProductChoose()->slug);
    }


    public static function catalogOrganizationCategory($slug,Request $request){
        $data=request()->validate([
            'cemetery_id'=>['nullable'],
            'filter_work'=>['nullable','string'],
            'district_id'=>['nullable'],
            'sort'=>['nullable'],
            'category_id'=>['nullable','integer'],
        ]);

        return OrganizationService::catalogOrganization($slug,$data);
    }

    public static function ajaxMapOrganizations(Request $request){
        $data=request()->validate([
            'cemetery_id'=>['nullable'],
            'filter_work'=>['nullable','string'],
            'district_id'=>['nullable'],
            'sort'=>['nullable'],
            'category_id'=>['nullable','integer'],
        ]);
       
        return OrganizationService::ajaxMapOrganizations($data);
        
    }


    public static function ajaxFilterCatalog(Request $request){
        $data=request()->validate([
            'cemetery_id'=>['nullable'],
            'filter_work'=>['nullable','string'],
            'district_id'=>['nullable'],
            'sort'=>['nullable'],
            'category_id'=>['nullable','integer'],
        ]);
        return OrganizationService::ajaxFilterCatalog($data);
    }

    public static function ajaxCategoryPrices(Request $request){
        $data=request()->validate([
            'filter_work'=>['nullable','string'],
            'cemetery_id'=>['nullable'],
            'district_id'=>['nullable'],
            'sort'=>['nullable'],
            'category_id'=>['nullable','integer'],
        ]);
        return OrganizationService::ajaxCategoryPrices($data);
    }

    public static function ajaxTitlePage(Request $request){
        $data=request()->validate([
            'cemetery_id'=>['nullable'],
            'district_id'=>['nullable'],
            'category_id'=>['nullable','integer'],
        ]);
        return OrganizationService::ajaxTitlePage($data);
    }




    public static function catalogOrganizationProvider(Request $request){
        $data=request()->validate([
            'filter_work'=>['nullable','string'],
            'city_id'=>['nullable','integer'],
            'sort'=>['nullable'],
            'name_organization'=>['nullable','string'],
            'category_id'=>['nullable','integer'],
        ]);
       
        return OrganizationProviderService::catalogOrganizationProvider($data);
    }

    
    public static function ajaxFilterCatalogProvider(Request $request){
        $data=request()->validate([
            'filter_work'=>['nullable','string'],
            'city_id'=>['nullable','integer'],
            'sort'=>['nullable'],
            'category_id'=>['nullable','integer'],
        ]);
        return OrganizationProviderService::ajaxFilterCatalog($data);
    }

    public static function ajaxCategoryPricesProvider(Request $request){
        $data=request()->validate([
            'filter_work'=>['nullable','string'],
           'city_id'=>['nullable','integer'],
            'sort'=>['nullable'],
            'category_id'=>['nullable','integer'],
        ]);
        return OrganizationProviderService::ajaxCategoryPrices($data);
    }

    public static function ajaxTitlePageProvider(Request $request){
        $data=request()->validate([
            'city_id'=>['nullable','integer'],
            'category_id'=>['nullable','integer'],
        ]);
        return OrganizationProviderService::ajaxTitlePage($data);
    }


    public static function ajaxSearchCatalogProvider(Request $request){
        $data=request()->validate([
            'name_organization'=>['nullable','string'],
        ]);
        return OrganizationProviderService::ajaxSearchCatalogProvider($data);
    }
    

    public static function ajaxRatingOrganizationProvider(Request $request){
        $data=request()->validate([
            'city_id'=>['nullable','integer'],
            'category_id'=>['nullable','integer'],
        ]);
        return OrganizationProviderService::ajaxRatingOrganizationProvider($data);
    }


    
    
    
}
