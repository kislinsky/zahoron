<?php

namespace App\Services\Account\Admin;
use App\Services\Parser\ParserChurchService;
use App\Services\Parser\ParserMosqueService;

class AdminChurchService {

    public static function parser(){
        return view('account.admin.church.parser');
    }

    public static function import($request){
        return ParserChurchService::index($request);
    }

    public static function importReviews($request){
        return ParserChurchService::importReviews($request);
    }

}