<?php

namespace App\Services\Account\Admin;

use App\Models\Cemetery;
use App\Services\Parser\ParserCemeteryService;

class AdminCemeteryService {


    public static function index(){
        $cemeteries=Cemetery::orderBy('title','asc')->paginate(10);
        return view('account.admin.cemetery.index',compact('cemeteries'));
    }

    public static function delete($id){
        Cemetery::find($id)->delete();
        return redirect()->back()->with('message_cart','Кладбище удалено');
    }

    public static function parser(){
        return view('account.admin.cemetery.parser');
    }

    public static function import($request){
        return ParserCemeteryService::index($request);
    }

    public static function importReviews($request){
        return ParserCemeteryService::importReviews($request);
    }

}