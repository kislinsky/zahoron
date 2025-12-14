<?php

namespace App\Http\Controllers;

use App\Models\OurWork;
use App\Services\CapturePagesService;
use Illuminate\Http\Request;

class CapturePages extends Controller
{
    public static function beatification(){
       return CapturePagesService::beatification();
    }
    
    public static function organizationFuneral(){
       return CapturePagesService::organizationFuneral();
    }

    public static function dead(){
       return CapturePagesService::dead();
    }

    public static function wake(){
       return CapturePagesService::wake();
    }

    public static function cargo(){
       return CapturePagesService::cargo();
    }

    public static function organizationCremation(){
       return CapturePagesService::organizationCremation();
    }
}
