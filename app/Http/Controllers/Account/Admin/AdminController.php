<?php

namespace App\Http\Controllers\Account\Admin;

use App\Http\Controllers\Controller;
use App\Services\Account\Admin\AdminCemeteryService;
use App\Services\Account\Admin\AdminColumbariumService;
use App\Services\Account\Admin\AdminCrematoriumService;
use App\Services\Account\Admin\AdminMortuaryService;
use App\Services\Account\Admin\AdminService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public static function index(){
       return AdminService::index();
    }
}
