<?php

namespace App\Http\Controllers\Account\Admin;

use App\Http\Controllers\Controller;
use App\Services\Account\Admin\AdminService;

class AdminController extends Controller
{
    public static function index(){
       return AdminService::index();
    }
}
