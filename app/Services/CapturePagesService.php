<?php

namespace App\Services;

use App\Models\OurWork;
use App\Models\Review;
use App\Models\Acf;
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
        $our_works=Acf::where('page_id',26)->where('name','img')->get();
        $reviews=Review::where('review_category_id',1)->get();
        
        // Новые SEO блоки
        $advantages_1_title=formatContent(getSeo('page-dead','h2_1'));
        $advantages_1_text=formatContent(getSeo('page-dead','p_1'));
        $advantages_2_title=formatContent(getSeo('page-dead','h2_2'));
        $advantages_2_text=formatContent(getSeo('page-dead','p_2'));
        $advantages_3_title=formatContent(getSeo('page-dead','h2_3'));
        $advantages_3_text=formatContent(getSeo('page-dead','p_3'));
        $instruction_1_title=formatContent(getSeo('page-dead','h2_4'));
        $instruction_1_text=formatContent(getSeo('page-dead','p_4'));
        $instruction_2_title=formatContent(getSeo('page-dead','h2_5'));
        $instruction_2_text=formatContent(getSeo('page-dead','p_5'));
        $instruction_3_title=formatContent(getSeo('page-dead','h2_6'));
        $instruction_3_text=formatContent(getSeo('page-dead','p_6'));
        $text_block_title=formatContent(getSeo('page-dead','h2_7'));
        $text_block_text=formatContent(getSeo('page-dead','p_7'));
        
        return view('capture-pages.beatification',compact(
            'reviews',
            'cemeteries_beatification',
            'categories_product_price_list',
            'user',
            'our_works',
            'title_h1',
            'advantages_1_title',
            'advantages_1_text',
            'advantages_2_title',
            'advantages_2_text',
            'advantages_3_title',
            'advantages_3_text',
            'instruction_1_title',
            'instruction_1_text',
            'instruction_2_title',
            'instruction_2_text',
            'instruction_3_title',
            'instruction_3_text',
            'text_block_title',
            'text_block_text'
        ));
    }

    public static function dead(){
        SEOTools::setTitle(formatContent(getSeo('page-beatification','title')));
        SEOTools::setDescription(formatContent(getSeo('page-beatification','description')));
        $title_h1=formatContent(getSeo('page-beatification','h1'));
        $cemeteries_beatification=selectCity()->cemeteries;
        $mortuaries=selectCity()->mortuaries;
        $categories_product_price_list=childrenCategoryPriceList();
        $user=null;
        $our_works=Acf::where('page_id',27)->where('name','img')->get();
        $reviews=Review::where('review_category_id',6)->get();
        
        // Новые SEO блоки
        $advantages_1_title=formatContent(getSeo('page-beatification','h2_1'));
        $advantages_1_text=formatContent(getSeo('page-beatification','p_1'));
        $advantages_2_title=formatContent(getSeo('page-beatification','h2_2'));
        $advantages_2_text=formatContent(getSeo('page-beatification','p_2'));
        $advantages_3_title=formatContent(getSeo('page-beatification','h2_3'));
        $advantages_3_text=formatContent(getSeo('page-beatification','p_3'));
        $instruction_1_title=formatContent(getSeo('page-beatification','h2_4'));
        $instruction_1_text=formatContent(getSeo('page-beatification','p_4'));
        $instruction_2_title=formatContent(getSeo('page-beatification','h2_5'));
        $instruction_2_text=formatContent(getSeo('page-beatification','p_5'));
        $instruction_3_title=formatContent(getSeo('page-beatification','h2_6'));
        $instruction_3_text=formatContent(getSeo('page-beatification','p_6'));
        $text_block_title=formatContent(getSeo('page-beatification','h2_7'));
        $text_block_text=formatContent(getSeo('page-beatification','p_7'));
        
        return view('capture-pages.dead',compact(
            'our_works',
            'reviews',
            'cemeteries_beatification',
            'categories_product_price_list',
            'user',
            'mortuaries',
            'title_h1',
            'advantages_1_title',
            'advantages_1_text',
            'advantages_2_title',
            'advantages_2_text',
            'advantages_3_title',
            'advantages_3_text',
            'instruction_1_title',
            'instruction_1_text',
            'instruction_2_title',
            'instruction_2_text',
            'instruction_3_title',
            'instruction_3_text',
            'text_block_title',
            'text_block_text'
        ));
    }

    public static function wake(){
        SEOTools::setTitle(formatContent(getSeo('page-wake','title')));
        SEOTools::setDescription(formatContent(getSeo('page-wake','description')));
        $title_h1=formatContent(getSeo('page-wake','h1'));
        $districts=selectCity()->districts;
        $categories_product_price_list=childrenCategoryPriceList();
        $user=null;
        $our_works=OurWork::all();
        $our_works=Acf::where('page_id',25)->where('name','img')->get();
        $reviews=Review::where('review_category_id',5)->get();
        
        // Новые SEO блоки
        $advantages_1_title=formatContent(getSeo('page-wake','h2_1'));
        $advantages_1_text=formatContent(getSeo('page-wake','p_1'));
        $advantages_2_title=formatContent(getSeo('page-wake','h2_2'));
        $advantages_2_text=formatContent(getSeo('page-wake','p_2'));
        $advantages_3_title=formatContent(getSeo('page-wake','h2_3'));
        $advantages_3_text=formatContent(getSeo('page-wake','p_3'));
        $instruction_1_title=formatContent(getSeo('page-wake','h2_4'));
        $instruction_1_text=formatContent(getSeo('page-wake','p_4'));
        $instruction_2_title=formatContent(getSeo('page-wake','h2_5'));
        $instruction_2_text=formatContent(getSeo('page-wake','p_5'));
        $instruction_3_title=formatContent(getSeo('page-wake','h2_6'));
        $instruction_3_text=formatContent(getSeo('page-wake','p_6'));
        $text_block_title=formatContent(getSeo('page-wake','h2_7'));
        $text_block_text=formatContent(getSeo('page-wake','p_7'));
        
        return view('capture-pages.wake',compact(
            'our_works',
            'reviews',
            'categories_product_price_list',
            'user',
            'districts',
            'our_works',
            'title_h1',
            'advantages_1_title',
            'advantages_1_text',
            'advantages_2_title',
            'advantages_2_text',
            'advantages_3_title',
            'advantages_3_text',
            'instruction_1_title',
            'instruction_1_text',
            'instruction_2_title',
            'instruction_2_text',
            'instruction_3_title',
            'instruction_3_text',
            'text_block_title',
            'text_block_text'
        ));
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
        $our_works=Acf::where('page_id',23)->where('name','img')->get();
        $reviews=Review::where('review_category_id',2)->get();
        
        // Новые SEO блоки
        $advantages_1_title=formatContent(getSeo('page-organization-funeral','h2_1'));
        $advantages_1_text=formatContent(getSeo('page-organization-funeral','p_1'));
        $advantages_2_title=formatContent(getSeo('page-organization-funeral','h2_2'));
        $advantages_2_text=formatContent(getSeo('page-organization-funeral','p_2'));
        $advantages_3_title=formatContent(getSeo('page-organization-funeral','h2_3'));
        $advantages_3_text=formatContent(getSeo('page-organization-funeral','p_3'));
        $instruction_1_title=formatContent(getSeo('page-organization-funeral','h2_4'));
        $instruction_1_text=formatContent(getSeo('page-organization-funeral','p_4'));
        $instruction_2_title=formatContent(getSeo('page-organization-funeral','h2_5'));
        $instruction_2_text=formatContent(getSeo('page-organization-funeral','p_5'));
        $instruction_3_title=formatContent(getSeo('page-organization-funeral','h2_6'));
        $instruction_3_text=formatContent(getSeo('page-organization-funeral','p_6'));
        $text_block_title=formatContent(getSeo('page-organization-funeral','h2_7'));
        $text_block_text=formatContent(getSeo('page-organization-funeral','p_7'));
        
        return view('capture-pages.organization-funeral',compact(
            'our_works',
            'reviews',
            'cemeteries_beatification',
            'categories_product_price_list',
            'user',
            'mortuaries',
            'our_works',
            'title_h1',
            'advantages_1_title',
            'advantages_1_text',
            'advantages_2_title',
            'advantages_2_text',
            'advantages_3_title',
            'advantages_3_text',
            'instruction_1_title',
            'instruction_1_text',
            'instruction_2_title',
            'instruction_2_text',
            'instruction_3_title',
            'instruction_3_text',
            'text_block_title',
            'text_block_text'
        ));
    }

    public static function organizationCremation(){
        SEOTools::setTitle(formatContent(getSeo('page-organization-cremation','title')));
        SEOTools::setDescription(formatContent(getSeo('page-organization-cremation','description')));
        $title_h1=formatContent(getSeo('page-organization-cremation','h1'));
        $mortuaries=selectCity()->mortuaries;
        $categories_product_price_list=childrenCategoryPriceList();
        $user=null;
        $our_works=OurWork::all();
        $our_works=Acf::where('page_id',24)->where('name','img')->get();
        $reviews=Review::where('review_category_id',3)->get();
        
        // Новые SEO блоки
        $advantages_1_title=formatContent(getSeo('page-organization-cremation','h2_1'));
        $advantages_1_text=formatContent(getSeo('page-organization-cremation','p_1'));
        $advantages_2_title=formatContent(getSeo('page-organization-cremation','h2_2'));
        $advantages_2_text=formatContent(getSeo('page-organization-cremation','p_2'));
        $advantages_3_title=formatContent(getSeo('page-organization-cremation','h2_3'));
        $advantages_3_text=formatContent(getSeo('page-organization-cremation','p_3'));
        $instruction_1_title=formatContent(getSeo('page-organization-cremation','h2_4'));
        $instruction_1_text=formatContent(getSeo('page-organization-cremation','p_4'));
        $instruction_2_title=formatContent(getSeo('page-organization-cremation','h2_5'));
        $instruction_2_text=formatContent(getSeo('page-organization-cremation','p_5'));
        $instruction_3_title=formatContent(getSeo('page-organization-cremation','h2_6'));
        $instruction_3_text=formatContent(getSeo('page-organization-cremation','p_6'));
        $text_block_title=formatContent(getSeo('page-organization-cremation','h2_7'));
        $text_block_text=formatContent(getSeo('page-organization-cremation','p_7'));
        
        return view('capture-pages.organization-cremation',compact(
            'our_works',
            'reviews',
            'categories_product_price_list',
            'user',
            'mortuaries',
            'our_works',
            'title_h1',
            'advantages_1_title',
            'advantages_1_text',
            'advantages_2_title',
            'advantages_2_text',
            'advantages_3_title',
            'advantages_3_text',
            'instruction_1_title',
            'instruction_1_text',
            'instruction_2_title',
            'instruction_2_text',
            'instruction_3_title',
            'instruction_3_text',
            'text_block_title',
            'text_block_text'
        ));
    }

    public static function cargo(){
        SEOTools::setTitle(formatContent(getSeo('page-cargo-200','title')));
        SEOTools::setDescription(formatContent(getSeo('page-cargo-200','description')));
        $title_h1=formatContent(getSeo('page-cargo-200','h1'));
        $mortuaries=selectCity()->mortuaries;
        $categories_product_price_list=childrenCategoryPriceList();
        $user=null;
        $our_works=Acf::where('page_id',22)->where('name','img')->get();
        $our_works=OurWork::all();
        $reviews=Review::where('review_category_id',4)->get();
        
        // Новые SEO блоки
        $advantages_1_title=formatContent(getSeo('page-cargo-200','h2_1'));
        $advantages_1_text=formatContent(getSeo('page-cargo-200','p_1'));
        $advantages_2_title=formatContent(getSeo('page-cargo-200','h2_2'));
        $advantages_2_text=formatContent(getSeo('page-cargo-200','p_2'));
        $advantages_3_title=formatContent(getSeo('page-cargo-200','h2_3'));
        $advantages_3_text=formatContent(getSeo('page-cargo-200','p_3'));
        $instruction_1_title=formatContent(getSeo('page-cargo-200','h2_4'));
        $instruction_1_text=formatContent(getSeo('page-cargo-200','p_4'));
        $instruction_2_title=formatContent(getSeo('page-cargo-200','h2_5'));
        $instruction_2_text=formatContent(getSeo('page-cargo-200','p_5'));
        $instruction_3_title=formatContent(getSeo('page-cargo-200','h2_6'));
        $instruction_3_text=formatContent(getSeo('page-cargo-200','p_6'));
        $text_block_title=formatContent(getSeo('page-cargo-200','h2_7'));
        $text_block_text=formatContent(getSeo('page-cargo-200','p_7'));
        
        return view('capture-pages.cargo',compact(
            'our_works',
            'reviews',
            'categories_product_price_list',
            'user',
            'mortuaries',
            'our_works',
            'title_h1',
            'advantages_1_title',
            'advantages_1_text',
            'advantages_2_title',
            'advantages_2_text',
            'advantages_3_title',
            'advantages_3_text',
            'instruction_1_title',
            'instruction_1_text',
            'instruction_2_title',
            'instruction_2_text',
            'instruction_3_title',
            'instruction_3_text',
            'text_block_title',
            'text_block_text'
        ));
    }
}