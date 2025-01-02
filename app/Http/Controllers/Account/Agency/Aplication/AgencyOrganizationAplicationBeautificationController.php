<?php

namespace App\Http\Controllers\Account\Agency\Aplication;

use App\Http\Controllers\Controller;
use App\Models\Beautification;
use App\Services\Account\Agency\Aplications\AgencyBeautificationAplicationOrganization;

class AgencyOrganizationAplicationBeautificationController extends Controller
{
    public static function new(){
        return AgencyBeautificationAplicationOrganization::new();
    }

    public static function inWork(){
        return AgencyBeautificationAplicationOrganization::inWork();
    }

    public static function completed(){
        return AgencyBeautificationAplicationOrganization::completed();
    }

    public static function notCompleted(){
        return AgencyBeautificationAplicationOrganization::notCompleted();
    }

    public static function accept(Beautification $aplication){
        return AgencyBeautificationAplicationOrganization::accept($aplication);
    }

    public static function complete(Beautification $aplication){
        return AgencyBeautificationAplicationOrganization::complete($aplication);
    }
    
}