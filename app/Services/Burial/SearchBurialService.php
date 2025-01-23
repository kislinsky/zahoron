<?php

namespace App\Services\Burial;

use App\Models\City;
use App\Models\News;
use App\Models\User;
use App\Models\Burial;
use App\Models\SearchBurial;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Artesaos\SEOTools\Facades\SEOTools;

class SearchBurialService
{
    public static function searchProductFilter($data){

        $seo="Поиск ".$data['name'] . $data['surname'] . $data['patronymic'] . $data['who'];

        SEOTools::setTitle($seo);
        SEOTools::setDescription($seo);

        $news=News::orderBy('id', 'desc')->take(2)->get();
        $products=Burial::where('name',$data['name'])->where('surname',$data['surname'])->where('patronymic',$data['patronymic'])->where('who',$data['who'])->where('status',1)->get();
        return view('burial.search-product',compact('products','news'));
    }

    public static function searchProduct($data){
        $cemetery_ids=selectCity()->cemeteries->pluck('id');

        if(isset($data['city'])){
            $city=City::where('title','like', '%'.$data['city'].'%')->get();
            if($city[0]!=null){
                $cemetery_ids=$city[0]->cemeteries->pluck('id');
            }
        }

        $seo="Поиск ".$data['name'] . $data['surname'] . $data['patronymic'];

        SEOTools::setTitle($seo);
        SEOTools::setDescription($seo);

        $news=News::orderBy('id', 'desc')->take(2)->get();
        $products=Burial::where('name',$data['name'])->where('surname',$data['surname'])->where('patronymic',$data['patronymic'])->whereIn('cemetery_id',$cemetery_ids)->where('status',1)->get();
        return view('burial.search-product',compact('products','news'));
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