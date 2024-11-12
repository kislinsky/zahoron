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
use App\Models\PriceListOrganization;
use App\Models\Product;
use App\Models\ReviewsOrganization;
use App\Models\StockProduct;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrganizationService 
{

    public static function single($slug){
        $organization=Organization::where('slug',$slug)->where('status',1)->first();
        $user=user();
        if($organization==null){
            return redirect()->back();
        }
        $id=$organization->id;

        $categories_organization=CategoryProduct::whereIn('id',ActivityCategoryOrganization::where('organization_id',$id)->pluck('category_main_id'))->get();

        $city=selectCity();
        $organization_all=Organization::where('width','!=',null)->where('longitude','!=',null)->get();
        $user_organization=User::find($organization->user_id);
        $images=ImageOrganization::orderBy('id','desc')->where('organization_id', $organization->id)->get();
        $ritual_products=Product::orderBy('id','desc')->where('organization_id',$organization->id)->where('type','ritual-product')->get();
        $reviews=ReviewsOrganization::orderBy('id','desc')->where('organization_id',$organization->id)->where('status',1)->get();
        $main_categories=CategoryProduct::where('parent_id',null)->get();
        $children_categories=CategoryProduct::where('parent_id',$main_categories->first()->id)->get();

        $ritual_products=Product::orderBy('id','desc')->where('organization_id',$id)->where('category_id',$children_categories->first()->id)->get();
      
        $rating_reviews=0;

        if($reviews!=null && count($reviews)>0){
            $rating_reviews=round($reviews->pluck('rating')->sum()/count($reviews));
        }
        $similar_organizations=Organization::whereIn('id',ActivityCategoryOrganization::whereIn('category_children_id',ActivityCategoryOrganization::where('organization_id',$id)->pluck('category_children_id'))->where('organization_id','!=',$id)->pluck('organization_id'))->get();
        $reviews_main=$reviews->take(3);
        $products_our=Product::orderBy('id','desc')->where('organization_id',$organization->id)->where('type','product')->get()->take(8);

        if($organization->role=='organization'){
            return view('organization.single.single-agency',compact('categories_organization','city','similar_organizations','main_categories','children_categories','organization_all','rating_reviews','organization','images','reviews','products_our','reviews','reviews_main','ritual_products'));
        }
        
        if($organization->role=='organization-provider' && Auth::check() && user()->role=='organization'){
            $categories_organization=CategoryProductProvider::whereIn('id',ActivityCategoryOrganization::where('organization_id',$id)->pluck('category_main_id'))->get();
            $remnants_ritual_goods=PriceListOrganization::orderBy('id','desc')->where('organization_id',$id)->where('type','remnant-ritual-good')->get();
            $price_lists=PriceListOrganization::orderBy('id','desc')->where('organization_id',$id)->where('type','price-list')->get();
            $product_stocks=StockProduct::orderBy('id','desc')->where('organization_id',$id)->get();

            return view('organization.single.single-provider',compact('categories_organization','city','similar_organizations','children_categories','main_categories','price_lists','organization_all','remnants_ritual_goods','rating_reviews','organization','product_stocks','images','reviews','reviews','reviews_main','ritual_products'));

        }
        return redirect()->back();
    }


    public static function ajaxProductsChildrenCat($data){
        $category=CategoryProduct::findOrFail($data['category_id']);
        $ritual_products=Product::orderBy('id','desc')->where('organization_id',$data['organization_id'])->where('category_id',$category->id)->get();

        return view('organization.components.ajax.products-services-organization',compact('ritual_products'));
    }

    public static function ajaxProductsMainCat($data){
        $category=CategoryProduct::findOrFail($data['category_id']);
        $categories_children=CategoryProduct::where('parent_id',$data['category_id'])->get();
        $ritual_products=Product::orderBy('id','desc')->where('organization_id',$data['organization_id'])->where('category_id',$categories_children->first()->id)->get();
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


    public static function catalogOrganization($data){
        
        $city=selectCity();
        $news_video=News::orderBy('id', 'desc')->where('type',2)->get();
        $news=News::orderBy('id', 'desc')->take(3)->get();
        $cats=CategoryProduct::orderBy('id','desc')->where('parent_id',null)->get();
        $organizations_category=orgniaztionsFilters($data);

        $cemeteries=Cemetery::where('city_id',$city->id)->get();

        $category=categoryProductChoose();
        $category_main=CategoryProduct::find($category->parent_id);
        if(isset($data['category_id'])){
            $category=CategoryProduct::find($data['category_id']);
            $category_main=CategoryProduct::find($category->parent_id);
        }

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
        return view('organization.catalog.catalog-organization',compact('filter_work','category_main','news_video','district_choose','districts','cemetery_choose','cemeteries','city','cats','news','organizations_category','price_min','price_middle','price_max','category','sort'));
    }
    

    public static function ajaxFilterCatalog($data){
        $organizations_category=orgniaztionsFilters($data);
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
        $category_main=CategoryProduct::find($category->parent_id);
        $cemetery_choose=null;
        $district_choose=null;
        if(isset($data['cemetery_id']) && $data['cemetery_id']!='null' && $category_main->id!=45){
            $cemetery_choose=Cemetery::find($data['cemetery_id']);
        }
        if(isset($data['district_id']) && $data['district_id']!='null' && $category_main->id==45){
            $district_choose=District::find($data['district_id']);
        }
        $city=selectCity();
        return  view('organization.components.catalog.title-page',compact('city','category','category_main','district_choose','cemetery_choose'));
    }

    public static function ajaxMapOrganizations($data){
        $category=CategoryProduct::find($data['category_id']);
        $city=selectCity();
        $organizations_category=orgniaztionsFilters($data);
        return view('organization.components.catalog.map-cats',compact('category','organizations_category','city'));
    }
    
}
