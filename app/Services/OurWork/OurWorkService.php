<?php

namespace App\Services\OurWork;


use App\Models\CategoryOurWork;
use Artesaos\SEOTools\Facades\SEOTools;

class OurWorkService
{
    public static function index(){
        $page=4;

        $seo="Наши работы";

        SEOTools::setTitle($seo);
        SEOTools::setDescription($seo);

        $cats=CategoryOurWork::orderBy('id', 'desc')->get();
        return view('our-works.index',compact('cats','page'));
    }

   
}