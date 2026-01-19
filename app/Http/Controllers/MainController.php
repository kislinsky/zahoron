<?php

namespace App\Http\Controllers;


use App\Models\Faq;
use App\Rules\RecaptchaRule;
use App\Services\Burial\SearchBurialService;
use App\Services\OurWork\OurWorkService;
use App\Services\Page\IndexService;
use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class MainController extends Controller
{

    public static function index(){ 
        return IndexService::index();
    }


function generateUniqueCitySlug($baseSlug, $cityId)
{
    $slug = $baseSlug;
    $counter = 1;
    
    // Проверяем существование slug, добавляя суффикс если нужно
    while (DB::table('cities')
        ->where('slug', $slug)
        ->where('id', '!=', $cityId)
        ->exists()) {
        $slug = $baseSlug . '-' . $counter;
        $counter++;
    }
    
    return $slug;
}

    public static function acceptCookie(Request $request){ 
        $data=request()->validate([
            'value'=>['required','integer'],
        ]);
        return IndexService::acceptCookie($data);

    }


    public static function contacts(){
        
        $page=7;
       SEOTools::setTitle(formatContent(getSeo('page-kontakty','title')));
        SEOTools::setDescription(formatContent(getSeo('page-kontakty','description')));
        $title_h1=formatContent(getSeo('page-kontakty','h1'));
        $faqs=Faq::where('type_object','usual')->orderBy('id','desc')->get();
        return view('contacts',compact('faqs','page','title_h1'));
    }
   
    public static function termsIUser(){
        $content=get_acf('14','content');
        return view('terms',compact('content'));
    }
    

    public static function ourWorks(){
        return OurWorkService::index();
    }

    public static function searchProductFilter(){
        return SearchBurialService::searchProductFilterPage();
    }

    public static function speczialist(){
        return view('speczialist');
    }

    public static function changeTheme(){

        if (isset($_COOKIE['theme'])) {
            // Если существует, получить текущее значение
            $currentTheme = $_COOKIE['theme'];
        
            // Меняем значение куки — переключаем с 'black' на 'white' или наоборот
            $newTheme = ($currentTheme === 'black') ? 'white' : 'black';
        } else {
            // Если куки нет, задаем начальное значение 'black'
            $newTheme = 'black';
        }
        
        // Устанавливаем или обновляем куку с новым значением
        setcookie('theme', $newTheme, time() + 7 * 24 * 60 * 60, "/");
        
         return true;
    }


    public function store(Request $request)
    {
        $data=request()->validate([
            'g-recaptcha-response' => ['required', new RecaptchaRule],
            'theme_feedback' => 'required|string|max:255',
            'faq_feedback' => 'required|string|min:10',
            'name_feedback' => [
            'required',
            'string',
            'max:255',
            'regex:/^[а-яА-ЯёЁ\s]+$/u'  // только русские буквы и пробелы
            ],
            'phone_feedback' => 'required|string|max:20'
        ]);
        return IndexService::store($data);

    }

    public static function sendAiMessage(Request $request){
        $data=request()->validate([
            'message_ai' => 'required|string|max:1000',
            'chat_id'=>'required|string|max:1000',
        ]);
        
        return IndexService::sendAiMessage($data);

    }

    

}
