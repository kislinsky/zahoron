<?php

namespace App\Services\Organization;

use App\Models\ActivityCategoryOrganization;
use App\Models\CategoryProduct;
use App\Models\CategoryProductProvider;
use App\Models\Cemetery;
use App\Models\District;
use App\Models\ImageOrganization;
use App\Models\LikeOrganization;
use App\Models\News;
use App\Models\Organization;
use App\Models\Page;
use App\Models\PriceListOrganization;
use App\Models\Product;
use App\Models\ReviewsOrganization;
use App\Models\StockProduct;
use App\Models\User;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class OrganizationService 
{

    public static function sendCode($data){
        $organization=Organization::find($data['organization_id']);
        $code=generateRandomNumber();
        sendCode($organization->phone,$code);
        setcookie("code", Hash::make($code), time() + (20 * 24 * 60 * 60), '/');
        return true;
    }


    public static function acceptCode($data){
        $code=$_COOKIE['code'];
        if(Hash::check($data['code'],$code)){
            $organization=Organization::find($data['organization_id']);
            $organization->update([
                'user_id'=>user()->id,
            ]);
            setcookie("code", '', time()-20, '/');
            return true;
        }
        return response('error');
        
    }

    public static function single($slug){
        
        $organization=Organization::where('slug',$slug)->first();
        $user=user();
        if($organization==null || $organization->status!=1){
            return redirect()->back();
        }

        addView('organization',$organization->id,user()->id ?? null,'site');

        

        SEOTools::setTitle(formatContent(getSeo('organization-single','title'),$organization));
        SEOTools::setDescription(formatContent(getSeo('organization-single','description'),$organization));
        $title_h1=formatContent(getSeo('organization-single','h1'),$organization);
        

        $id=$organization->id;

        $categories_organization=CategoryProduct::whereIn('id',ActivityCategoryOrganization::where('organization_id',$id)->pluck('category_main_id'))->get();

        $city=selectCity();
        $user_organization=User::find($organization->user_id);
        $images=ImageOrganization::orderBy('id','desc')->where('organization_id', $organization->id)->get();
        $ritual_products=Product::orderBy('id','desc')->where('view',1)->where('organization_id',$organization->id)->where('type','ritual-product')->get();
        $reviews=$organization->reviews;
        $main_categories=CategoryProduct::where('parent_id',null)->get();
        $children_categories=CategoryProduct::where('parent_id',$main_categories->first()->id)->get();

        $ritual_products=Product::orderBy('id','desc')->where('view',1)->where('organization_id',$id)->where('category_id',$children_categories->first()->id)->get();
      
        $rating_reviews=0;

        if($reviews!=null && count($reviews)>0){
            $rating_reviews=round($reviews->pluck('rating')->sum()/count($reviews));
        }
        $similar_organizations=Organization::whereIn('id',ActivityCategoryOrganization::whereIn('category_children_id',ActivityCategoryOrganization::where('organization_id',$id)->pluck('category_children_id'))->where('organization_id','!=',$id)->pluck('organization_id'))->get();
        $reviews_main=$reviews->take(3);
        $products_our=Product::orderBy('id','desc')->where('view',1)->where('organization_id',$organization->id)->where('type','product')->get()->take(8);

        if($organization->role=='organization' ){
            return view('organization.single.single-agency',compact('title_h1','categories_organization','city','similar_organizations','main_categories','children_categories','rating_reviews','organization','images','reviews','products_our','reviews','reviews_main','ritual_products'));
        }
        
        if($organization->role=='organization-provider' && (Auth::check() && (user()->role=='organization' || user()->role=='organization-provider' || user()->role=='admin'))){
            $categories_organization=CategoryProductProvider::whereIn('id',ActivityCategoryOrganization::where('organization_id',$id)->pluck('category_main_id'))->get();
            $remnants_ritual_goods=PriceListOrganization::orderBy('id','desc')->where('organization_id',$id)->where('type','remnant-ritual-good')->get();
            $price_lists=PriceListOrganization::orderBy('id','desc')->where('organization_id',$id)->where('type','price-list')->get();
            $product_stocks=StockProduct::orderBy('id','desc')->where('organization_id',$id)->get();

            return view('organization.single.single-provider',compact('title_h1','categories_organization','city','similar_organizations','children_categories','main_categories','price_lists','remnants_ritual_goods','rating_reviews','organization','product_stocks','images','reviews','reviews','reviews_main','ritual_products'));

        }
        return redirect()->back();
    }


    public static function ajaxProductsChildrenCat($data){
        $category=CategoryProduct::findOrFail($data['category_id']);
        $ritual_products=Product::orderBy('id','desc')->where('view',1)->where('organization_id',$data['organization_id'])->where('category_id',$category->id)->get();

        return view('organization.components.ajax.products-services-organization',compact('ritual_products'));
    }

    public static function ajaxProductsMainCat($data){
        $category=CategoryProduct::findOrFail($data['category_id']);
        $categories_children=CategoryProduct::where('parent_id',$data['category_id'])->get();
        $ritual_products=Product::orderBy('id','desc')->where('view',1)->where('organization_id',$data['organization_id'])->where('category_id',$categories_children->first()->id)->get();
        return view('organization.components.ajax.products-services-organization',compact('ritual_products'));
    }


    public static function ajaxProductsMainCatUlChildren($data){
        $children_categories=CategoryProduct::where('parent_id',$data['category_id'])->get();
        return view('organization.components.ajax.children-categories',compact('children_categories'));

    }


    public static function addLikeOrganization($id){
        $organization=Organization::findOrFail($id);
        $like_organization=LikeOrganization::where('organization_id',$id)->where('user_id',Auth::user()->id)->get();

        if($like_organization=null || $like_organization->count()==0){
            LikeOrganization::create([
                'organization_id'=>$id,
                'user_id'=>Auth::user()->id,
            ]);
            return redirect()->back()->with('message_words_memory','Организация успешно добавлена.');
        }
        return redirect()->back()->with('error','Организация уже добавлена.');
    }
    


    public static function addReview($data){

        $rating=null;

        if(isset($data['rating'])){
            $rating=$data['rating'];
        }

        ReviewsOrganization::create([
            'rating'=>$rating,
            'city_id'=>$data['city_id'],
            'content'=>$data['content_review'],
            'name'=>$data['name'],
            'organization_id'=>$data['organization_id']
        ]);

        // $organization=Organization::find($data['organization_id']);
        // изменить когда админ будет одабривать 
        // $organization->update([
        //     'rating'=>raitingOrganization($organization),
        // ]);
        // $cat_organizations=ActivityCategoryOrganization::where('organization_id',$organization->id)->get();
        // foreach($cat_organizations as $cat_organization){
        //     $cat_organization->update([
        //         'rating'=>$organization->rating,
        //     ]);
        // }
        return redirect()->back()->with('message_words_memory','Сообщение отправлено на проверку.');
    }


    public static function catalogOrganization($slug,$data){

        addView('page',Page::where('title','catalog-organization')->first()->id,user()->id ?? null,'site');


        $category=CategoryProduct::where('slug',$slug)->first();
        if($category==null){
            return redirect()->route('organizations.category',categoryProductChoose()->slug);
        }
        $category_main=CategoryProduct::find($category->parent_id);

        $city=selectCity();
        $news_video=News::orderBy('id', 'desc')->where('type',2)->get();
        $news=News::orderBy('id', 'desc')->take(3)->get();
        $cats=CategoryProduct::orderBy('id','desc')->where('parent_id',null)->get();
        $organizations_category=orgniaztionsFilters($data,$category);

       
        $cemeteries = Cemetery::whereHas('city.area', function($query) use ($city) {
            $query->where('id', $city->area_id);  
        })
        ->get();
        // $category=categoryProductChoose();
        // $category_main=CategoryProduct::find($category->parent_id);
        // if(isset($data['category_id'])){
        //     $category=CategoryProduct::find($data['category_id']);
        //     $category_main=CategoryProduct::find($category->parent_id);
        // }

        $cemetery_choose=null;
        $district_choose=null;
        if(isset($data['cemetery_id']) && ($data['cemetery_id']!='null' || $data['cemetery_id']!=null)){
            $cemetery_choose=Cemetery::find($data['cemetery_id']);
        }
        if(isset($data['district_id']) && ($data['district_id']!='null' || $data['district_id']!=null)){
            $district_choose=District::find($data['district_id']);
        }
        $districts=District::where('city_id',$city->id)->get();
        $organizations_prices=organizationsPrices($data);
        $price_min=null;
        $price_middle=null;
        $price_max=null;
        if($organizations_prices!=null){
            $price_min=$organizations_prices[0];
            $price_middle=$organizations_prices[1];
            $price_max=$organizations_prices[2];     
        }
        $sort=null;
        if(isset($data['sort']) && $data['sort']!=null && $data['sort']!=''){
            $sort=$data['sort'];
        }
        $filter_work=null;
        if(isset($data['filter_work']) && $data['filter_work']=='on'){
            $filter_work='on';
        }
        if($organizations_category->total()<3){
            SEOMeta::setRobots('noindex, nofollow');
        }

        SEOTools::setTitle(formatContentCategory(getSeo($category->slug.'-catalog-organization','title'),$category,$organizations_category));
        SEOTools::setDescription(formatContentCategory(getSeo($category->slug.'-catalog-organization','description'),$category,$organizations_category));
        $title_h1=formatContentCategory(getSeo($category->slug.'-catalog-organization','h1'),$category,$organizations_category);

        $pages_navigation=[['Главная',route('index')],['Организации',route('organizations')],[$category->title]];

        return view('organization.catalog.catalog-organization',compact('pages_navigation','title_h1','filter_work','category_main','news_video','district_choose','districts','cemetery_choose','cemeteries','city','cats','news','organizations_category','price_min','price_middle','price_max','category','sort'));
    }
    

    public static function ajaxFilterCatalog($data){
        $category=CategoryProduct::find($data['category_id']);
        $organizations_category=orgniaztionsFilters($data,$category);
        return  view('organization.components.catalog.organizations-show',compact('organizations_category'));
    }

    
    public static function ajaxCategoryPrices($data){
        $city=selectCity();
        $organizations_prices=organizationsPrices($data);
        $price_min=null;
        $price_middle=null;
        $price_max=null;
        $category=CategoryProduct::find($data['category_id']);
        if($organizations_prices!=null){
            $price_min=$organizations_prices[0];
            $price_middle=$organizations_prices[1];
            $price_max=$organizations_prices[2];     
        }
        return  view('organization.components.catalog.prices',compact('price_min','price_middle','price_max','city','category'));
    }


    public static function ajaxTitlePage($data){
        $category=CategoryProduct::find($data['category_id']);

        // $category_main=CategoryProduct::find($category->parent_id);

        // $cemetery_choose=null;
        // $district_choose=null;
        // if(isset($data['cemetery_id']) && $data['cemetery_id']!='null' && $category_main->id!=45){
        //     $cemetery_choose=Cemetery::find($data['cemetery_id']);
        // }
        // if(isset($data['district_id']) && $data['district_id']!='null' && $category_main->id==45){
        //     $district_choose=District::find($data['district_id']);
        // }

        $city=selectCity();
        $organizations_category=orgniaztionsFilters($data,$category);
        $title_h1=formatContentCategory(getSeo($category->slug.'-catalog-organization','h1'),$category,$organizations_category);

        return  view('organization.components.catalog.title-page',compact('title_h1'));
    }

    public static function ajaxMapOrganizations($data){
        $category=CategoryProduct::find($data['category_id']);
        $city=selectCity();
        $organizations_category=orgniaztionsFilters($data,$category);
        return view('organization.components.catalog.map-cats',compact('category','organizations_category','city'));
    }
    
}
