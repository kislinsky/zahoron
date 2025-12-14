<?php

namespace App\Services\Account\Agency\Aplications;

use App\Models\Beautification;
use App\Models\TypeService;

class AgencyBeautificationAplicationOrganization {

    public static function new(){
        $organization=user()->organization();
        deleteNotifications('beautification_new',null,$organization->id);
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
        // $service=getTypeService('beatification');
        // if($service!=null){
        //     if($service->count()>0 && $aplication->status==0){
        //         $aplication->update([
        //             'status'=>1,
        //             'organization_id'=>$organization->id
        //         ]);
        //         $service->updateCount($service->count()-1);
        //         return redirect()->back()->with('message_cart','Заявка успешно принята');
        //     }
        // }
        // return redirect()->back()->with('error','Закончились заявки');


        $type_service=TypeService::where('title','beatification')->first();

        if($type_service!=null  && $aplication->status==0){

            $description="Покупка заявок {$type_service->title_ru}";
            $balance=user()->currentWallet()->withdraw($type_service->price,[],$description);
            if($balance!=false){
                $aplication->update([
                    'status'=>1,
                    'organization_id'=>$organization->id
                ]);
                return redirect()->back()->with('message_cart','Заявка успешно принята');
            }
            return redirect()->back()->with('error','Пополните счет, недостаточно средств');
        }
        return redirect()->back()->with('error','Ошибка, обратитесь в тех поддержку.');

    }

}

