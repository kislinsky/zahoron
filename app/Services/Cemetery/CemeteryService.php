<?php

namespace App\Services\Cemetery;

use App\Filament\Resources\FaqRitualObjectResource;
use App\Models\Cemetery;
use App\Models\FaqCemetery;
use App\Models\FaqRitualObject;
use App\Models\ImageCemetery;
use App\Models\ReviewCemetery;
use App\Models\ServiceCemetery;
use App\Models\UsefulCemetery;
use Artesaos\SEOTools\Facades\SEOTools;



class CemeteryService {
    
    public static function index(){
        $city=selectCity();
        
        SEOTools::setTitle(formatContent(getSeo('cemetery-catalog','title')));
        SEOTools::setDescription(formatContent(getSeo('cemetery-catalog','description')));
        $title_h1=formatContent(getSeo('cemetery-catalog','h1'));


        $products=randomProductsPlace(29);
        $usefuls=UsefulCemetery::orderBy('id','desc')->get();
        $cemeteries_map=Cemetery::orderBy('id', 'asc')->where('city_id',$city->id)->get();
        $cemeteries=Cemetery::orderBy('priority', 'desc')->where('city_id',$city->id)->paginate(6);
        $faqs=FaqRitualObject::where('type_object','cemetery')->orderBy('id','desc')->get();
        $pages_navigation=[['Главная',route('index')],['Кладбища']];
       


        return view('cemetery.index',compact('faqs','cemeteries','city','products','usefuls','cemeteries_map','pages_navigation','title_h1'));
    }

    public static function singleCemetery($slug){
        $cemetery=Cemetery::where('slug',$slug)->first();

        addView('cemetery',$cemetery->id,user()->id ?? null,'site');

        SEOTools::setTitle(formatContent(getSeo('cemetery-single','title'),$cemetery));
        SEOTools::setDescription(formatContent(getSeo('cemetery-single','description'),$cemetery));
        $title_h1=formatContent(getSeo('cemetery-single','h1'),$cemetery);

        $reviews=ReviewCemetery::orderBy('id','desc')->where('status',1)->where('cemetery_id',$cemetery->id)->get();
        $reviews_main=$reviews->take(3);
        $organizations_our=$cemetery->cemeteryOrganiaztions();
        $city=selectCity();
        $services=ServiceCemetery::where('cemetery_id',$cemetery->id)->get();
        $faqs=FaqCemetery::orderBy('id','desc')->get();
        $characteristics=json_decode($cemetery->characteristics);
        $images=ImageCemetery::where('cemetery_id',$cemetery->id)->get();
        $similar_cemeteries=Cemetery::orderBy('priority', 'desc')->where('city_id',$cemetery->city_id)->where('id','!=',$cemetery->id)->get();

        $pages_navigation=[['Главная',route('index')],['Кладбища',route('cemeteries')],[$cemetery->title]];

        return view('cemetery.single',compact('pages_navigation','title_h1','images','similar_cemeteries','cemetery','reviews','reviews_main','organizations_our','services','city','faqs','characteristics'));
    }

    public static function ajaxCemetery($city){
        $cemeteries_beatification=Cemetery::orderBy('title','asc')->where('city_id',$city)->get();
        return view('components.components_form.cemetery',compact('cemeteries_beatification'));
    }

    public static function addReview($data){
        $rating=null;
        if(isset($data['rating'])){
            $rating=$data['rating'];
        }
        ReviewCemetery::create([
            'rating'=>$rating,
            'city_id'=>selectCity()->id,
            'content'=>$data['content_review'],
            'name'=>$data['name'],
            'cemetery_id'=>$data['cemetery_id']
        ]);
        return redirect()->back()->with('message_words_memory','Сообщение отправлено на проверку.');
    }
    

    

}