<?php

namespace App\Services\Burial;

use App\Models\Burial;




class IndexBurial {

    public static function singleProduct($slug){
        $product=Burial::where('slug',$slug)->where('status',1)->first();
        if($product==null){
            return redirect()->back();
        }
        $image_monument=$product->imagesMonument();
        $image_personal=$product->imagesPersonal();
        $services=$product->services();
        $life_story=$product->lifeStory();
        $products_names=$product->productsNames();
        $products_dates=$product->productsDates();
        $memory_words=$product->memoryWords();
        return view('burial.single',compact('product','memory_words','services','products_names','products_dates','life_story','image_monument','image_personal'));
        
    }

}