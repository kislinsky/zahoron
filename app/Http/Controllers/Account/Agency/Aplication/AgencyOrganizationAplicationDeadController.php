<?php

namespace App\Http\Controllers\Account\Agency\Aplication;

use App\Http\Controllers\Controller;
use App\Models\DeadApplication;
use App\Services\Account\Agency\Aplications\AgencyDeadAplicationOrganization;

class AgencyOrganizationAplicationDeadController extends Controller
{
    public static function new(){
        return AgencyDeadAplicationOrganization::new();
    }

    public static function inWork(){
        return AgencyDeadAplicationOrganization::inWork();
    }

    public static function completed(){
        return AgencyDeadAplicationOrganization::completed();
    }

    public static function notCompleted(){
        return AgencyDeadAplicationOrganization::notCompleted();
    }

    public static function complete(DeadApplication $aplication){
            return AgencyDeadAplicationOrganization::complete($aplication);
    }

    public static function accept(DeadApplication $aplication){
         return AgencyDeadAplicationOrganization::accept($aplication);
    }
}