<?php

namespace App\Http\Controllers\Account\Admin;

use App\Http\Controllers\Controller;
use App\Services\Account\Admin\AdminProduct;
use Illuminate\Http\Request;

class AdminProductController extends Controller
{
    public static function parser(){
       return AdminProduct::parser();
    }

    public static function import(Request $request){
        $request->validate([    
            'files' => 'required',
            'files.*' => 'required|mimes:xlsx,xls,csv'
        ]);
        return AdminProduct::import($request);
    }
    
}
