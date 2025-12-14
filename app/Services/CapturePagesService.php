<?php

namespace App\Services;

use App\Models\OurWork;
use Artesaos\SEOTools\Facades\SEOTools;

class CapturePagesService
{
    public static function beatification(){
        SEOTools::setTitle(formatContent(getSeo('page-dead','title')));
        SEOTools::setDescription(formatContent(getSeo('page-dead','description')));
        $title_h1=formatContent(getSeo('page-dead','h1'));
        $cemeteries_beatification=selectCity()->cemeteries;
        $categories_product_price_list=childrenCategoryPriceList();
        $user=null;
        $our_works=OurWork::all();
        return view('capture-pages.beatification',compact('cemeteries_beatification','categories_product_price_list','user','our_works','title_h1'));
    }

    public static function dead(){
        SEOTools::setTitle(formatContent(getSeo('page-beatification','title')));
        SEOTools::setDescription(formatContent(getSeo('page-beatification','description')));
        $title_h1=formatContent(getSeo('page-beatification','h1'));
        $cemeteries_beatification=selectCity()->cemeteries;
        $mortuaries=selectCity()->mortuaries;
        $categories_product_price_list=childrenCategoryPriceList();
        $user=null;
        return view('capture-pages.dead',compact('cemeteries_beatification','categories_product_price_list','user','mortuaries','title_h1'));
    }

    public static function wake(){
        SEOTools::setTitle(formatContent(getSeo('page-wake','title')));
        SEOTools::setDescription(formatContent(getSeo('page-wake','description')));
        $title_h1=formatContent(getSeo('page-wake','h1'));
        $districts=selectCity()->districts;
        $categories_product_price_list=childrenCategoryPriceList();
        $user=null;
        $our_works=OurWork::all();
        return view('capture-pages.wake',compact('categories_product_price_list','user','districts','our_works','title_h1'));
    }

    public static function organizationFuneral(){
        SEOTools::setTitle(formatContent(getSeo('page-organization-funeral','title')));
        SEOTools::setDescription(formatContent(getSeo('page-organization-funeral','description')));
        $title_h1=formatContent(getSeo('page-organization-funeral','h1'));
        $cemeteries_beatification=selectCity()->cemeteries;
        $mortuaries=selectCity()->mortuaries;
        $categories_product_price_list=childrenCategoryPriceList();
        $user=null;
        $our_works=OurWork::all();
        return view('capture-pages.organization-funeral',compact('cemeteries_beatification','categories_product_price_list','user','mortuaries','our_works','title_h1'));
    }

    public static function organizationCremation(){
        SEOTools::setTitle(formatContent(getSeo('page-organization-cremation','title')));
        SEOTools::setDescription(formatContent(getSeo('page-organization-cremation','description')));
        $title_h1=formatContent(getSeo('page-organization-cremation','h1'));
        $mortuaries=selectCity()->mortuaries;
        $categories_product_price_list=childrenCategoryPriceList();
        $user=null;
        $our_works=OurWork::all();
        return view('capture-pages.organization-cremation',compact('categories_product_price_list','user','mortuaries','our_works','title_h1'));
    }

    public static function cargo(){
        SEOTools::setTitle(formatContent(getSeo('page-cargo-200','title')));
        SEOTools::setDescription(formatContent(getSeo('page-cargo-200','description')));
        $title_h1=formatContent(getSeo('page-cargo-200','h1'));
        $mortuaries=selectCity()->mortuaries;
        $categories_product_price_list=childrenCategoryPriceList();
        $user=null;
        $our_works=OurWork::all();
        return view('capture-pages.cargo',compact('categories_product_price_list','user','mortuaries','our_works','title_h1'));
    }
}