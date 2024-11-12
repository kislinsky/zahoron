<?php

namespace App\Services\Account\Admin;

use App\Models\User;
use App\Services\Parser\ParserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminService {
    
    public static function index(){
        $user=user();
        return view('account.admin.index',compact('user'));
    }
    
}