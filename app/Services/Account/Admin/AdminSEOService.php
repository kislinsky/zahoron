<?php

namespace App\Services\Account\Admin;

use App\Models\SEO;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminSEOService {

    public static function object($page){
       $object_columns=SEO::where('page',$page)->get();
       return view('account.admin.seo.update',compact('object_columns'));
    }
}