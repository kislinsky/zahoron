<?php

namespace App\Services\Page;


use App\Models\Cemetery;
use App\Models\City;
use App\Models\Faq;
use App\Models\FeedbackForm;
use App\Models\Mortuary;
use App\Models\News;
use App\Models\Review;
use App\Models\Service;
use App\Models\User;
use App\Services\Burial\SearchBurialService;
use App\Services\OurWork\OurWorkService;
use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;



class IndexService
{
    public static function index(){      
        $city=selectCity();
        $services=Service::orderBy('id', 'desc')->get();
        $faqs=Faq::orderBy('id', 'desc')->get();
        
        SEOTools::setTitle(formatContent(getSeo('index-page','title'),$model=null));
        SEOTools::setDescription(formatContent(getSeo('index-page','description'),$model=null));
        $title_h1=formatContent(getSeo('index-page','h1'),$model=null);

        $page=0;
        $news_video=News::orderBy('id', 'desc')->where('type',2)->get();
        $news=News::orderBy('id', 'desc')->where('type',1)->take(3)->get();
        return view('index',compact('title_h1','services','news','faqs','page','city','news_video'));
    }

   
    public static function acceptCookie($data){
        setcookie('cookie_consent',$data['value'],time() + (20 * 24 * 60 ), '/');
        return true;
    }

    public static function store($data){

        $feedback=FeedbackForm::create([
            'topic' => $data['theme_feedback'],
            'question' => $data['faq_feedback'],
            'name' => $data['name_feedback'],
            'phone' => $data['phone_feedback']
        ]);
        $admin=User::where('role','admin')->first();
        if($admin!=null){
            sendMail($admin->email,'Новое обращение обратной связи zahoton.ru',"Id заявки:{$feedback->id}");            
        }

        return redirect()->back()->with('message_cart', 'Ваш запрос успешно отправлен!');
    }

    public static function sendAiMessage($data){
        $response = Http::withoutVerifying()
        ->get('https://hoquaromihi.beget.app/webhook/gpt-message', [
            'text' => $data['message_ai'],
            'chat_id' => $data['chat_id']
        ]);

        // Получить статус ответа
        $status = $response->status(); // 200, 404, 500, etc.

        // Получить содержимое ответа
        $content = $response->body();

        // Или как массив, если JSON
        $data = $response->json();
        return $data[0]['output'];     
    }
}