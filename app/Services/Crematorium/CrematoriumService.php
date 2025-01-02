<?php

namespace App\Services\Crematorium;



use App\Models\City;
use App\Models\FaqCrematorium;
use App\Models\ImageCrematorium;
use App\Models\Crematorium;
use App\Models\Organization;
use App\Models\ReviewCrematorium;
use App\Models\ServiceCrematorium;
use App\Models\UsefulCrematorium;

class CrematoriumService {

    public static function index(){
        $city=selectCity();
        $usefuls=UsefulCrematorium::orderBy('id','desc')->get();
        $products=randomProductsPlace(33);
        $crematoriums_map=Crematorium::orderBy('id', 'asc')->where('city_id',$city->id)->get();
        $crematoriums=Crematorium::orderBy('id', 'asc')->where('city_id',$city->id)->paginate(6);
        return view('crematorium.index',compact('crematoriums','city','products','usefuls','crematoriums_map'));
    }

    public static function single($id){
        $crematorium=Crematorium::find($id);
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
        return view('crematorium.single',compact('organizations_our','images','similar_crematoriums','crematorium','reviews','reviews_main','services','city','faqs','crematorium_all','characteristics'));
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