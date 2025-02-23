<?php

namespace App\Services\Product;

use App\Models\ActivityCategoryOrganization;
use App\Models\Product;
use App\Models\Cemetery;
use App\Models\ImageProduct;
use Illuminate\Http\Request;
use App\Models\CommentProduct;
use App\Models\AdditionProduct;
use App\Models\CategoryProduct;
use App\Models\City;
use App\Models\District;
use App\Models\MemorialMenu;
use App\Models\Mortuary;
use App\Models\Organization;
use App\Models\ProductParameters;
use App\Models\User;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\SEOTools;

class ProductService
{
    public static function singleProduct($slug){
        $product=Product::where('slug',$slug)->first();
        if($product==null){
            return redirect()->back();
        }
        $id=$product->id;
        $organization=$product->organization;
        $agent=$organization->user;
        $additionals=[];
        $ids_additionals=$product->category;
        if($ids_additionals->additional!=null){
            $additionals=AdditionProduct::whereIn('id',json_decode($ids_additionals->additional))->get();
        }
        $size=explode('|',$product->size);
        $comments=$product->reviews();
        $images=$product->getImages;
        $parameters=$product->getParam;
        $category=$product->category;
        $sales=ActivityCategoryOrganization::where('organization_id',$organization->id)->where('category_children_id',$category->id)->where('sales','!=',null)->get();
        $city=selectCity();  

        $cemeteries=Cemetery::whereIn('id',explode(',', rtrim($product->organization->cemetery_ids,',')))->get();
        
        $cities=$city->area->edge->area->flatMap(function($area_one) {
            return $area_one->cities; // Здесь предполагается, что есть связь mortuaries
        });
        $mortuaries=Mortuary::whereIn('city_id',$cities->pluck('id'))->get();
        $category_products=Product::orderBy('id','desc')->where('category_id',$product->category_id)->where('id','!=',$product->id)->where('city_id',$product->city_id)->get();

        
        if($category->type=='funeral-service'){
            SEOTools::setTitle(formatContent(getSeo('product-service-single','title'),$product));
            SEOTools::setDescription(formatContent(getSeo('product-service-single','description'),$product));
            $title_h1=formatContent(getSeo('product-service-single','h1'),$product);
        }
        else{
            SEOTools::setTitle(formatContent(getSeo('product-single','title'),$product));
            SEOTools::setDescription(formatContent(getSeo('product-single','description'),$product));
            $title_h1=formatContent(getSeo('product-single','h1'),$product);
        }


        if($category->slug=='pominal-nyh-obedy'){
            $district=$product->district;
            $memorial_menu=$product->memorialMenu;
            return view('product.single.single-menu',compact('title_h1','product','sales','agent','city','district','images','organization','memorial_menu','category','additionals','comments','category_products'));
        }

        if($category->slug=='pominal-nye-zaly'){
            $district=$product->district;
            return view('product.single.single-hall',compact('title_h1','product','sales','agent','city','district','images','organization','category','additionals','comments','category_products'));
        }

        if($category->slug=='organizacia-pohoron'){
            return view('product.single.single-organization-funeral',compact('title_h1','mortuaries','product','cemeteries','sales','agent','city','images','organization','parameters','category','additionals','comments','category_products'));
        }

        if($category->slug=='organizacia-kremacii'){
            return view('product.single.single-cremation',compact('title_h1','product','mortuaries','sales','agent','city','images','organization','parameters','category','additionals','comments','category_products'));
        }

        if($category->slug=='otpravka-gruz-200'){
            return view('product.single.single-shipment-200-cargo',compact('title_h1','product','mortuaries','sales','agent','city','images','organization','parameters','category','additionals','comments','category_products'));
        }
        
        if($category->slug=='knopka-mogil'){
            return view('product.single.single-button-grave',compact('title_h1','product','cemeteries','sales','agent','city','images','organization','parameters','category','additionals','comments','category_products'));
        }

        if($category->slug=='knopka-mogil'){
            return view('product.single.single-button-grave',compact('title_h1','product','cemeteries','sales','agent','city','images','organization','parameters','category','additionals','comments','category_products'));
        }
        
        $category_products=Product::orderBy('id','desc')->where('category_id',$product->category_id)->where('id','!=',$product->id)->where('organization_id',$product->organization->id)->get();

        return view('product.single.single',compact('cemeteries','title_h1','agent','product','organization','sales','images','parameters','category','size','additionals','comments','category_products'));
    }



    public static function marketplace($slug,$data){
        $cat_slug=CategoryProduct::where('slug',$slug)->first();
        $data['category']=categoryProductChoose()->id;
        if($cat_slug!=null){
            $data['category']=$cat_slug->id;
        }



        $city=selectCity();
        $page=2;
        $materials_filter=Product::pluck('material')->unique()->filter(function ($value) { return !is_null($value); });
        $layerings=Product::pluck('layering')->unique()->filter(function ($value) { return !is_null($value); });
        $sort='Сортировка';
        if(isset($data['sort']) && $data['sort']!=null){
           $sort=$data['sort'];
        }
        $reviews=reviewProducts($data);
        $cats=CategoryProduct::orderBy('id','desc')->where('parent_id',null)->get();
        $products=filterProducts($data);
        $faqs=faqCatsProduct($data);
        $cemeteries_all=$city->cemeteries;
        $cemetery=cemeteryProduct($data);
        $district=null;
        if(isset($data['district_id'])  && $data['district_id']!='undefined'){
           $district=District::find($data['district_id']);
        }
        $category=ajaxCatContent($data);
        $districts_all=$city->districts;

        if($products->count()<3){
            SEOMeta::setRobots('noindex, nofollow');
        }

        SEOTools::setTitle(formatContentCategory(getSeo($category->slug.'-marketplace','title'),$category,$products));
        SEOTools::setDescription(formatContentCategory(getSeo($category->slug.'-marketplace','description'),$category,$products));
        $title_h1=formatContentCategory(getSeo($category->slug.'-marketplace','h1'),$category,$products);

        $pages_navigation=[['Главная',route('index')],['Маркетплэйс',route('marketplace')],[$category->title]];

        
        return view('product.marketplace',compact('pages_navigation','title_h1','district','layerings','sort','districts_all','cemeteries_all','reviews','products','city','cats','materials_filter','faqs','cemetery','category','page'));

    }



    // public static function category($data,$category){
    //     $city=selectCity();
    //     $page=2;
    //     $materials_filter=Product::pluck('material')->unique()->filter(function ($value) { return !is_null($value); });
    //     $layerings=Product::pluck('layering')->unique()->filter(function ($value) { return !is_null($value); });
    //     $price_all=cartPrice();
    //     $sort='Сортировка';
    //     if(isset($data['sort']) && $data['sort']!=null){
    //        $sort=$data['sort'];
    //     }
    //     $reviews=reviewProducts($data);
    //     $cats=CategoryProduct::orderBy('id','desc')->where('parent_id',null)->get();
    //     $products=filterProducts($data);
    //     $faqs=faqCatsProduct($data);
    //     $cemeteries_all=$city->cemeteries();
    //     $cemetery=cemeteryProduct($data);
    //     $district=null;
    //     if(isset($data['district_id'])  && $data['district_id']!='undefined'){
    //        $district=District::find($data['district_id']);
    //     }
    //     $category=ajaxCatContent($data);
    //     $districts_all=$city->districts();
    //     return view('product.marketplace',compact('district','layerings','sort','districts_all','cemeteries_all','reviews','products','city','cats','price_all','materials_filter','faqs','cemetery','category','page'));

    // }




    public static function ajaxCatReviews($data){
        $reviews=reviewProducts($data);
        return view('product.components.catalog.reviews',compact('reviews'));
    }


    public static function filterShow($data){
        $category=categoryProductChoose();
        if(isset($data['category'])){
            if($data['category']!='undefined'){
                $category=CategoryProduct::findOrFail($data['category']);
            }
        }
        $products=filterProducts($data);
        if($category->parent_id==36){
            return view("product.components.catalog.products-show-beautification", compact("products"));
        }
        if($category->parent_id==31){
            return view("product.components.catalog.products-show-funeral-service", compact("products"));
       }
       if($category->parent_id==45){
            return view("product.components.catalog.products-show-organization-commemorations", compact("products"));
       }

        return view("product.components.catalog.products-show-beautification", compact("products"));
    }

    public static function ajaxProductCat($data){ 
        $faqs=faqCatsProduct($data);
        return view("product.components.catalog.faq-show", compact("faqs"));
    }

    public static function ajaxCemeteryCat($data){
        $cemetery=cemeteryProduct($data);
        return view("product.components.catalog.cemetery-show", compact("cemetery"));
    }
    public static function ajaxCatContent($data){
        $category=ajaxCatContent($data);
        return view("product.components.catalog.cat-content-show", compact("category"));
    }
    public static function ajaxCatManual($data){
        $category=ajaxCatManual($data);
        return view("product.components.catalog.cat-manual-show", compact("category"));
    }

    public static function ajaxTitle($data){
        // $cemetery=null;
        // $district=null;
        $category=null;
        if(isset($data['category'])){
            $category=CategoryProduct::find($data['category']);
        }
        
        // if(isset($data['district_id'])){
        //     $district=District::find($data['district_id']);
        // }
        // if(isset($data['cemetery_id'])){
        //     $cemetery=Cemetery::find($data['cemetery_id']);
        // }
        $city=selectCity();
        $products=filterProducts($data);
        $title_h1=formatContentCategory(getSeo($category->slug.'-marketplace','h1'),$category,$products);

        return view("product.components.catalog.title", compact('title_h1'));
    }

    public static function addReview($data){
        $product=Product::find($data['product_id']);
        CommentProduct::create([
            'name'=>$data['name'],
            'surname'=>$data['surname'],
            'product_id'=>$product->id,
            'category_id'=>$product->category_id,
            'organization_id'=>$product->organization_id,
            'content'=>$data['message'],
        ]);
        return redirect()->back()->with("message_words_memory", 'Отзыв отправлен на проверку');

    }
  
    
}