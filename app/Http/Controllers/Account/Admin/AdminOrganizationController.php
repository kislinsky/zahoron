<?php

namespace App\Http\Controllers\Account\Admin;

use App\Http\Controllers\Controller;
use App\Services\Account\Admin\Organization\AdminOrganizationIndexService;
use Illuminate\Http\Request;

class AdminOrganizationController extends Controller
{
    public static function parser(){
       return AdminOrganizationIndexService::parser();
    }

    public static function import(Request $request){
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);
        return AdminOrganizationIndexService::import($request);
    }


    public static function importReviews(Request $request){
        $request->validate([
            'file_reviews' => 'required|mimes:xlsx,xls,csv'
        ]);
        return AdminOrganizationIndexService::importReviews($request);
    }


    public static function importPrices(Request $request){
        $request->validate([
            'file_prices' => 'required|mimes:xlsx,xls,csv'
        ]);
        return AdminOrganizationIndexService::importPrices($request);
    }


    
}
