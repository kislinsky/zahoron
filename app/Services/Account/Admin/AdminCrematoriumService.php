<?php

namespace App\Services\Account\Admin;

use App\Models\Crematorium;
use App\Models\User;
use App\Services\Parser\ParserCrematoriumService;
use App\Services\Parser\ParserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminCrematoriumService {

    public static function index(){
        $crematoriums=Crematorium::orderBy('title','asc')->paginate(10);
        return view('account.admin.crematorium.index',compact('crematoriums'));
    }

    public static function delete($id){
        Crematorium::find($id)->delete();
        return redirect()->back()->with('message_cart','Крематорий удален');
    }

    public static function parser(){
        return view('account.admin.crematorium.parser');
    }

    public static function import($request){
        return ParserCrematoriumService::index($request);
    }
    
    public static function importReviews($request){
        return ParserCrematoriumService::importReviews($request);
    }
}