<?php

namespace App\Services\Burial;

use App\Models\Burial;
use Artesaos\SEOTools\Facades\SEOTools;

class IndexBurial {

    public static function singleProduct($slug){
        $product=Burial::where('slug',$slug)->where('status',1)->first();
        if($product==null){
            return redirect()->back();
        }
        
        SEOTools::setTitle(formatContentBurial(getSeo('burial-single','title'),$product));
        SEOTools::setDescription(formatContentBurial(getSeo('burial-single','description'),$product));
        $title_h1=formatContentBurial(getSeo('burial-single','h1'),$product);

        $image_monument=$product->imageMonumentAccept;
        $image_personal=$product->imagePersonalAccept;
        $services=$product->services();
        $life_story=$product->lifeStory;
        $products_names=$product->productsNames();
        $products_dates=$product->productsDates();
        $memory_words=$product->wordsMemoryAccept;
        return view('burial.single',compact('title_h1','product','memory_words','services','products_names','products_dates','life_story','image_monument','image_personal'));
        
    }

}