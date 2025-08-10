<?php

namespace App\Http\Controllers;


use App\Models\Cemetery;
use App\Models\Church;
use App\Models\Crematorium;
use App\Models\Faq;
use App\Models\Mortuary;
use App\Models\Mosque;
use App\Models\ReviewCrematorium;
use App\Services\Burial\SearchBurialService;
use App\Services\OurWork\OurWorkService;
use App\Services\Page\IndexService;
use App\Services\ZvonokService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class MainController extends Controller
{

    public static function index(){ 

    //    $models = [
    //     Cemetery::class => 'title',
    //     Crematorium::class => 'name', 
    //     Mortuary::class => 'title',
    //     Mosque::class => 'name',
    //     Church::class => 'title'
    // ];

    // $totalUpdated = 0;
    // $results = [];

    // foreach ($models as $model => $titleField) {
    //     $count = 0;
        
    //     DB::transaction(function () use ($model, $titleField, &$count) {
    //         $model::query()
    //             ->whereNotNull($titleField)
    //             ->orderBy('id')
    //             ->chunkById(500, function ($records) use ($model, $titleField, &$count) {
    //                 foreach ($records as $record) {
    //                     $newSlug = generateUniqueSlug($record->{$titleField}, $model);
                        
    //                     if ($newSlug !== $record->slug) {
    //                         $record->slug = $newSlug;
    //                         $record->save();
    //                         $count++;
    //                     }
    //                 }
    //             });
    //     });

    //     $results[$model] = $count;
    //     $totalUpdated += $count;
    // }

    // return [
    //     'total_updated' => $totalUpdated,
    //     'details' => $results
    // ];

        return IndexService::index();
    }

    public static function acceptCookie(Request $request){ 
        $data=request()->validate([
            'value'=>['required','integer'],
        ]);
        return IndexService::acceptCookie($data);

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
