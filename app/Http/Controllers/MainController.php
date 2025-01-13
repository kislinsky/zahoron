<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\WorkingHoursOrganization;
use App\Services\OurWork\OurWorkService;
use App\Services\Burial\SearchBurialService;
use App\Services\Page\IndexService;
use Carbon\Carbon;


class MainController extends Controller
{

    public static function index(){
        return IndexService::index();
    }

    public static function contacts(){
        $page=7;
        $faqs=Faq::orderBy('id', 'desc')->get();
        return view('contacts',compact('faqs','page'));
    }
   

    public static function ourWorks(){
        return OurWorkService::index();
    }

    public static function searchProductFilter(){
        return SearchBurialService::searchProductFilterPage();
    }

    public static function speczialist(){
        return view('speczialist');
    }

    

}
