<?php

namespace App\Http\Controllers\Account\Agency\Aplication;

use App\Http\Controllers\Controller;
use App\Models\FuneralService;
use App\Services\Account\Agency\Aplications\AgencyFuneralServiceAplicationOrganization;
use Illuminate\Http\Request;

class AgencyOrganizationAplicationFuneralServiceController extends Controller
{
    public static function new(Request $request){
        $data=request()->validate([
            'service'=>['nullable','integer'],
        ]);
        return AgencyFuneralServiceAplicationOrganization::new($data);
    }


    public static function inWork(Request $request){
        $data=request()->validate([
            'service'=>['nullable','integer'],
        ]);
        return AgencyFuneralServiceAplicationOrganization::inWork($data);
    }

    public static function filterService(Request $request){
        $data=request()->validate([
            'service'=>['required','integer'],
            'status'=>['required','integer'],
        ]);
        return AgencyFuneralServiceAplicationOrganization::filterService($data);
    }

    public static function notCompleted(Request $request){
        $data=request()->validate([
            'service'=>['nullable','integer'],
        ]);
        return AgencyFuneralServiceAplicationOrganization::notCompleted($data);
    }

    public static function accept(FuneralService $aplication){
        
        return AgencyFuneralServiceAplicationOrganization::accept($aplication);
    } 


    public static function completed(Request $request){
        $data=request()->validate([
            'service'=>['nullable','integer'],
        ]);
        return AgencyFuneralServiceAplicationOrganization::completed($data);
    }

    public static function complete(FuneralService $aplication){
            return AgencyFuneralServiceAplicationOrganization::complete($aplication);
    }
}
