<?php

namespace App\Services\News;


use App\Models\News;
use App\Models\Product;
use App\Models\Service;
use App\Models\CategoryNews;
use Illuminate\Http\Request;



class IndexNews 
{
    public static function index(){
        $page=6;
        $news=News::orderBy('id', 'desc')->where('type',1)->get();
        $cats=CategoryNews::orderBy('id', 'desc')->get();
        return view('news.index',compact('news','cats','page'));
    }

    public static function singleNews($slug){
        $news=News::where('slug',$slug)->first();
        if($news==null){
            return redirect()->back();
        }
        $cats=CategoryNews::orderBy('id', 'desc')->get();
        return view('news.single',compact('news','cats'));
    }

    public static function newsCat($id){
        $cat=CategoryNews::findOrFail($id);
        $page=6;
        $news=News::orderBy('id', 'desc')->where('type',1)->where('category_id',$cat->id)->get();
        $cats=CategoryNews::orderBy('id', 'desc')->get();
        $id_cat=$cat->id;
        return view('news.index',compact('news','cats','page','id_cat'));
    }
}