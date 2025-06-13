<?php

namespace App\Services\Account\Admin;
use App\Services\Parser\ParserChurchService;

class AdminChurchService {

    public static function parser(){
        return view('account.admin.church.parser');
    }

    public static function import($request){
        return ParserChurchService::index($request);
    }

}