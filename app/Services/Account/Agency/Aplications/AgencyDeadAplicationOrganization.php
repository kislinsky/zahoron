<?php

namespace App\Services\Account\Agency\Aplications;

use App\Models\DeadApplication;

class AgencyDeadAplicationOrganization {

    public static function new(){
        $organization=user()->organization();
        $aplications=collect();
        if($organization!=null){
        $aplications=DeadApplication::orderBy('id','desc')->where('status',1)->where('city_id',$organization->city->id)->where('organization_id',$organization->id)->paginate(6);
        }
        return view('account.agency.organization.aplications.find-out-information-about-deceased.new',compact('aplications'));
    }

    public static function inWork(){
        $organization=user()->organization();
        $aplications=collect();
        if($organization!=null){
        $aplications=DeadApplication::orderBy('id','desc')->where('status',2)->where('organization_id',$organization->id)->paginate(6);
        }
        return view('account.agency.organization.aplications.find-out-information-about-deceased.in-work',compact('aplications'));
    }

    public static function completed(){
        $organization=user()->organization();
        $aplications=collect();
        if($organization!=null){
        $aplications=DeadApplication::orderBy('id','desc')->where('status',3)->where('organization_id',$organization->id)->paginate(6);
        }
        return view('account.agency.organization.aplications.find-out-information-about-deceased.completed',compact('aplications'));
    }

    public static function notCompleted(){
        $organization=user()->organization();
        $aplications=collect();
        if($organization!=null){
        $aplications=DeadApplication::orderBy('id','desc')->where('status',4)->where('organization_id',$organization->id)->paginate(6);
        }
        return view('account.agency.organization.aplications.find-out-information-about-deceased.not-completed',compact('aplications'));
    }

    public static function complete($aplication){
        $aplication->update(['status'=>3]);
        return redirect()->back()->with('message_cart','Статус успешно обновлен');

    }

    public static function accept($aplication){
        if ($aplication->status==0){
            $organization=user()->organization();
            $aplication->update([
                'status'=>2,
                'organization_id'=>$organization->id
            ]);
            return redirect()->back()->with('message_cart','Заявка успешно принята');

        }
        return redirect()->back()->with('error','Ошибка');

        
    }
    
}