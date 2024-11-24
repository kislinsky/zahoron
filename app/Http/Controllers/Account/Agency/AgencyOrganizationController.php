<?php

namespace App\Http\Controllers\Account\Agency;

use App\Http\Controllers\Controller;
use App\Services\Account\Agency\AgencyOrganizationService;
use Illuminate\Http\Request;

class AgencyOrganizationController extends Controller
{

    public static function settings($id){
        return AgencyOrganizationService::settings($id);
    }


    public static function update(Request $request){
        $data=request()->validate([
            'cemetery_ids'=>['nullable'],
            'id'=>['integer','required'],
            'title'=>['string','required'],
            'mini_content'=>['string','nullable'],
            'content'=>['string','nullable'],
            'phone'=>['string','nullable'],
            'telegram'=>['string','nullable'],
            'whatsapp'=>['string','nullable'],
            'email'=>['string','nullable'],
            'city'=>['integer','required'],
            'next_to'=>['string','nullable'],
            'underground'=>['string','nullable'],
            'adres'=>['string','required'],
            'width'=>['string','required'],
            'longitude'=>['string','required'],
            'categories_organization'=>['nullable'],
            'price_cats_organization'=>['nullable'],
            'working_day'=>['nullable'],
            'holiday_day'=>['nullable'],
            'available_installments'=>['nullable'],
            'found_cheaper'=>['nullable'],
            'сonclusion_contract'=>['nullable'],
            'state_compensation'=>['nullable'],
    
        ]);

        return AgencyOrganizationService::update($data);
    }

    public static function searchOrganizations(Request $request){
        $data=request()->validate([
            's'=>['nullable','string'],
            'city_id'=>['nullable','integer'],
        ]);
        return AgencyOrganizationService::searchOrganizations($data);
    }
   
    public static function aplications(){
        
        return AgencyOrganizationService::aplications();
    }

    public static function buyAplicationsFuneralServices(Request $request){ 
 
        $data=request()->validate([
            'applications_funeral_services'=>['required','integer'],
        ]);

        return AgencyOrganizationService::buyAplicationsFuneralServices($data['applications_funeral_services']);
    }

    public static function buyAplicationsCallsOrganization(Request $request){  
        $data=request()->validate([
            'calls_organization'=>['required','integer'],
        ]);
        return AgencyOrganizationService::buyAplicationsCallsOrganization($data['calls_organization']);
    }

    public static function buyAplicationsProductRequestsFromMarketplace(Request $request){  
        $data=request()->validate([
            'product_requests_from_marketplace'=>['required','integer'],
        ]);
        return AgencyOrganizationService::buyAplicationsProductRequestsFromMarketplace($data['product_requests_from_marketplace']);
    }

    public static function buyAplicationsImprovemenGraves(Request $request){  
        $data=request()->validate([
            'applications_improvemen_graves'=>['required','integer'],
        ]);
        return AgencyOrganizationService::buyAplicationsImprovemenGraves($data['applications_improvemen_graves']);
    }


    public static function addProduct(){
        return AgencyOrganizationService::addProduct();
    }

    public static function allProducts(){
        $data=request()->validate([
            'category_id'=>['nullable','string'],
            'parent_category_id'=>['nullable','string'],
            's'=>['nullable','string'],
        ]);
        return AgencyOrganizationService::allProducts($data);
    }

    public static function deleteProduct($id){
        return AgencyOrganizationService::deleteProduct($id);
    }


    public static function updatePriceProduct(Request $request){
        $data=request()->validate([
            'price'=>['required','integer'],
            'product_id'=>['required','integer'],
        ]);
        return AgencyOrganizationService::updatePriceProduct($data);
    }


    public static function searchProduct(Request $request){
        $data=request()->validate([
            's'=>['nullable','string'],
        ]);
        return AgencyOrganizationService::searchProduct($data);
    }

    public static function filtersProduct(Request $request){
        $data=request()->validate([
            'category_id'=>['nullable','string'],
            'parent_category_id'=>['nullable','string'],
        ]);
        return AgencyOrganizationService::filtersProduct($data);

    }

    public static function createProduct(Request $request){
        $data=request()->validate([
            'title'=>['required','string'],
            'content'=>['required','string'],
            'price'=>['required','string'],
            'price_sale'=>['nullable','integer'],
            'material'=>['nullable','string'],
            'size'=>['nullable','string'],
            'your_size'=>['nullable','string'],
            'parameters'=>['nullable','string'],
            'width'=>['nullable','string'],
            'longitude'=>['nullable','string'],
            'menus'=>['nullable','string'],
            'images'=>['required'],
            'cat'=>['required','integer'],
            'cat_children'=>['required','integer'],
        ]);

        return AgencyOrganizationService::createProduct($data);
    }
   
            
    public static function reviewsOrganization(){
        return AgencyOrganizationService::reviewsOrganization();
    }

    public static function reviewsProduct(){
        return AgencyOrganizationService::reviewsProduct();
    }


    public static function reviewOrganizationAccept($id){
        return AgencyOrganizationService::reviewOrganizationAccept($id);

    }

    public static function reviewProductAccept($id){
        return AgencyOrganizationService::reviewProductAccept($id);

    }


    public static function reviewOrganizationDelete($id){
        return AgencyOrganizationService::reviewOrganizationDelete($id);

    }

    public static function reviewProductDelete($id){
        return AgencyOrganizationService::reviewProductDelete($id);

    }

    public static function updateReviewOrganization(Request $request){
        $data=request()->validate([
            'id_review'=>['required','integer'],
            'content_review'=>['required','string'],
        ]);
        return AgencyOrganizationService::updateReviewOrganization($data);

    }

    public static function updateReviewProduct(Request $request){
        $data=request()->validate([
            'id_review'=>['required','integer'],
            'content_review'=>['required','string'],
        ]);
        return AgencyOrganizationService::updateReviewProduct($data);

    }


    public static function updateOrganizationResponseReviewOrganization(Request $request){
        $data=request()->validate([
            'id_review'=>['required','integer'],
            'organization_response_review'=>['required','string'],
        ]);
        return AgencyOrganizationService::updateOrganizationResponseReviewOrganization($data);

    }

    public static function updateOrganizationResponseReviewProduct(Request $request){
        $data=request()->validate([
            'id_review'=>['required','integer'],
            'organization_response_review'=>['required','string'],
        ]);
        return AgencyOrganizationService::updateOrganizationResponseReviewProduct($data);

    }
    
}