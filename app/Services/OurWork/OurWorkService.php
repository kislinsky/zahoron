<?php

namespace App\Services\OurWork;


use App\Models\News;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Models\CategoryOurWork;



class OurWorkService
{
    public static function index(){
        $page=4;
        $cats=CategoryOurWork::orderBy('id', 'desc')->get();
        return view('our-works.index',compact('cats','page'));
    }

   
}