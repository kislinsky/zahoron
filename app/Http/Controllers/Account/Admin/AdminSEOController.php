<?php

namespace App\Http\Controllers\Account\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Account\Admin\AdminSEOService;


class AdminSEOController extends Controller{

    public static function object($page){
        return AdminSEOService::object($page);
    }

}