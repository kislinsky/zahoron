<?php

namespace App\Services\Account\Admin;

use App\Models\Mortuary;
use App\Models\User;
use App\Services\Parser\ParserMortuaryService;
use App\Services\Parser\ParserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminMortuaryService {

    public static function index(){
        $mortuaries=Mortuary::orderBy('title','asc')->paginate(10);
        return view('account.admin.mortuary.index',compact('mortuaries'));
    }

    public static function delete($id){
        Mortuary::find($id)->delete();
        return redirect()->back()->with('message_cart','Морги удалены');
    }

    public static function parser(){
        return view('account.admin.mortuary.parser');
    }

    public static function import($request){
        return ParserMortuaryService::index($request);
    }
    
    public static function importReviews($request){
        return ParserMortuaryService::importReviews($request);
    }
}