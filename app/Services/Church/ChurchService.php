<?php

namespace App\Services\Church;

use App\Models\Church;
use App\Models\Faq;
use App\Models\ReviewChurch;
use Artesaos\SEOTools\Facades\SEOTools;

class ChurchService {

    public static function index(){
        $city=selectCity();

        $seo="Церкви г.".$city->title;

        SEOTools::setTitle($seo);
        SEOTools::setDescription($seo);

        $products=randomProductsPlace(32);
        $churches_map=Church::orderBy('id', 'asc')->where('city_id',$city->id)->get();
        $churches=Church::orderBy('id', 'asc')->where('city_id',$city->id)->paginate(6);
        $faqs=Faq::where('type_object','churche')->orderBy('id','desc')->get();

        $pages_navigation=[['Главная',route('index')],['Церкви']];

        return view('church.index',compact('faqs','pages_navigation','churches','city','products','churches_map'));
    }

    public static function single($slug){
        $object=Church::where('slug',$slug)->first();

        addView('church',$object->id,user()->id ?? null,'site');

        SEOTools::setTitle(formatContent(getSeo('ritual-object','title'),$object));
        SEOTools::setDescription(formatContent(getSeo('ritual-object','description'),$object));
        $title_h1=formatContent(getSeo('ritual-object','h1'),$object);

        $reviews=ReviewChurch::orderBy('id','desc')->where('status',1)->where('church_id',$object->id)->get();
        $reviews_main=$reviews->take(3);
        $city=selectCity();
        $organizations_our=$city->organizations;
        $characteristics=json_decode($object->characteristics);
        $images=$object->images;
        $similar_objects=Church::where('city_id',$object->city_id)->where('id','!=',$object->id)->get();

        $pages_navigation=[['Главная',route('index')],['Церкви',route('churches')],[$object->title]];

        return view('church.single',compact('pages_navigation','title_h1','organizations_our','images','similar_objects','object','reviews','reviews_main','city','characteristics'));
    }

    public static function addReview($data){
        $rating=null;
        if(isset($data['rating'])){
            $rating=$data['rating'];
        }
        ReviewChurch::create([
            'rating'=>$rating,
            'city_id'=>selectCity()->id,
            'content'=>$data['content_review'],
            'name'=>$data['name'],
            'church_id'=>$data['church_id']
        ]);
        return redirect()->back()->with('message_words_memory','Сообщение отправлено на проверку.');
    }
    
}