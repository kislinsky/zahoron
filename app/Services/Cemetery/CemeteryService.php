<?php

namespace App\Services\Cemetery;


use App\Models\News;
use App\Models\Service;
use App\Models\Cemetery;
use App\Models\CategoryNews;
use App\Models\FaqCemetery;
use App\Models\ImageCemetery;
use App\Models\Organization;
use App\Models\ReviewCemetery;
use App\Models\ServiceCemetery;
use App\Models\UsefulCemetery;
use Illuminate\Http\Request;
use Artesaos\SEOTools\Facades\SEOTools;



class CemeteryService {
    
    public static function index(){
        
        $city=selectCity();
        
        $seo="Кладбища г.".$city->title;

        SEOTools::setTitle($seo);
        SEOTools::setDescription($seo);

        $products=randomProductsPlace(29);
        $usefuls=UsefulCemetery::orderBy('id','desc')->get();
        $cemeteries_map=Cemetery::orderBy('id', 'asc')->where('city_id',$city->id)->get();
        $cemeteries=Cemetery::orderBy('id', 'asc')->where('city_id',$city->id)->paginate(6);

        $pages_navigation=[['Главная',route('index')],['Кладбища']];
        
        return view('cemetery.index',compact('cemeteries','city','products','usefuls','cemeteries_map','pages_navigation'));
    }

    public static function singleCemetery($cemetery){

        addView('cemetery',$cemetery->id,user()->id ?? null,'site');

        SEOTools::setTitle(formatContent(getSeo('ritual-object','title'),$cemetery));
        SEOTools::setDescription(formatContent(getSeo('ritual-object','description'),$cemetery));
        $title_h1=formatContent(getSeo('ritual-object','h1'),$cemetery);


        $id=$cemetery->id;
        $reviews=ReviewCemetery::orderBy('id','desc')->where('status',1)->where('cemetery_id',$id)->get();
        $reviews_main=$reviews->take(3);
        $organizations_our=$cemetery->cemeteryOrganiaztions();
        $city=selectCity();
        $cemetery_all=Cemetery::all();
        $services=ServiceCemetery::where('cemetery_id',$id)->get();
        $faqs=FaqCemetery::orderBy('id','desc')->get();
        $characteristics=json_decode($cemetery->characteristics);
        $images=ImageCemetery::where('cemetery_id',$cemetery->id)->get();
        $similar_cemeteries=Cemetery::where('city_id',$cemetery->city_id)->where('id','!=',$cemetery->id)->get();

        $pages_navigation=[['Главная',route('index')],['Кладбища',route('cemeteries')],[$cemetery->title]];

        return view('cemetery.single',compact('pages_navigation','title_h1','images','similar_cemeteries','cemetery','reviews','reviews_main','organizations_our','services','city','faqs','cemetery_all','characteristics'));
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