<?php

namespace App\Services\Account\Admin;

use App\Models\Burial;
use App\Services\Parser\ParserBurialService;

class AdminBurialService {
    
    public static function index(){
        $burials=Burial::orderBy('id','desc')->paginate(10);
        return view('account.admin.burial.index',compact('burials'));
    }

    public static function delete($burial){
        $burial->delete();
        return redirect()->back()->with('message_cart','Захоронение удалено');
    }

    public static function parser(){
        return view('account.admin.burial.parser');
    }

    public static function import($request){
        return ParserBurialService::index($request);
    }



}