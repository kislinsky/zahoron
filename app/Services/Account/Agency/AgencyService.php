<?php

namespace App\Services\Account\Agency;


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
use App\Models\City;
use App\Models\Edge;
use App\Models\FavouriteBurial;
use App\Models\ImageAgency;
use App\Models\ImageOrganization;
use App\Models\Organization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AgencyService {

    public static function index(){
        $user=Auth::user();
        $last_orders_services=[];
        if($user->cemetery_ids!=null){
            $last_orders_services=OrderService::orderBy('id', 'desc')->where('worker_id',null)->where('status',0)->whereIn('cemetery_id',json_decode($user->cemetery_ids))->get();
        }
        return view('account.agency.index',compact('user','last_orders_services'));
    }

    public static function settings(){
        $user=user();
        if($user->organizational_form=='ep'){
            $edges=Edge::orderBy('title','asc')->get();
            $cities=City::orderBy('title','asc')->where('edge_id',$user->edge_id)->get();
            return view('account.agency.settings.settings-ep',compact('user','edges','cities'));
        }
        elseif($user->organizational_form=='organization'){
            $edges=Edge::orderBy('title','asc')->get();
            $cities=City::orderBy('title','asc')->where('edge_id',$user->edge_id)->get();
            return view('account.agency.settings.settings-organization',compact('user','edges','cities'));
        }
        return redirect()->back();

    }


    public static function organizationSettingsUpdate($data){
        $organization_ddata=null;
        if(env('API_WORK')=='true'){
            $organization_ddata=checkOrganizationInn($data['inn']);
        }
         if($organization_ddata!=null && $organization_ddata['state']['status']=='ACTIVE'){
            $name_user_org=$organization_ddata['fio']['name'];
            $surname_user_org=$organization_ddata['fio']['surname'];
            $patronymic_user_org=$organization_ddata['fio']['patronymic'];
            $ogrn_user_org=$organization_ddata['ogrn'];
            $user=Auth::user();
            $user_email=User::where('email',$data['email'])->where('id','!=',$user->id)->get();
            $user_phone=User::where('phone',$data['phone'])->where('id','!=',$user->id)->get();
            if(count($user_email)<1 && count($user_phone)<1){
                $user->update([
                    'name'=>$name_user_org,
                    'surname'=>$surname_user_org,
                    'patronymic'=>$patronymic_user_org,
                    'phone'=>$data['phone'],
                    'adres'=>$data['adres'],
                    'email'=>$data['email'],
                    'whatsapp'=>$data['whatsapp'],
                    'telegram'=>$data['telegram'],
                    'language'=>$data['language'],
                    'theme'=>$data['theme'],
                    'inn'=>$data['inn'],
                    'ogrn'=>$ogrn_user_org,
                ]);

                if($data['password']!=null && $data['password_new']!=null){
                    if(Hash::check($data['password'], $user->password)==true){
                        if($data['password_new']==$data['password_new_2'] && strlen($data['password_new'])>7){
                            $user->update([
                                'password'=>Hash::make($data['password_new'])
                            ]);           
                            return redirect()->back();
                        }
                        return redirect()->back()->with("error", 'Новые пароли не совпадают');
                    }
                    return redirect()->back()->with("error", 'Неверный пароль');
                }
                if(!isset($data['email_notifications'])){
                    $user->update([
                        'email_notifications'=>0
                    ]);
                }else{
                    $user->update([
                        'email_notifications'=>1
                    ]);
                }
                if(isset($data['ogrn'])){
                    $user->update([
                        'ogrn'=>$data['ogrn'],
                    ]);
                }
                
                if(isset($data['number_cart'])){
                    $user->update([
                        'number_cart'=>$data['number_cart'],
                    ]);
                }
                if(isset($data['bank'])){
                    $user->update([
                        'bank'=>$data['bank'],
                    ]);
                }

                if(isset($data['name_organization'])){
                    $user->update([
                        'name_organization'=>$data['name_organization'],
                    ]);
                }

                if(isset($data['city_id'])){
                    $user->update([
                        'city_id'=>$data['city_id'],
                    ]);
                }
                if(isset($data['city'])){
                    $user->update([
                        'city'=>$data['city'],
                    ]);
                }
                if(isset($data['edge_id'])){
                    $user->update([
                        'edge_id'=>$data['edge_id'],
                    ]);
                }
                if(isset($data['in_face'])){
                    $user->update([
                        'in_face'=>$data['in_face'],
                    ]);
                }
                if(isset($data['regulation'])){
                    $user->update([
                        'regulation'=>$data['regulation'],
                    ]);
                }

                if(!isset($data['sms_notifications'])){
                    $user->update([
                        'sms_notifications'=>0
                    ]);
                }else{
                    $user->update([
                        'sms_notifications'=>1
                    ]);
                }

                return redirect()->back();
            }
            return redirect()->back()->with("error", 'Такой телефон или email уже существует');
        }

        return redirect()->back()->with('error','Не существующий или не действуйющий инн');
        
    }







    public static function chooseOrganization($id){
        $organization=Organization::findOrFail($id);
        $user=Auth::user();
        if($organization->user_id==$user->id){
            $user->update([
                'organization_id'=>$id,
            ]);
        }
        return redirect()->back()->with('message_cart','Выбрана новая организация');
        
    }



    // public static function acceptService($id){
    //     $user=Auth::user();
    //     $order=OrderService::findOrFail($id);
    //     $order->update([
    //         'worker_id'=>$user->id,
    //         'status'=>2,
    //     ]);
    //     return redirect()->back();
    // }


    // public static function serviceIndex(){
    //     $page=3;
    //     $user=Auth::user();
    //     $orders=OrderService::orderBy('id', 'desc')->where('worker_id',$user->id)->whereIn('cemetery_id',json_decode($user->cemetery_ids))->get();
    //     $orders_2=OrderService::orderBy('id', 'desc')->where('worker_id',null)->where('status',0)->whereIn('cemetery_id',json_decode($user->cemetery_ids))->get();
    //     return view('account.organization.services.index',compact('orders','page','orders_2'));
    // }


    // public static function serviceFilter($status){
    //     $page=3;
    //     $user=Auth::user();
    //     if($status==1){
    //         $orders=OrderService::orderBy('id', 'desc')->where('worker_id',$user->id)->whereIn('status',[1,2,3])->whereIn('cemetery_id',json_decode($user->cemetery_ids))->get();
    //         return view('account.organization.services.index',compact('orders','status'));
    //     }
    //     elseif($status==4){
    //         $orders=OrderService::orderBy('id', 'desc')->where('worker_id',null)->whereIn('cemetery_id',json_decode($user->cemetery_ids))->get();
    //         return view('account.organization.services.index',compact('orders','status'));
    //     }
    //     $orders=OrderService::orderBy('id', 'desc')->where('worker_id',$user->id)->where('status',$status)->whereIn('cemetery_id',json_decode($user->cemetery_ids))->get();
    //     return view('account.organization.services.index',compact('orders','status','page'));
    // }

    // public static function addUploadSeal($data){
    //     $user=Auth::user();
    //     foreach($data['file_print'] as $file){
    //         $filename=generateRandomString().".jpeg";
    //         $file->storeAs("uploads_organization", $filename, "public");
    //         ImageAgency::create([
    //             'title'=>$filename,
    //             'user_id'=>$user->id,
    //         ]);
    //     }
    //     return redirect()->back();
    // }

    // public static function deleteUploadSeal($id){
    //     ImageAgency::findOrFail($id)->delete();
    //     return redirect()->back();
    // }


    // public static function rentService($data){
    //     $order=OrderService::findOrFail($data['order_id']);
    //     $user=Auth::user();
    //     $names=[];
    //     foreach($data['file_services'] as $file){
    //         $filename=generateRandomString().".jpeg";
    //         $file->storeAs("uploads_order", $filename, "public");
    //         $names[]=$filename;
    //     }
    //     $result=implode('|',$names);
    //     $order->update([
    //         'status'=>5,
    //         'imgs'=> $result,
    //     ]);
    //     return redirect()->back();
    // }


    // public static function addCemetery($data){
    //     if(isset($data['id_location'])){
    //         $cemetery=Cemetery::findOrFail($data['id_location']);
    //         return response()->json(['adres'=>$cemetery->adres,'id_cemetery'=>$cemetery->id]);
    //     }else{
    //         $cemetery=Cemetery::where('title',$data['name_location'])->get();
    //         if(count($cemetery)>0){
    //             return response()->json(['adres'=>$cemetery[0]->adres,'id_cemetery'=>$cemetery[0]->id]);
    //         }else{
    //             return response()->json(['error'=>'Такого кладбища нет']);
    //         }
    //     }
    // }


    // public static function beautificationsIndex(){
    //     $user=Auth::user();
    //     $page=4;
    //     $beautifications_burial=Beautification::orderBy('id','desc')->where('status',null)->whereIn('cemetery_id',json_decode($user->cemetery_ids))->get();
    //     return view('account.organization.beautification.index',compact('beautifications_burial','page'));
    // }


    // public static function acceptBeatification($id){
    //     $user=Auth::user();
    //     $beautification=Beautification::findOrFail($id);
    //     $beautification->update(['worker_id'=>$user->id]);
    //     return redirect()->back();
    // }
}