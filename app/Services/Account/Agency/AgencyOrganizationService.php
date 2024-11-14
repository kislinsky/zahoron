<?php

namespace App\Services\Account\Agency;

use App\Models\ActivityCategoryOrganization;
use App\Models\User;
use App\Models\Burial;
use App\Models\Service;
use App\Models\Cemetery;
use App\Models\ImageAgent;
use App\Models\OrderBurial;
use App\Models\OrderService;
use App\Models\SearchBurial;
use Illuminate\Http\Request;
use App\Models\Beautification;
use App\Models\CategoryProduct;
use App\Models\City;
use App\Models\FavouriteBurial;
use App\Models\ImageAgency;
use App\Models\ImageOrganization;
use App\Models\Organization;
use App\Models\WorkingHoursOrganization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AgencyOrganizationService {

    public static function settings($id){
        $organization=Organization::find($id);
        $cities=City::orderBy('title','asc')->get();
        $cemeteries=[];
        if($organization->cemetery_ids!=null){
            $cemeteries=Cemetery::whereIn('id',array_filter(explode(',',$organization->cemetery_ids)))->get();
        }
        $categories=CategoryProduct::where('parent_id',null)->get();
        $categories_children=childrenCategoryProducts($categories[0]);
        $categories_organization=ActivityCategoryOrganization::where('organization_id',$organization->id)->get();
        $days=WorkingHoursOrganization::where('organization_id',$organization->id)->orderByRaw("FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')")->get();

        if($organization->user_id==user()->id){
            return view('account.agency.organization.settings',compact('days','cemeteries','cities','organization','categories_children','categories','categories_organization'));
        }
        return redirect()->back()->with('error','Эта организация не принадлежит вам');
    }


    public static function update($data){
        $organization=Organization::findOrFail($data['id']);
        $cemeteries=implode(",", $data['cemetery_ids']).',';
        $organization->update([
            'title'=>$data['title'],
            'mini_content'=>$data['mini_content'],
            'content'=>$data['content'],
            'cemetery_ids'=>$cemeteries,
            'phone'=>$data['phone'],
            'telegram'=>$data['telegram'],
            'whatsapp'=>$data['whatsapp'],
            'email'=>$data['email'],
            'city_id'=>$data['city'],
            'next_to'=>$data['next_to'],
            'underground'=>$data['underground'],
            'adres'=>$data['adres'],
            'width'=>$data['width'],
            'longitude'=>$data['longitude'],
        
        ]);
        ActivityCategoryOrganization::where('organization_id',$organization->id)->delete();
        foreach($data['categories_organization'] as $key=>$category_organization){
            $cat=CategoryProduct::find($category_organization);
            $active_cetagory_organizaiton=ActivityCategoryOrganization::create([
                'organization_id'=>$organization->id,
                'category_main_id'=>$cat->parent_id,
                'category_children_id'=>$cat->id,
                'city_id'=>$organization->city_id,
                'rating'=>$organization->rating,
                'cemetery_ids'=>$cemeteries,
                'price'=>$data['price_cats_organization'][$key],
            ]);
        }

        WorkingHoursOrganization::where('organization_id',$organization->id)->delete();

        $days=[
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday',
            'Sunday'
        ];
        $holidays=[];
        if(isset($data['holiday_day'])){
            $holidays=$data['holiday_day'];
        }
        
        $working_days=$data['working_day'];
        foreach($days as $key=>$day){
            
            if(in_array($day,$holidays)){
                $workings_hours=WorkingHoursOrganization::create([
                    'day'=>$day,
                    'holiday'=>1,
                    'organization_id'=>$organization->id,
                ]);
            }else{
                $time_start_work=explode(' - ',$working_days[$key])[0];
                $time_end_work=explode(' - ',$working_days[$key])[1];
                $workings_hours=WorkingHoursOrganization::create([
                    'day'=>$day,
                    'time_start_work'=>$time_start_work,
                    'time_end_work'=>$time_end_work,
                    'organization_id'=>$organization->id,
                ]);
            }
        }

        if(!isset($data['available_installments'])){
            $organization->update([
                'available_installments'=>0
            ]);
        }else{
            $organization->update([
                'available_installments'=>1
            ]);
        }

        if(!isset($data['found_cheaper'])){
            $organization->update([
                'found_cheaper'=>0
            ]);
        }else{
            $organization->update([
                'found_cheaper'=>1
            ]);
        }
        
        if(!isset($data['сonclusion_contract'])){
            $organization->update([
                'сonclusion_contract'=>0
            ]);
        }else{
            $organization->update([
                'сonclusion_contract'=>1
            ]);
        }

        if(!isset($data['state_compensation'])){
            $organization->update([
                'state_compensation'=>0
            ]);
        }else{
            $organization->update([
                'state_compensation'=>1
            ]);
        }

        return redirect()->back()->with('message_cart','Организация успешно обновлена');
    }





    public static function searchOrganizations($data){
        $city=selectCity();
        $s=null;
        if(isset($data['city_id'])){
            $city=City::find($data['city_id']);
        }
        if(isset($data['s']) && $data['s']!=null){
            $s=$data['s'];
            $organizations=Organization::where('title','like','%'.$data['s'].'%')->where('city_id',$city->id)->where('role','organization')->paginate(10);
        }
        else{
            $organizations=Organization::where('city_id',$city->id)->where('role','organization')->paginate(10);
        }
        $cities=City::all();
        return view('account.agency.organization.add-organization',compact('organizations','city','cities','s'));
    }
    


    public static function aplications(){
        $organization=user()->organizationInAccount();
        $user=user();
        return view('account.agency.organization.pay.buy-applications',compact('user','organization'));
    }


    public static function buyAplicationsFuneralServices($count){  
        $organization=user()->organizationInAccount();
        $organization->update([
            'applications_funeral_services'=>$organization->applications_funeral_services+$count,
        ]);
        return redirect()->back()->with('message_cart','Зявки успешно куплены');
    }

    public static function buyAplicationsCallsOrganization($count){  
        $organization=user()->organizationInAccount();
        $organization->update([
            'calls_organization'=>$organization->calls_organization+$count,
        ]);
        return redirect()->back()->with('message_cart','Зявки успешно куплены');
    }

    public static function buyAplicationsProductRequestsFromMarketplace($count){ 
        $organization=user()->organizationInAccount();
        $organization->update([
            'product_requests_from_marketplace'=>$organization->product_requests_from_marketplace+$count,
        ]);
        return redirect()->back()->with('message_cart','Зявки успешно куплены');
    }

    public static function buyAplicationsImprovemenGraves($count){  
        $organization=user()->organizationInAccount();
        $organization->update([
            'applications_improvemen_graves'=>$organization->applications_improvemen_graves+$count,
        ]);
        return redirect()->back()->with('message_cart','Зявки успешно куплены');
    }
}