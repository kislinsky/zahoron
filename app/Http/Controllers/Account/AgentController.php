<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Account\Agent\AgentService;

class AgentController extends Controller
{
    public static function index(){
        return AgentService::index();
    }


    public static function services(Request $request){
        $data=request()->validate([
            'status'=>['integer','nullable'],
        ]);
        return AgentService::services($data);
    }

    public static function acceptService($id){
        return AgentService::acceptService($id);
    }

    public static function getToWorkService($id){
        return AgentService::getToWorkService($id);
    }

    public static function agentSettings(){
        return AgentService::agentSettings();
    }


    public static function agentSettingsUpdate(Request $request){
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
           'cemetery_ids'=>['nullable']
           

        ]);

        return AgentService::agentSettingsUpdate($data);
    }

    public static function addUploadSeal(Request $request){
        $data=request()->validate([
            'file_print'=>["required"]
        ]);

        return AgentService::addUploadSeal($data);
    }
    


    public static function deleteUploadSeal($id){
        return AgentService::deleteUploadSeal($id);
    }

    public static function rentService(Request $request){
        $data=request()->validate([
            'order_id'=>["required",'integer'],
            'file_services'=>["required"],
        ]);
        return AgentService::rentService($data);
    }


   

    public static function addCemetery(Request $request){
        $data=request()->validate([
            'id_location'=>["nullable",'integer'],
            'name_location'=>["required",'string'],
        ]);

        return AgentService::addCemetery($data);
    }
    
    
}
