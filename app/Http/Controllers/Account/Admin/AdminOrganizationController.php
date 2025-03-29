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
        'columns_to_update'=>'nullable',
        'import_with_user'=>'required',
        'import_type'=>'required',
        'files' => 'required',
        'files.*' => 'required|mimes:xlsx,xls,csv'
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
            'files_prices.*' => 'required|mimes:xlsx,xls,csv',
            'import_with_user_prices' => 'sometimes|in:0,1',
            'update_empty_to_ask' => 'sometimes|in:0,1'
        ]);
        return AdminOrganizationIndexService::importPrices($request);
    }


    
}
