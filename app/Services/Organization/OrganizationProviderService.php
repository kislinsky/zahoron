<?php

namespace App\Services\Organization;

use App\Models\ActivityCategoryOrganization;
use App\Models\CategoryProductProvider;
use App\Models\City;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\SEOTools;

class OrganizationProviderService 
{

    public static function catalogOrganizationProvider($data){
        $city=selectCity();

        if(isset($data['city_id']) && $data['city_id']!=null){
            $city=City::find($data['city_id']);
        }

        $city_all=cityWithOrganizationProvider();
        $cats=CategoryProductProvider::orderBy('id','desc')->where('parent_id',null)->get();
        $organizations_category=orgniaztionsProviderFilters($data);
        
    
        $category=categoryProductProviderChoose();
        $category_main=CategoryProductProvider::find($category->parent_id);
        
        if(isset($data['category_id'])){
            $category=CategoryProductProvider::find($data['category_id']);
            $category_main=CategoryProductProvider::find($category->parent_id);
        }
     
       $organizations_rating = ActivityCategoryOrganization::with('organization')
    ->whereHas('organization', function($query) use ($city) {  // Добавлен use ($city)
        $query->where('role', 'organization-provider')
              ->where('city_id', $city->id);
    })
    ->where('category_children_id', $category->id)
    ->orderBy('price', 'asc')
    ->get();

        $organizations_prices=organizationsProviderPrices($data);
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

        SEOTools::setTitle(formatContentCategory(getSeo($category->slug.'-catalog-organization','title'),$category,$organizations_category));
        SEOTools::setDescription(formatContentCategory(getSeo($category->slug.'-catalog-organization','description'),$category,$organizations_category));
        $title_h1=formatContentCategory(getSeo($category->slug.'-catalog-organization','h1'),$category,$organizations_category);


        if($organizations_category->total()<3){
            SEOMeta::setRobots('noindex, nofollow');
        }
        

        $city=selectCity();
       

        if(isset($data['city_id']) && $data['city_id']!=null){
            $city=City::find($data['city_id']);
        }
        $category=categoryProductProviderChoose();
        if(isset($data['category_id']) && $data['category_id']!=null){
            $category=CategoryProductProvider::find($data['category_id']);
        }
        
        $organizations_rating = ActivityCategoryOrganization::with('organization')
            ->whereHas('organization', function($query) use ($city) {  // Добавлен use ($city)
                $query->where('role', 'organization-provider')
                      ->where('city_id', $city->id);
            })
            ->where('category_children_id', $category->id)
            ->orderBy('price', 'asc')
            ->get();        
        return view('organization.catalog.catalog-organization-provider',compact('title_h1','filter_work','organizations_rating','city_all','category_main','city','cats','organizations_category','price_min','price_middle','price_max','category','sort'));
   
    }



    public static function ajaxFilterCatalog($data){
        $organizations_category=orgniaztionsProviderFilters($data);
        return  view('organization.components.catalog-provider.organizations-show',compact('organizations_category'));
    }

    
    public static function ajaxCategoryPrices($data){
        $city=selectCity();
        if(isset($data['city_id']) && $data['city_id']!=null){
            $city=City::find($data['city_id']);
        }
        $category=CategoryProductProvider::find($data['category_id']);
        $organizations_prices=organizationsProviderPrices($data);
        $price_min=null;
        $price_middle=null;
        $price_max=null;
        if($organizations_prices!=null){
            $price_min=$organizations_prices[0];
            $price_middle=$organizations_prices[1];
            $price_max=$organizations_prices[2];     
        }
        return  view('organization.components.catalog-provider.prices',compact('price_min','price_middle','price_max','city','category'));
    }


    public static function ajaxTitlePage($data){
        $category=CategoryProductProvider::find($data['category_id']);
        $category_main=$category->parent();
        $city=selectCity();
        if(isset($data['city_id']) && $data['city_id']!=null){
            $city=City::find($data['city_id']);
        }
        
        $organizations_category=orgniaztionsProviderFilters($data);
        $title_h1=formatContentCategory(getSeo($category->slug.'-catalog-organization','h1'),$category,$organizations_category);

        return  view('organization.components.catalog-provider.title-page',compact('city','category','category_main','title_h1'));
    }


    public static function ajaxSearchCatalogProvider($data){
        $name=null;
        if(isset($data['name_organization']) && $data['name_organization']!=null){
            $name=$data['name_organization'];
        }
        $organizations=searchOrganization($name);
        return  view('organization.components.catalog-provider.organizations-search-show',compact('organizations'));
    }

    public static function ajaxRatingOrganizationProvider($data){
        $city=selectCity();
       

        if(isset($data['city_id']) && $data['city_id']!=null){
            $city=City::find($data['city_id']);
        }
        $category=categoryProductProviderChoose();
        if(isset($data['category_id']) && $data['category_id']!=null){
            $category=CategoryProductProvider::find($data['category_id']);
        }
        $organizations_rating = ActivityCategoryOrganization::with('organization')
            ->whereHas('organization', function($query) use ($city) {  // Добавлен use ($city)
                $query->where('role', 'organization-provider')
                      ->where('city_id', $city->id);
            })
            ->where('category_children_id', $category->id)
            ->orderBy('price', 'asc')
            ->get();
        return view('organization.components.catalog-provider.rating-price-organizations',compact('organizations_rating','category','city'));


    }

}