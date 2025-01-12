<?php

namespace App\Services\ProductPriceList;

use App\Models\ProductPriceList;
use App\Models\CategoryProductPriceList;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\SEOTools;

class ProductPriceListService {  

    public static function priceList(){
        $city=selectCity();

        $seo="Товары и услуги г.".$city->title;

        SEOTools::setTitle($seo);
        SEOTools::setDescription($seo);

        $cats=CategoryProductPriceList::orderBy('id', 'desc')->where('parent_id',null)->get();
        $page=8;
        $services=ProductPriceList::orderBy('id', 'desc')->get();

        if($services->count()<3){
            SEOMeta::setRobots('noindex, nofollow');
        }
        return view('product-pricelist.index',compact('services','page','cats','city'));
    }

    public static function serviceCategory($slug){        
        $city=selectCity();
        $cat_selected=CategoryProductPriceList::where('slug',$slug)->first();

        $seo="$cat_selected->title в г.$city->title";
        SEOTools::setTitle($seo);
        SEOTools::setDescription($seo);

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
        
        SEOTools::setTitle(formatContent(getSeo('product-pricelist-single','title'),$product));
        SEOTools::setDescription(formatContent(getSeo('product-pricelist-single','description'),$product));
        $title_h1=formatContent(getSeo('product-pricelist-single','h1'),$product);

        $advices=$product->advices();
        $reviews=$product->reviews();
        $variants=$product->variants();
        $city=selectCity();
        $faqs=$product->faqs();
        $stages=$product->stages();
        $imgs_service=$product->imgsService();
        $advantages=$product->advantages();
        return view('product-pricelist.single',compact('title_h1','product','city','advantages','imgs_service','reviews','stages','variants','faqs','advices'));
    }


    public static function ajaxProducts($city){
        $products_beatification=ProductPriceList::where('city_id',$city)->get();
        return view('components.components_form.products-beautification',compact('products_beatification'));
    }
}