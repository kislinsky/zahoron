<?php

namespace App\Http\Controllers\Account\Agency;

use App\Http\Controllers\Controller;
use App\Services\Account\Agency\AgencyService;
use Illuminate\Http\Request;

class AgencyController extends Controller
{

    public static function index(){
        return AgencyService::index();
    }


    public static function chooseOrganization($id){
        return AgencyService::chooseOrganization($id);
    }

    public static function serviceIndex(){
        return AgencyService::serviceIndex();
    }
    public static function serviceFilter($status){
        return AgencyService::serviceFilter($status);
    }

    public static function acceptService($id){
        return AgencyService::acceptService($id);
    }



    public static function settings(){
        return AgencyService::settings();
    }

    public static function organizationSettingsUpdate(Request $request){
        $user=user();
        if($user->organizational_form=='user'){
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
               'inn'=>['required','string'],
               'number_cart'=>['nullable','string'],
               'bank'=>['nullable','string'],
               'name_organization'=>['nullable','string'],
            ]);
        }
        elseif($user->organizational_form=='ep'){
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
               'inn'=>['required','string'],
               'name_organization'=>['nullable','string'],
                'ogrn'=>['nullable','string'],
                'city_id'=>['required','integer'],
                'edge_id'=>['required','integer'],
            ]);
        }
        elseif($user->organizational_form=='organization'){
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
               'inn'=>['required','string'],
               'name_organization'=>['nullable','string'],
                'city_id'=>['required','integer'],
                'edge_id'=>['required','integer'],
                'in_face'=>['required','string'],
                'regulation'=>['required','string'],
            ]);
        }


        

        return AgencyService::organizationSettingsUpdate($data);
    }



 
    public static function addUploadSeal(Request $request){
        $data=request()->validate([
            'file_print'=>["required"]
        ]);

        return AgencyService::addUploadSeal($data);
    }

    public static function deleteUploadSeal($id){
        return AgencyService::deleteUploadSeal($id);
    }



    public static function rentService(Request $request){
        $data=request()->validate([
            'order_id'=>["required",'integer'],
            'file_services'=>["required"],
        ]);
        return AgencyService::rentService($data);
    }


    // public static function organizationDeleteCemetery($id){
    //     return AgencyService::agentDeleteCemetery($id);
    // }

    public static function addCemetery(Request $request){
        $data=request()->validate([
            'id_location'=>["nullable",'integer'],
            'name_location'=>["required",'string'],
        ]);
        return AgencyService::addCemetery($data);
    }

    public static function beautificationsIndex(){
        return AgencyService::beautificationsIndex();
    }

    public static function acceptBeatification($id){
        return AgencyService ::acceptBeatification($id);
    }
    
    
}
