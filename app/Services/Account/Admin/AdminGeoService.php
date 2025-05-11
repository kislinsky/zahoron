<?php

namespace App\Services\Account\Admin;

use App\Services\Parser\ParserGeoService;


class AdminGeoService {

    public static function parser(){
        return view('account.admin.geo.import');
    }

    public static function import($request){
        return ParserGeoService::index($request);
    }
}