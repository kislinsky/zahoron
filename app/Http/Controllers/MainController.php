<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\ImageOrganization;
use App\Models\Organization;
use App\Models\OtpCodes;
use App\Models\User;
use App\Services\Burial\SearchBurialService;
use App\Services\OurWork\OurWorkService;
use App\Services\Page\IndexService;


class MainController extends Controller
{

    public static function index(){
        $existingOrg=Organization::find(2252328094666654);
        addActiveCategory('Организация похорон, Организация кремации, Подготовка отправки груза 200, Памятники, Оградки, Плитка на могилу, Венки траурные, Кресты на могилу, Вазы на могилу,', [], $existingOrg);
        return IndexService::index();
    }

    public static function contacts(){
        $page=7;
        $faqs=Faq::orderBy('id', 'desc')->get();
        return view('contacts',compact('faqs','page'));
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

    

}
