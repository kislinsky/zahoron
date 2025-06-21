<?php

namespace App\Services\Account\Admin;
use App\Services\Parser\ParserMosqueService;

class AdminMosqueService {

    public static function parser(){
        return view('account.admin.mosque.parser');
    }

    public static function import($request){
        return ParserMosqueService::index($request);
    }

    public static function importReviews($request){
        return ParserMosqueService::importReviews($request);
    }

}