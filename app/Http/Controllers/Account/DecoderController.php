<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Services\Account\DecoderService;
use Illuminate\Http\Request;

class DecoderController extends Controller
{

    public static function index(){
       return DecoderService::index();
    }

    public static function settings(){
        return DecoderService::settings();
    }


    public static function settingsUpdate(Request $request){
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
           'number_cart'=>['nullable','string'],
           'bank'=>['nullable','string'],

        ]);
        return DecoderService::settingsUpdate($data);
    }

    public static function trainingMaterialVideo(){
        return DecoderService::trainingMaterialVideo();
    }

    public static function trainingMaterialFile(){
        return DecoderService::trainingMaterialFile();
    }


    public static function paymentsPaid(){
        return DecoderService::paymentsPaid();

    }

    public static function paymentsOnVerification(){
        return DecoderService::paymentsOnVerification();

    }
    

    public static function iconUpdateUser(Request $request){
        $data=request()->validate([
            'file'=>['required'],
        ]);
        return DecoderService::iconUpdateUser($data['file']);
    }
    
    public static function viewEditBurial(){
        return DecoderService::viewEditBurial();
    }

    public static function addCommentBurial(Request $request){
        $data=request()->validate([
            'burial_id'=>['required','integer'],
            'comment'=>['required','string'],
        ]);
        return DecoderService::addCommentBurial($data);

    }

    public static function updateBurial(Request $request){
        $data=request()->validate([
            'burial_id'=>['required','integer'],
            'name'=>['required','string'],
            'surname'=>['required','string'],
            'patronymic'=>['required','string'],
            'date_death'=>['required','string'],
            'date_birth'=>['required','string'],
            
        ]);
        return DecoderService::updateBurial($data);
    }

    public static function withdraw($id){
        return DecoderService::withdraw($id);
    }
}