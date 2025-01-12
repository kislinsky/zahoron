<?php

namespace App\Http\Controllers\Account\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Account\Admin\AdminSEOService;


class AdminSEOController extends Controller{

    public static function object($page){
        return AdminSEOService::object($page);
    }

    public static function settings(){
        return  AdminSEOService::settings();
    }

    public static function updateSeo(Request $request,$page){
        $data=$request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'h1' => 'required|string',
        ]);
        return AdminSEOService::updateSeo($data,$page);
    }

}