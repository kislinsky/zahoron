<?php

namespace App\Http\Controllers\Account\Admin;

use App\Http\Controllers\Controller;
use App\Models\Burial;
use App\Services\Account\Admin\AdminBurialService;
use Illuminate\Http\Request;

class AdminBurialController extends Controller
{
    public static function index(){
       return AdminBurialService::index();
    }

    public static function delete(Burial $burial){
        return AdminBurialService::delete($burial);
    }

    public static function parser(){
        return AdminBurialService::parser();
    }
    
    public static function import(Request $request){
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);
        return AdminBurialService::import($request);
    }
}
