<?php

namespace App\Services\ProductPriceList;

use App\Models\ProductPriceList;
use App\Models\CategoryProductPriceList;


class ProductPriceListService {  
    public static function priceList(){
        $city=selectCity();
        $cats=CategoryProductPriceList::orderBy('id', 'desc')->where('parent_id',null)->get();
        $page=8;
        $services=ProductPriceList::orderBy('id', 'desc')->get();
        return view('product-pricelist.index',compact('services','page','cats','city'));
    }

    public static function serviceCategory($slug){
        $city=selectCity();
        $cat_selected=CategoryProductPriceList::where('slug',$slug)->first();
        $our_works=$cat_selected->ourWorks();
        $cats=CategoryProductPriceList::orderBy('id', 'desc')->where('parent_id',null)->get();
        $page=8;
        $faqs=$cat_selected->faqs();
        $services=$cat_selected->services();
        return view('product-pricelist.index',compact('services','our_works','page','cats','city','cat_selected','faqs'));
    }


    public static function singleProduct($slug){
        $product=ProductPriceList::where('slug',$slug)->first();
        if($product==null){
            return redirect()->back();
        }
        $advices=$product->advices();
        $reviews=$product->reviews();
        $variants=$product->variants();
        $city=selectCity();
        $faqs=$product->faqs();
        $stages=$product->stages();
        $imgs_service=$product->imgsService();
        $advantages=$product->advantages();
        return view('product-pricelist.single',compact('product','city','advantages','imgs_service','reviews','stages','variants','faqs','advices'));
    }


    public static function ajaxProducts($city){
        $products_beatification=ProductPriceList::where('city_id',$city)->get();
        return view('components.components_form.products-beautification',compact('products_beatification'));
    }
}