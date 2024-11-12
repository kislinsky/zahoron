<?php

namespace App\Services\Mortuary;



use App\Models\City;
use App\Models\FaqMortuary;
use App\Models\FaqOrganization;
use App\Models\ImageMortuary;
use App\Models\Mortuary;
use App\Models\Organization;
use App\Models\ReviewMortuary;
use App\Models\ServiceMortuary;
use App\Models\UsefulMortuary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



class MortuaryService {

    public static function ajaxMortuary($city){
        $mortuaries=Mortuary::orderBy('title','asc')->where('city_id',$city)->get();
        return view('components.components_form.mortuaries',compact('mortuaries'));
    }

    public static function index(){
        $city=selectCity();
        $usefuls=UsefulMortuary::orderBy('id','desc')->get();
        $products=randomProductsPlace();
        $mortuaries_map=Mortuary::orderBy('id', 'asc')->where('city_id',$city->id)->get();
        $mortuaries=Mortuary::orderBy('id', 'asc')->where('city_id',$city->id)->paginate(6);
        return view('mortuary.index',compact('mortuaries','city','products','usefuls','mortuaries_map'));
    }

    public static function single($id){
        $mortuary=Mortuary::find($id);
        $reviews=ReviewMortuary::orderBy('id','desc')->where('status',1)->where('mortuary_id',$id)->get();
        $reviews_main=$reviews->take(3);
        $city=selectCity();
        $organizations_our=$city->cityOrganizations();
        $mortuary_all=Mortuary::all();
        $services=ServiceMortuary::where('mortuary_id',$id)->get();
        $faqs=FaqMortuary::orderBy('id','desc')->get();
        $characteristics=json_decode($mortuary->characteristics);
        $images=ImageMortuary::where('mortuary_id',$mortuary->id)->get();
        $similar_mortuaries=Mortuary::where('city_id',$mortuary->city_id)->where('id','!=',$mortuary->id)->get();
        return view('mortuary.single',compact('organizations_our','images','similar_mortuaries','mortuary','reviews','reviews_main','services','city','faqs','mortuary_all','characteristics'));
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