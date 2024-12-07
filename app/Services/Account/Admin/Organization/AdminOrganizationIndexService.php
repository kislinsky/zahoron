<?php

namespace App\Services\Account\Admin\Organization;

use App\Services\Parser\ParserOrganizationService;

class AdminOrganizationIndexService {

    public static function parser(){
        return view('account.admin.organization.parser');
    }

    public static function import($request){
        return ParserOrganizationService::index($request);
    }

    public static function importReviews($request){
        return ParserOrganizationService::importReviews($request);
    }

    public static function importPrices($request){
        return ParserOrganizationService::importPrices($request);
    }

    
}

