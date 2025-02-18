<?php

namespace App\Services\Account\Agency\Aplications;

use App\Models\FuneralService;

class AgencyFuneralServiceAplicationOrganization {

    public static function new($data){
        $organization=user()->organization();
        $aplications=collect();
        if($organization!=null){
        if(isset($data['service'])){
            $aplications=FuneralService::orderBy('id','desc')->where('status',0)->where('city_id',$organization->city->id)->where('service',$data['service'])->paginate(6);

        }else{
            $aplications=FuneralService::orderBy('id','desc')->where('status',0)->where('city_id',$organization->city->id)->paginate(6);
        }
    }
        return view('account.agency.organization.aplications.funeral-services.new',compact('aplications'));
    }




    public static function inWork($data){
        $organization=user()->organization();
        $aplications=collect();
        if($organization!=null){
        if(isset($data['service'])){
            $aplications=FuneralService::orderBy('id','desc')->where('status',1)->where('organization_id',$organization->id)->where('service',$data['service'])->paginate(6);

        }else{
            $aplications=FuneralService::orderBy('id','desc')->where('status',1)->where('organization_id',$organization->id)->paginate(6);
        }
    }
        return view('account.agency.organization.aplications.funeral-services.in-work',compact('aplications'));
    }


    public static function completed($data){
        $organization=user()->organization();
        $aplications=collect();
        if($organization!=null){
        if(isset($data['service'])){
            $aplications=FuneralService::orderBy('id','desc')->where('status',2)->where('organization_id',$organization->id)->where('service',$data['service'])->paginate(6);

        }else{
            $aplications=FuneralService::orderBy('id','desc')->where('status',2)->where('organization_id',$organization->id)->paginate(6);
        }
    }
        return view('account.agency.organization.aplications.funeral-services.completed',compact('aplications'));
    }


    public static function notCompleted($data){
        $organization=user()->organization();
        $aplications=collect();
        if($organization!=null){
        if(isset($data['service'])){
            $aplications=FuneralService::orderBy('id','desc')->where('status',4)->where('city_id',$organization->city->id)->where('service',$data['service'])->paginate(6);

        }else{
            $aplications=FuneralService::orderBy('id','desc')->where('status',4)->where('city_id',$organization->city->id)->paginate(6);
        }
    }
        return view('account.agency.organization.aplications.funeral-services.not-completed',compact('aplications'));
    }

    public static function complete($aplication){
        $aplication->update(['status'=>2,]);
        return redirect()->back()->with('message_cart','Статус успешно обновлен');

    }

    public static function filterService($data){
        $organization=user()->organization();
        if($data['status']==0){
            $aplications=FuneralService::orderBy('id','desc')->where('status',$data['status'])->where('city_id',$organization->city->id)->where('service',$data['service'])->paginate(6);
        }
        elseif($data['status']==4){
            $aplications=FuneralService::orderBy('id','desc')->where('status',$data['status'])->where('city_id',$organization->city->id)->where('service',$data['service'])->paginate(6);
        }
        else{
            $aplications=FuneralService::orderBy('id','desc')->where('status',$data['status'])->where('organization_id',$organization->id)->where('service',$data['service'])->paginate(6);
        }
        return view('account.agency.components.aplication.funeral-service.show-aplications',compact('aplications'));
    }

    public static function accept($aplication){
        $organization=user()->organization();
        if($aplication->service==1){
            $service=getTypeService('sending_cargo_200');
        }elseif($aplication->service==3){
            $service=getTypeService('funeral_arrangements');
        }elseif($aplication->service==2){
            $service=getTypeService('organization_cremation');
        }
        if($service!=null){
            if($service->count()>0 && $aplication->status==0){
                $aplication->update([
                    'status'=>1,
                    'organization_id'=>$organization->id
                ]);
                $service->updateCount($service->count()-1);
                return redirect()->back()->with('message_cart','Заявка успешно принята');
            }
        }
        return redirect()->back()->with('error','Закончились заявки');

    }

}