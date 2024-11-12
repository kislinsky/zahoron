<?php

namespace App\Http\Controllers;

use App\Models\Cemetery;
use App\Models\Faq;
use App\Models\City;
use App\Models\News;
use App\Models\Organization;
use App\Models\Page;
use App\Models\Product;
use App\Models\Review;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Services\OurWork\OurWorkService;
use App\Services\Burial\SearchBurialService;
use App\Services\Page\IndexService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Artisan;

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
