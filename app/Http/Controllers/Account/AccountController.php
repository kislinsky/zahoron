<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Account\UserService;
use App\Services\Order\OrderBurialService;

class AccountController extends Controller
{
    public static function serviceIndex(){
        return UserService::serviceIndex();
    }

    public static function serviceFilter($status){
        return UserService::serviceFilter($status);
    }

    
    public static function burialRequestIndex(){
        return UserService::burialRequestIndex();
    }

    public static function burialRequestFilter($status){
        return UserService::burialRequestFilter($status);
    }

    public static function burialIndex(){
        return UserService::burialIndex();
    }

    public static function burialDelete($id){
        return OrderBurialService::burialDelete($id);
    }

    public static function burialFilter($status){
        return UserService::burialFilter($status);
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

    public static function products(){
        return UserService::products();
    }

    public static function productDelete($id){
        return UserService::productDelete($id);
    }

    public static function productFilter($status){
        return UserService::productFilter($status);
    }

    
    
    
    
}
