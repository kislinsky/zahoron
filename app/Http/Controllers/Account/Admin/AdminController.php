<?php

namespace App\Http\Controllers\Account\Admin;

use App\Http\Controllers\Controller;
use App\Services\Account\Admin\AdminService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public static function index(){
       return AdminService::index();
    }

    public static function pageUpdateRobotsTxt(){   
        return AdminService::pageUpdateRobotsTxt();
    }

    public static function updateRobotsTxt(Request $request){ 
        $data=$request->validate([    
            'content' => 'required|string',
        ]);  
        return AdminService::updateRobotsTxt($data);
    }
}
