<?php

namespace App\Services\Mortuary;

use App\Models\City;
use App\Models\Faq;
use App\Models\FaqMortuary;
use App\Models\FaqRitualObject;
use App\Models\Mortuary;
use App\Models\ReviewMortuary;
use App\Models\UsefulMortuary;
use Artesaos\SEOTools\Facades\SEOTools;


class MortuaryService {

    public static function ajaxMortuary($city){
        $areaId=City::find($city)->area_id;
        $mortuaries = Mortuary::whereHas('city', function($q) use ($areaId) {
            $q->where('area_id', $areaId);
        })->orderBy('title', 'asc')->get();
        return view('components.components_form.mortuaries',compact('mortuaries'));
    }

    public static function index(){
        $city=selectCity();

        SEOTools::setTitle(formatContent(getSeo('mortuary-catalog','title')));
        SEOTools::setDescription(formatContent(getSeo('mortuary-catalog','description')));
        $title_h1=formatContent(getSeo('mortuary-catalog','h1'));


        $usefuls=UsefulMortuary::orderBy('id','desc')->get();
        $products=randomProductsPlace(32);
        $mortuaries_map=Mortuary::orderBy('id', 'asc')->where('city_id',$city->id)->get();
        $mortuaries=Mortuary::orderBy('id', 'asc')->where('city_id',$city->id)->paginate(6);
        $faqs=Faq::where('type_object','mortuary')->orderBy('id','desc')->get();

        $pages_navigation=[['Главная',route('index')],['Морги']];

        return view('mortuary.index',compact('faqs','pages_navigation','mortuaries','city','products','usefuls','mortuaries_map','title_h1'));
    }

    public static function single($slug){
        $mortuary=Mortuary::where('slug',$slug)->first();
        addView('mortuary',$mortuary->id,user()->id ?? null,'site');

        SEOTools::setTitle(formatContent(getSeo('mortuary-single','title'),$mortuary));
        SEOTools::setDescription(formatContent(getSeo('mortuary-single','description'),$mortuary));
        $title_h1=formatContent(getSeo('mortuary-single','h1'),$mortuary);

        $reviews=ReviewMortuary::orderBy('id','desc')->where('status',1)->where('mortuary_id',$mortuary->id)->get();
        $reviews_main=$reviews->take(3);
        $city=selectCity();
        $organizations_our=$city->organizations;
        $services=$mortuary->services;
        $faqs=Faq::where('type_object','mortuary')->orderBy('id','desc')->get();
        $characteristics=json_decode($mortuary->characteristics);
        $images=$mortuary->images;
        $similar_mortuaries=Mortuary::where('city_id',$mortuary->city_id)->where('id','!=',$mortuary->id)->get();

        $pages_navigation=[['Главная',route('index')],['Морги',route('mortuaries')],[$mortuary->title]];

        return view('mortuary.single',compact('pages_navigation','title_h1','organizations_our','images','similar_mortuaries','mortuary','reviews','reviews_main','services','city','faqs','characteristics'));
    }

    public static function addReview($data){
        $rating=null;
        if(isset($data['rating'])){
            $rating=$data['rating'];
        }
        ReviewMortuary::create([
            'rating'=>$rating,
            'city_id'=>selectCity()->id,
            'content'=>$data['content_review'],
            'name'=>$data['name'],
            'mortuary_id'=>$data['mortuary_id']
        ]);
        return redirect()->back()->with('message_words_memory','Сообщение отправлено на проверку.');
    }
}