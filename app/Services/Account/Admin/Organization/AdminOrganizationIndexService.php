<?php

namespace App\Services\Account\Admin\Organization;

use App\Models\Area;
use App\Models\City;
use App\Models\Edge;
use App\Services\Parser\ParserOrganizationService;

class AdminOrganizationIndexService {

    public static function parser(){
        $edges=Edge::orderBy('title','asc')->get();
        $areas=Area::orderBy('title','asc')->get();
        $cities=City::orderBy('title','asc')->get();
        return view('account.admin.organization.parser',compact('edges','areas','cities'));
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

