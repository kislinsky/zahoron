<?php

namespace App\Services\Burial;

use App\Models\Burial;
use App\Models\City;
use App\Models\News;
use App\Models\SearchBurial;
use App\Models\Service;
use App\Models\User;
use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SearchBurialService
{
    public static function searchProductFilter($data){

        $seo="Поиск ".$data['name'] . $data['surname'] . $data['patronymic'] . $data['who'];

        SEOTools::setTitle($seo);
        SEOTools::setDescription($seo);

        $news=News::orderBy('id', 'desc')->take(2)->get();
        $products=Burial::where('name',$data['name'])->where('surname',$data['surname'])->where('patronymic',$data['patronymic'])->where('who',$data['who'])->where('status',1)->get();
        return view('burial.search-burial-result',compact('products','news'));
    }

    public static function searchBurialResult($data){
        $cemetery_ids = selectCity()->area->cities->flatMap(function($city) {
            return $city->cemeteries->pluck('id');
        });
        $news=News::orderBy('id', 'desc')->take(2)->get();
        $products=collect();
        $seo='Поиск могил ';

        if(isset($data['surname'])  ){
            $products=Burial::where('surname',$data['surname'])->whereIn('cemetery_id',$cemetery_ids)->where('status',1);
            $seo=$seo.' '.$data['surname'];
        }
        if(isset($data['name'])  ){
            $products=$products->where('name',$data['name']);
            $seo=$seo.' '.$data['name'];
        }
        
        if(isset($data['patronymic'])  ){
            $products=$products->where('patronymic',$data['patronymic']);
            $seo=$seo.' '.$data['patronymic'];
        }
        
        if(isset($data['date_birth'])  ){
            $products=$products->where('date_birth',$data['date_birth']);
            $seo=$seo.' '.$data['date_birth'];
        }
        
        if(isset($data['date_death'])  ){
            $products=$products->where('date_death',$data['date_death']);
            $seo=$seo.' '.$data['date_death'];
        }



        SEOTools::setTitle($seo);
        SEOTools::setDescription($seo);
        $page=11;
        if($products->count()!==0){
            $products=$products->get();
        }
        return view('burial.search-burial-result',compact('products','news','page'));
    }



    public static function searchBurial(){
        $services = Service::orderBy('id', 'desc')->get();
        $burials=Burial::where('date_death', 'LIKE', date('d.m').'%')->whereIn('cemetery_id',selectCity()->cemeteries->pluck('id'))->get();
        $news=News::orderBy('id', 'desc')->take(2)->get();
        return view('burial.search-burial',compact('news','services','burials'));
    }
    
    public static function searchProductRequestAdd($data){
        if(Auth::check()){
            SearchBurial::create([
                'surname'=>$data['surname'],
                 'name'=>$data['name'],
                 'patronymic'=>$data['patronymic'],
                 'date_birth'=>$data['date_birth'],
                 'date_death'=>$data['date_death'],
                 'location'=>$data['location'],
                 'user_id'=>Auth::user()->id,
             ]);
             return redirect()->back()->with("message_words_memory", "В ближайшее время с Вами свяжутся наши Партнеры по товарам которые вы заказали.");
        }
        else{
            $user_email=User::where('email',$data['email_customer'])->get();
            $user_phone=User::where('phone',$data['phone_customer'])->get();
            if(!isset($user_email[0]) && !isset($user_phone[0])){
                $password=generateRandomString(8);
                // mail($data['email'], 'Ваш пароль', $password);
                $last_id=User::create([
                'name'=>$data['name_customer'],
                'phone'=>$data['phone_customer'],
                'email'=>$data['email_customer'],
                'password'=>Hash::make('123456789'),
                ]);

                SearchBurial::create([
                    'surname'=>$data['surname'],
                     'name'=>$data['name'],
                     'patronymic'=>$data['patronymic'],
                     'date_birth'=>$data['date_birth'],
                     'date_death'=>$data['date_death'],
                     'location'=>$data['location'],
                     'user_id'=>Auth::user()->id,
                 ]);
                 return redirect()->back()->with("message_words_memory", "В ближайшее время с Вами свяжутся наши Партнеры по товарам которые вы заказали.");
            }
            return redirect()->back()->with("error", 'Такой телефон или почта уже зарегестрированы.');
        }
    }

    public static function searchProductFilterPage(){
        $page=1;
        $seo="Установить судьбу";

        SEOTools::setTitle($seo);
        SEOTools::setDescription($seo);
        return view('burial.search-product-filter',compact('page'));
    }
    
    
}