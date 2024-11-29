<?php

namespace App\Services\Account\Agency;

use App\Models\CategoryProductProvider;
use App\Models\LikeOrganization;
use App\Models\Organization;
use App\Models\Product;
use App\Models\ProductRequestToSupplier;
use App\Models\PromotionsВiscountProvider;
use App\Models\RequestsCostProductsSupplier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AgencyOrganizationProviderService {

    public static function requestsCostProductSuppliers(){
        $categories_products_provider=CategoryProductProvider::where('parent_id','!=',null)->get();
        return view('account.agency.organization.provider.requests.add',compact('categories_products_provider'));
    }

    public static function addRequestsCostProductSuppliers($data){
        $products=[];

        foreach($data['products'] as $key=>$product){
            $count=$data['count'][$key];
            $products[]=[$product,$count,0];
        }
        $transport_companies=json_encode('all');
        if(!isset($data['all_lcs']) && isset($data['lcs'])){
            $transport_companies=json_encode($data['lcs']);
        }
        RequestsCostProductsSupplier::create([
            'organization_id'=>user()->organization()->id,
            'products'=>json_encode($products),
            'transport_companies'=>json_encode($transport_companies),
            'categories_provider_product'=>json_encode($data['products']),
        ]);
        return redirect()->back()->with('message_cart','Заявка успешно отправлена');

    }

    public static function likeOrganizations(){
        $ids_organizations=LikeOrganization::where('user_id',user()->id)->pluck('organization_id');
        $organizations=Organization::whereIn('id',$ids_organizations)->where('role','organization-provider')->paginate(10);
        return view('account.agency.organization.provider.like-organizations',compact('organizations'));
    }

    public static function stocksOrganizationProviders(){
        $stocks=PromotionsВiscountProvider::where('type','stock')->paginate(10);
        return view('account.agency.organization.provider.stocks',compact('stocks'));
    }

    public static function discountsOrganizationProviders(){
        $discounts=PromotionsВiscountProvider::where('type','discount')->paginate(10);
        return view('account.agency.organization.provider.discounts',compact('discounts'));
    }


    public static function addOfferToProvider(){
        $categories_products_provider=CategoryProductProvider::where('parent_id','!=',null)->get();
        return view('account.agency.organization.provider.offers.create',compact('categories_products_provider'));
    }
    
    public static function createOfferToProvider($data){
        $organization=user()->organization();
        $images=[];
        if(count($data['images'])>0  && count($data['images'])<6){
            foreach($data['images'] as $image){
                $filename=generateRandomString().".jpeg";
                $image->storeAs("uploads_product", $filename, "public");
                $images[]=$filename;
            }
            $images=json_encode($images);

        }else{
            return redirect()->back()->with('error','Превышено допустимое количество файлов');
        }


        $request_to_provider=ProductRequestToSupplier::create([
            'title'=>$data['title'],
            'content'=>$data['content'],
            'organization_id'=>$organization->id,
            'images'=>$images,
        ]);

        if(isset($data['delivery']) ){
            $request_to_provider->update([
                'delivery'=>1,
            ]);
        }

        if(!isset($data['none_category']) ){
            $request_to_provider->update([
                'category_id'=>$data['category'],
            ]);
        }
        return redirect()->back()->with('message_cart','Заявка успешно отправлена');
    }

    public static function createdOfferToProvider($data){
        $categories_products_provider=CategoryProductProvider::where('parent_id','!=',null)->get();
        $requests_to_provider=ProductRequestToSupplier::orderBy('id','desc')->where('status',0);
        $category_choose=0;
        if(isset($data['category']) && $data['category']!=0){
            $requests_to_provider=$requests_to_provider->where('category_id',$data['category']);
            $category_choose=$data['category'];
        }
        $city=selectCity();
        $requests=$requests_to_provider->paginate(10);
        return view('account.agency.organization.provider.offers.created',compact('city','requests','category_choose','categories_products_provider'));
    }

    public static function deleteOffer($offer){   
        $offer->delete();     
        return redirect()->back()->with('message_cart','Заявка успешно удалена');
    }


    public static function deletRequest($request){   
        $request->delete();     
        return redirect()->back()->with('message_cart','Заявка успешно удалена');
    }
    

    public static function answerOfferToProvider($data){
        $categories_products_provider=CategoryProductProvider::where('parent_id','!=',null)->get();
        $requests_to_provider=ProductRequestToSupplier::orderBy('id','desc')->where('status',1);
        $category_choose=0;
        if(isset($data['category']) && $data['category']!=0){
            $requests_to_provider=$requests_to_provider->where('category_id',$data['category']);
            $category_choose=$data['category'];
        }
        $city=selectCity();
        $requests=$requests_to_provider->paginate(10);
        
        return view('account.agency.organization.provider.offers.answer',compact('city','requests','categories_products_provider','category_choose'));
    }


    public static function answerRequestsCostProductSuppliers(){
        $requests=RequestsCostProductsSupplier::orderBy('id','desc')->where('status',1)->paginate(10);
        return view('account.agency.organization.provider.requests.answer',compact('requests'));

    }


    public static function filterCategoryAnswerOfferToProvider($data){
        $requests_to_provider=ProductRequestToSupplier::orderBy('id','desc')->where('status',1);
        if(isset($data['category']) && $data['category']!=0){
            $requests_to_provider=$requests_to_provider->where('category_id',$data['category']);
        }
        $requests=$requests_to_provider->paginate(10);

        return view('account.agency.components.provider.requests-answer-show',compact('requests'));
    }

    public static function filterCategoryCreatedOfferToProvider($data){
        $requests_to_provider=ProductRequestToSupplier::orderBy('id','desc')->where('status',0);
        if(isset($data['category']) && $data['category']!=0){
            $requests_to_provider=$requests_to_provider->where('category_id',$data['category']);
        }
        $requests=$requests_to_provider->paginate(10);

        return view('account.agency.components.provider.requests-created-show',compact('requests'));
    }


    
    
}

