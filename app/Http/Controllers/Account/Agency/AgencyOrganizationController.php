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
   
}