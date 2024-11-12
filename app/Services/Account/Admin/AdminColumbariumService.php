<?php

namespace App\Services\Account\Admin;

use App\Models\Columbarium;
use App\Models\User;
use App\Services\Parser\ParserColumbariumService;
use App\Services\Parser\ParserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminColumbariumService {

    public static function index(){
        $columbariums=Columbarium::orderBy('title','asc')->paginate(10);
        return view('account.admin.columbarium.index',compact('columbariums'));
    }

    public static function delete($id){
        Columbarium::find($id)->delete();
        return redirect()->back()->with('message_cart','Колумбарий удален');
    }

    public static function parser(){
        return view('account.admin.columbarium.parser');
    }

    public static function import($request){
        return ParserColumbariumService::index($request);
    }

    public static function importReviews($request){
        return ParserColumbariumService::importReviews($request);
    }
    
}