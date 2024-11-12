<?php

namespace App\Services\Burial;

use App\Models\User;
use App\Models\News;
use App\Models\Burial;
use App\Models\Service;
use App\Models\SearchBurial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SearchBurialService
{
    public static function searchProductFilter($data){
        $news=News::orderBy('id', 'desc')->take(2)->get();
        $products=Burial::where('name',$data['name'])->where('surname',$data['surname'])->where('patronymic',$data['patronymic'])->where('who',$data['who'])->where('status',1)->get();
        return view('burial.search-product',compact('products','news'));
    }

    public static function searchProduct($data){
        $news=News::orderBy('id', 'desc')->take(2)->get();
        $products=Burial::where('name',$data['name'])->where('surname',$data['surname'])->where('patronymic',$data['patronymic'])->where('location_death','like','%'.$data['city'].'%')->where('status',1)->get();
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
        return view('burial.search-product-filter',compact('page'));
    }
    
    
}