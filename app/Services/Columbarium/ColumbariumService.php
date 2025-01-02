<?php

namespace App\Services\Columbarium;



use App\Models\City;
use App\Models\FaqColumbarium;
use App\Models\ImageColumbarium;
use App\Models\Columbarium;
use App\Models\Organization;
use App\Models\ReviewColumbarium;
use App\Models\ServiceColumbarium;
use App\Models\UsefulColumbarium;

class ColumbariumService {

    public static function index(){
        $city=selectCity();
        $products=randomProductsPlace(35);
        $usefuls=UsefulColumbarium::orderBy('id','desc')->get();
        $columbariums_map=Columbarium::orderBy('id', 'asc')->where('city_id',$city->id)->get();
        $columbariums=Columbarium::orderBy('id', 'asc')->where('city_id',$city->id)->paginate(6);
        return view('columbarium.index',compact('columbariums','city','products','usefuls','columbariums_map'));
    }


    public static function single($id){
        $columbarium=Columbarium::find($id);
        $reviews=ReviewColumbarium::orderBy('id','desc')->where('status',1)->where('columbarium_id',$id)->get();
        $reviews_main=$reviews->take(3);
        $city=selectCity();
        
        $organizations_our = City::with(['cityOrganizations' => function($query) {
            $query->where('role','organization'); // или 'asc' для возрастающей сортировки
        }])->get();

        $organizations_our=$city->cityOrganizations();
        $columbarium_all=Columbarium::all();
        $services=$columbarium->services;
        $faqs=FaqColumbarium::orderBy('id','desc')->get();
        $characteristics=json_decode($columbarium->characteristics);
        $images=$columbarium->images;
        $similar_columbariums=Columbarium::where('city_id',$columbarium->city_id)->where('id','!=',$columbarium->id)->get();
        return view('columbarium.single',compact('organizations_our','images','similar_columbariums','columbarium','reviews','reviews_main','services','city','faqs','columbarium_all','characteristics'));
    }

    public static function addReview($data){
        $rating=null;
        if(isset($data['rating'])){
            $rating=$data['rating'];
        }
        Reviewcolumbarium::create([
            'rating'=>$rating,
            'city_id'=>selectCity()->id,
            'content'=>$data['content_review'],
            'name'=>$data['name'],
            'columbarium_id'=>$data['columbarium_id']
        ]);
        return redirect()->back()->with('message_words_memory','Сообщение отправлено на проверку.');
    }
}