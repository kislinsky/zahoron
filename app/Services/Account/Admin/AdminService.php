<?php

namespace App\Services\Account\Admin;


class AdminService {
    
    public static function index(){
        $user=user();
        return view('account.admin.index',compact('user'));
    }

    public static function pageUpdateRobotsTxt(){  
        $path = public_path('robots.txt');
        $content = file_get_contents($path);
        return view('account.admin.settings.robots-txt',compact('content'));    
    }

    public static function updateRobotsTxt($data){   
        $path = public_path('robots.txt');
        unlink($path);
        file_put_contents($path, $data['content']);
        return redirect()->back();
    }
    
}