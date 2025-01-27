<?php

namespace App\Services\Crematorium;



use App\Models\FaqCrematorium;
use App\Models\Crematorium;
use App\Models\ReviewCrematorium;
use App\Models\UsefulCrematorium;
use Artesaos\SEOTools\Facades\SEOTools;

class CrematoriumService {

    public static function index(){
        $city=selectCity();


        $seo="Крематории г.".$city->title;

        SEOTools::setTitle($seo);
        SEOTools::setDescription($seo);

        $usefuls=UsefulCrematorium::orderBy('id','desc')->get();
        $products=randomProductsPlace(33);
        $crematoriums_map=Crematorium::orderBy('id', 'asc')->where('city_id',$city->id)->get();
        $crematoriums=Crematorium::orderBy('id', 'asc')->where('city_id',$city->id)->paginate(6);
        
        $pages_navigation=[['Главная',route('index')],['Крематории']];

        return view('crematorium.index',compact('pages_navigation','crematoriums','city','products','usefuls','crematoriums_map'));
    }

    public static function single($id){
        $crematorium=Crematorium::find($id);

        SEOTools::setTitle(formatContent(getSeo('ritual-object','title'),$crematorium));
        SEOTools::setDescription(formatContent(getSeo('ritual-object','description'),$crematorium));
        $title_h1=formatContent(getSeo('ritual-object','h1'),$crematorium);

        $reviews=ReviewCrematorium::orderBy('id','desc')->where('status',1)->where('crematorium_id',$id)->get();
        $reviews_main=$reviews->take(3);
        $city=selectCity();
        $organizations_our=$city->cityOrganizations();
        $crematorium_all=Crematorium::all();
        $services=$crematorium->services;
        $faqs=FaqCrematorium::orderBy('id','desc')->get();
        $characteristics=json_decode($crematorium->characteristics);
        $images=$crematorium->images;
        $similar_crematoriums=Crematorium::where('city_id',$crematorium->city_id)->where('id','!=',$crematorium->id)->get();
        
        $pages_navigation=[['Главная',route('index')],['Крематории',route('crematorium')],[$crematorium->title]];

        return view('crematorium.single',compact('pages_navigation','title_h1','organizations_our','images','similar_crematoriums','crematorium','reviews','reviews_main','services','city','faqs','crematorium_all','characteristics'));
    }

    public static function addReview($data){
        $rating=null;
        if(isset($data['rating'])){
            $rating=$data['rating'];
        }
        ReviewCrematorium::create([
            'rating'=>$rating,
            'city_id'=>selectCity()->id,
            'content'=>$data['content_review'],
            'name'=>$data['name'],
            'crematorium_id'=>$data['crematorium_id']
        ]);
        return redirect()->back()->with('message_words_memory','Сообщение отправлено на проверку.');
    }
}