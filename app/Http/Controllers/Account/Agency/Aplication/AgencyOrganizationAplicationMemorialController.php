<?php

namespace App\Http\Controllers\Account\Agency\Aplication;

use App\Http\Controllers\Controller;
use App\Models\Memorial;
use App\Services\Account\Agency\Aplications\AgencyMemorialAplicationOrganization;

class AgencyOrganizationAplicationMemorialController extends Controller
{
    public static function new(){
        return AgencyMemorialAplicationOrganization::new();
    }

    public static function inWork(){
        return AgencyMemorialAplicationOrganization::inWork();
    }

    public static function completed(){
        return AgencyMemorialAplicationOrganization::completed();
    }

    public static function notCompleted(){
        return AgencyMemorialAplicationOrganization::notCompleted();
    }

    public static function complete(Memorial $aplication){
            return AgencyMemorialAplicationOrganization::complete($aplication);
    }

    public static function accept(Memorial $aplication){
        return AgencyMemorialAplicationOrganization::accept($aplication);
    }
}