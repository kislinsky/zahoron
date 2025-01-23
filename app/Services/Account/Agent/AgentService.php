<?php

namespace App\Services\Account\Agent;


use App\Models\User;
use App\Models\Burial;
use App\Models\Service;
use App\Models\Cemetery;
use App\Models\ImageAgent;
use App\Models\OrderBurial;
use App\Models\OrderService;
use App\Models\SearchBurial;
use Illuminate\Http\Request;
use App\Models\FavouriteBurial;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AgentService {

     
    public static function index(){
        $user=user();
        $orders_services=OrderService::orderBy('id','desc')->where('status',0)->where('worker_id',null)->whereIn('cemetery_id',json_decode($user->cemetery_ids))->get();
        return view('account.agent.index',compact('user','orders_services'));
    }



    public static function services($data){
        $user=user();
        $status=null;
        if(isset($data['status']) && $data['status']!=null ){
            if($data['status']==2){
                $orders_services=OrderService::orderBy('id','desc')->where('paid',1)->where('worker_id',$user->id)->paginate(6);
            }
            
            else{
                $orders_services=OrderService::orderBy('id','desc')->where('status',$data['status'])->where('worker_id',$user->id)->paginate(6);
            }
            $status=$data['status'];
        }else{
            $orders_services=OrderService::orderBy('id','desc')->where('worker_id',$user->id)->paginate(6);
        }
        return view('account.agent.services.index',compact('orders_services','status'));

    }

    public static function acceptService($id){
        $user=Auth::user();
        $order=OrderService::findOrFail($id);
        $order->update([
            'worker_id'=>$user->id,
            'status'=>1,
        ]);
        return redirect()->back();
    }

    public static function getToWorkService($id){
        $user=Auth::user();
        $order=OrderService::findOrFail($id);
        $order->update([
            'status'=>3,
        ]);
        return redirect()->back();
    }

    

    public static function agentSettings(){
        $page=5;
        $user=Auth::user();
        $cemeteries=[];
        if($user->cemetery_ids!=null){
            $cemeteries=Cemetery::whereIn('id',json_decode($user->cemetery_ids))->get();
        }
        $imgs_agent=ImageAgent::where('user_id',$user->id)->get();
        return view('account.agent.settings',compact('user','page','imgs_agent','cemeteries'));

    }


    public static function agentSettingsUpdate($data){
        $user=Auth::user();
        $user_email=User::where('email',$data['email'])->where('id','!=',$user->id)->get();
        $user_phone=User::where('phone',$data['phone'])->where('id','!=',$user->id)->get();
        if(count($user_email)<1 && count($user_phone)<1){
            $user->update([
                'name'=>$data['name'],
                'surname'=>$data['surname'],
                'patronymic'=>$data['patronymic'],
                'phone'=>$data['phone'],
                'city'=>$data['city'],
                'adres'=>$data['adres'],
                'email'=>$data['email'],
                'whatsapp'=>$data['whatsapp'],
                'telegram'=>$data['telegram'],
                'language'=>$data['language'],
                'theme'=>$data['theme'],
                'number_cart'=>$data['number_cart'],
                'bank'=>$data['bank'],
            ]);

            if(isset($data['cemetery_ids'])){
                $user->update(['cemetery_ids'=>json_encode($data['cemetery_ids']) ]);
            }else{
                $user->update(['cemetery_ids'=>null ]);
            }

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


    public static function addUploadSeal($data){
        $user=Auth::user();
        foreach($data['file_print'] as $file){
            $filename=generateRandomString().".jpeg";
            $file->storeAs("uploads_agent", $filename, "public");
            ImageAgent::create([
                'title'=>$filename,
                'user_id'=>$user->id,
            ]);
        }
        return redirect()->back();
    }

    public static function deleteUploadSeal($id){
        ImageAgent::findOrFail($id)->delete();
        return redirect()->back();
    }


    public static function rentService($data){
        $order=OrderService::findOrFail($data['order_id']);
        $user=Auth::user();
        $names=[];
        foreach($data['file_services'] as $file){
            $filename=generateRandomString().".jpeg";
            $file->storeAs("uploads_order", $filename, "public");
            $names[]=$filename;
        }
        $result=implode('|',$names);
        $order->update([
            'status'=>4,
            'imgs'=> $result,
        ]);
        return redirect()->back();
    }


    public static function addCemetery($data){
        if(isset($data['id_location'])){
            $cemetery=Cemetery::findOrFail($data['id_location']);
            return response()->json(['adres'=>$cemetery->adres,'id_cemetery'=>$cemetery->id]);
        }else{
            $cemetery=Cemetery::where('title',$data['name_location'])->get();
            if(count($cemetery)>0){
                return response()->json(['adres'=>$cemetery[0]->adres,'id_cemetery'=>$cemetery[0]->id]);
            }else{
                return response()->json(['error'=>'Такого кладбища нет']);
            }
        }

    }

}