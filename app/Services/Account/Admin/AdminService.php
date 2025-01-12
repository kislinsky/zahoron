<?php

namespace App\Services\Account\Admin;


class AdminService {
    
    public static function index(){
        $user=user();
        return view('account.admin.index',compact('user'));
    }
    
}