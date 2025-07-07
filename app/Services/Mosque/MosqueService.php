<?php

namespace App\Services\Mosque;

use App\Models\Mosque;
use App\Models\Organization;
use App\Models\ReviewMosque;
use Artesaos\SEOTools\Facades\SEOTools;

class MosqueService {

    public static function index(){
        $city=selectCity();

        $seo="Мечети г.".$city->title;

        SEOTools::setTitle($seo);
        SEOTools::setDescription($seo);

        $products=randomProductsPlace(32);
        $mosques_map=Mosque::orderBy('id', 'asc')->where('city_id',$city->id)->get();
        $mosques=Mosque::orderBy('id', 'asc')->where('city_id',$city->id)->paginate(6);

        $pages_navigation=[['Главная',route('index')],['Мечети']];

        return view('mosque.index',compact('pages_navigation','mosques_map','city','products','mosques'));
    
    }


    public static function single($id){
        $object=Mosque::find($id);
        addView('mosque',$object->id,user()->id ?? null,'site');

        SEOTools::setTitle(formatContent(getSeo('ritual-object','title'),$object));
        SEOTools::setDescription(formatContent(getSeo('ritual-object','description'),$object));
        $title_h1=formatContent(getSeo('ritual-object','h1'),$object);

        $reviews=ReviewMosque::orderBy('id','desc')->where('status',1)->where('mosque_id',$id)->get();
        $reviews_main=$reviews->take(3);
        $city=selectCity();
        $organizations_our=$city->organizations;
        $characteristics=json_decode($object->characteristics);
        $images=$object->images;
        $similar_objects=Mosque::where('city_id',$object->city_id)->where('id','!=',$object->id)->get();

        $pages_navigation=[['Главная',route('index')],['Мечети',route('mosques')],[$object->title]];

        return view('mosque.single',compact('pages_navigation','title_h1','organizations_our','images','similar_objects','object','reviews','reviews_main','city','characteristics'));
    }

    public static function addReview($data){
        $rating=null;
        if(isset($data['rating'])){
            $rating=$data['rating'];
        }
        ReviewMosque::create([
            'rating'=>$rating,
            'city_id'=>selectCity()->id,
            'content'=>$data['content_review'],
            'name'=>$data['name'],
            'mosque_id'=>$data['mosque_id']
        ]);
        return redirect()->back()->with('message_words_memory','Сообщение отправлено на проверку.');
    }
}