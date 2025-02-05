<?php

namespace App\Services\Account\Agency\Aplications;

use App\Models\Memorial;

class AgencyMemorialAplicationOrganization {

    public static function new(){
        $organization=user()->organization();
        $aplications=collect();
        if($organization!=null){
        $aplications=Memorial::orderBy('id','desc')->where('status',0)->where('city_id',$organization->city->id)->paginate(6);
        }
        return view('account.agency.organization.aplications.memorial.new',compact('aplications'));
    }

    public static function inWork(){
        $organization=user()->organization();
        $aplications=collect();
        if($organization!=null){
        $aplications=Memorial::orderBy('id','desc')->where('status',1)->where('organization_id',$organization->id)->paginate(6);
        }
        return view('account.agency.organization.aplications.memorial.in-work',compact('aplications'));
    }


    public static function completed(){
        $organization=user()->organization();
        $aplications=collect();
        if($organization!=null){
        $aplications=Memorial::orderBy('id','desc')->where('status',2)->where('organization_id',$organization->id)->paginate(6);
        }
        return view('account.agency.organization.aplications.memorial.completed',compact('aplications'));
    }

    public static function notCompleted(){
        $organization=user()->organization();
        $aplications=collect();
        if($organization!=null){
        $aplications=Memorial::orderBy('id','desc')->where('status',4)->where('city_id',$organization->city->id)->paginate(6);
        }
        return view('account.agency.organization.aplications.memorial.not-completed',compact('aplications'));
    }

    public static function accept($aplication){
        $organization=user()->organization();
        if($organization->aplications_memorial>0 && $aplication->status==0){
            $aplication->update([
                'status'=>1,
                'organization_id'=>$organization->id
            ]);
            $organization->update([
                'aplications_memorial'=>$organization->aplications_memorial-1,
            ]);
            return redirect()->back()->with('message_cart','Заявка успешно принята');
        }
        return redirect()->back()->with('error','Закончились заявки');

    }

    public static function complete($aplication){
        $aplication->update(['status'=>2]);
        return redirect()->back()->with('message_cart','Статус успешно обновлен');

    }

}
