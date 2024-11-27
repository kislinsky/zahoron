<?php

namespace App\Http\Controllers\Account\Agency;

use App\Http\Controllers\Controller;
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

    public static function likeOrganizations(){
        return AgencyOrganizationProviderService::likeOrganizations();
    }
}