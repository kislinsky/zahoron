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
use Artesaos\SEOTools\Facades\SEOTools;



class IndexService
{
    public static function index(){      
        $city=selectCity();
        $services=Service::orderBy('id', 'desc')->get();
        $faqs=Faq::orderBy('id', 'desc')->get();
        
        SEOTools::setTitle(formatContent(getSeo('index-page','title'),$model=null));
        SEOTools::setDescription(formatContent(getSeo('index-page','description'),$model=null));
        $title_h1=formatContent(getSeo('index-page','h1'),$model=null);

        $page=0;
        $news_video=News::orderBy('id', 'desc')->where('type',2)->get();
        $news=News::orderBy('id', 'desc')->where('type',1)->take(3)->get();
        return view('index',compact('title_h1','services','news','faqs','page','city','news_video'));
    }

   
    public static function acceptCookie($data){
        setcookie('cookie_consent',$data['value'],time() + (20 * 24 * 60 ), '/');
        return true;
    }
}