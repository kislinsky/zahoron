<?php

namespace App\Services\Page;


use App\Models\Cemetery;
use App\Models\Faq;
use App\Models\City;
use App\Models\Mortuary;
use App\Models\News;
use App\Models\Review;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Services\OurWork\OurWorkService;
use App\Services\Burial\SearchBurialService;



class IndexService
{
    public static function index(){
        $city=selectCity();
        $services=Service::orderBy('id', 'desc')->get();
        $faqs=Faq::orderBy('id', 'desc')->get();
        $page=0;
        $news_video=News::orderBy('id', 'desc')->where('type',2)->get();
        $news=News::orderBy('id', 'desc')->where('type',1)->take(3)->get();
        return view('index',compact('services','news','faqs','page','city','news_video'));
    }

   
}