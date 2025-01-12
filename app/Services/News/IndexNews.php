<?php

namespace App\Services\News;


use App\Models\News;
use App\Models\CategoryNews;
use Artesaos\SEOTools\Facades\SEOTools;



class IndexNews 
{
    public static function index(){

        SEOTools::setTitle("Новости");
        SEOTools::setDescription("Новости");

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

        SEOTools::setTitle(formatContent(getSeo('news-single','title'),$news));
        SEOTools::setDescription(formatContent(getSeo('news-single','description'),$news));
        $title_h1=formatContent(getSeo('news-single','h1'),$news);

        $cats=CategoryNews::orderBy('id', 'desc')->get();
        return view('news.single',compact('title_h1','news','cats'));
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