<?php

namespace App\Services\Account\Agency;

use App\Models\CategoryProductProvider;
use App\Models\LikeOrganization;
use App\Models\Organization;
use App\Models\RequestsCostProductsSupplier;

class AgencyOrganizationProviderService {

    public static function requestsCostProductSuppliers(){
        $categories_products_provider=CategoryProductProvider::where('parent_id','!=',null)->get();
        return view('account.agency.organization.provider.add-requests',compact('categories_products_provider'));
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

}

