<?php

namespace App\Http\Controllers\Account\Admin;

use App\Http\Controllers\Controller;
use App\Services\Account\Admin\AdminGeoService;
use Illuminate\Http\Request;

class AdminGeoController extends Controller
{
    public static function parser(){
       return AdminGeoService::parser();
    }

    public static function import(Request $request){
        $request->validate([    
            'files' => 'required',
            'files.*' => 'required|mimes:xlsx,xls,csv'
        ]);
        return AdminGeoService::import($request);
    }
    
}
