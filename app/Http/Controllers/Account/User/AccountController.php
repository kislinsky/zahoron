<?php

namespace App\Http\Controllers\Account\User;

use App\Http\Controllers\Controller;
use App\Models\OrderProduct;
use App\Models\SearchBurial;
use Illuminate\Http\Request;
use App\Services\Account\User\UserService;
use App\Services\Order\OrderBurialService;

class AccountController extends Controller
{

    public static function index(){
        return UserService::index();

    }

    public static function services(Request $request){
        $data=request()->validate([
            'status'=>['integer','nullable'],
        ]);
        return UserService::services($data);
    }
    
    public static function burialRequestIndex(Request $request){
        $data=request()->validate([
            'status'=>['integer','nullable'],
        ]);
        return UserService::burialRequestIndex($data);
    }

    public static function burialRequestDelete(SearchBurial $burial_request){
        return UserService::burialRequestDelete($burial_request);
    }


    public static function burials(Request $request){
        $data=request()->validate([
            'status'=>['integer','nullable'],
        ]);
        return UserService::burials($data);
    }

    public static function burialDelete($id){
        return OrderBurialService::burialDelete($id);
    }

    
    public static function favoriteProduct(){
        return UserService::favoriteProduct();
    }

    public static function userSettings(){
        return UserService::userSettings();
    }

    public static function userSettingsUpdate(Request $request){
        $data=request()->validate([
            'name'=>['string','nullable'],
            'surname'=>['string','nullable'],
            'patronymic'=>['string','nullable'],
            'phone'=>['required','string'],
            'city'=>['string','nullable'],
            'adres'=>['string','nullable'],
            'email'=>['string','email','nullable'],
           'whatsapp'=>['string','nullable'],
            'telegram'=>['string','nullable'],
           'password'=>['string','nullable','min:8',],
            'password_new'=>['string','nullable','min:8',],
            'password_new_2'=>['string','nullable','min:8',],
            'email_notifications'=>['nullable','integer'],
            'sms_notifications'=>['nullable','integer'],
           'language'=>['nullable','integer'],
           'theme'=>['nullable','string'],

        ]);

        return UserService::userSettingsUpdate($data);
    }

    public static function products(Request $request){
        $data=request()->validate([
            'status'=>['integer','nullable'],
            'cemetery'=>['integer','nullable'],
        ]);
        return UserService::products($data);
    }

    public static function productDelete(OrderProduct $order){
        return UserService::productDelete($order);
    }

    public static function productFilter($status){
        return UserService::productFilter($status);
    }

    
    
    
    
}