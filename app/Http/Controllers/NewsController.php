<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\CategoryNews;
use Illuminate\Http\Request;
use App\Services\News\IndexNews;

class NewsController extends Controller
{
    public static function index(){
        return IndexNews::index();
    }
    public static function singleNews($slug){
       return IndexNews::singleNews($slug);
    }

    public static function newsCat($id){
        return IndexNews::newsCat($id);
    }
    
}
