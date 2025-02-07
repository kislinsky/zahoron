<?php

namespace App\Services\Account\Agency\Aplications;

use App\Models\Beautification;

class AgencyBeautificationAplicationOrganization {

    public static function new(){
        $organization=user()->organization();
       $aplications=collect();
        if($organization!=null){
            $aplications=Beautification::orderBy('id','desc')->where('status',0)->where('city_id',$organization->city->id)->paginate(6);
        }
        return view('account.agency.organization.aplications.beautifications.new',compact('aplications'));
    }

    public static function inWork(){
        $organization=user()->organization();
       $aplications=collect();
        if($organization!=null){
            $aplications=Beautification::orderBy('id','desc')->where('status',1)->where('organization_id',$organization->id)->paginate(6);
        }
        return view('account.agency.organization.aplications.beautifications.in-work',compact('aplications'));
    }


    public static function completed(){
        $organization=user()->organization();
       $aplications=collect();
        if($organization!=null){
            $aplications=Beautification::orderBy('id','desc')->where('status',2)->where('organization_id',$organization->id)->paginate(6);
        }
        return view('account.agency.organization.aplications.beautifications.completed',compact('aplications'));
    }

    public static function notCompleted(){
        $organization=user()->organization();
       $aplications=collect();
        if($organization!=null){
            $aplications=Beautification::orderBy('id','desc')->where('status',4)->where('city_id',$organization->city->id)->paginate(6);
        }
        return view('account.agency.organization.aplications.beautifications.not-completed',compact('aplications'));
    }

    public static function complete($aplication){
        $aplication->update(['status'=>2,]);
        return redirect()->back()->with('message_cart','Статус успешно обновлен');

    }

    public static function accept($aplication){
        $organization=user()->organization();
        if($organization->applications_improvemen_graves>0 && $aplication->status==0){
            $aplication->update([
                'status'=>1,
                'organization_id'=>$organization->id
            ]);
            $organization->update([
                'applications_improvemen_graves'=>$organization->applications_improvemen_graves-1,
            ]);
            return redirect()->back()->with('message_cart','Заявка успешно принята');
        }
        return redirect()->back()->with('error','Закончились заявки');

    }

}

