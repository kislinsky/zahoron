<?php

namespace App\Services\OurWork;


use App\Models\CategoryOurWork;
use Artesaos\SEOTools\Facades\SEOTools;

class OurWorkService
{
    public static function index(){
        $page=4;

        SEOTools::setTitle(formatContent(getSeo('page-our-works','title')));
        SEOTools::setDescription(formatContent(getSeo('page-our-works','description')));
        $title_h1=formatContent(getSeo('page-our-works','h1'));

        $cats=CategoryOurWork::orderBy('id', 'desc')->get();
        return view('our-works.index',compact('cats','page','title_h1'));
    }

   
}