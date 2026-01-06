<?php

namespace App\Services\Account\Admin;

use App\Services\Parser\ParserProduct;


class AdminProduct {

    public static function parser(){
        return view('account.admin.product.import');
    }

    public static function import($request){
        return ParserProduct::index($request);
    }
}