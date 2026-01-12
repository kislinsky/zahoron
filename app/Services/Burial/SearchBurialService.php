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
            $products=$products->paginate(10);
        }
        return view('burial.search-burial-result',compact('products','news','page'));
    }



    public static function searchBurial(){
        SEOTools::setTitle(formatContent(getSeo('page-search-burial','title')));
        SEOTools::setDescription(formatContent(getSeo('page-search-burial','description')));
        $title_h1=formatContent(getSeo('page-search-burial','h1'));
        
        $services = Service::orderBy('id', 'desc')->get();
        $burials=Burial::where('date_death', 'LIKE', date('d.m').'%')->whereIn('cemetery_id',selectCity()->cemeteries->pluck('id'))->get();
        $news=News::orderBy('id', 'desc')->take(2)->get();
        return view('burial.search-burial',compact('news','services','burials','title_h1'));
    }
    
     public static function searchProductRequestAdd($data)
    {
        try {
            // Обработка загрузки фото
            $imageAttachments = [];
            
            // Проверяем наличие загруженных файлов
            if (isset($data['photos']) && is_array($data['photos'])) {
                foreach ($data['photos'] as $photo) {
                    if ($photo && $photo->isValid()) {
                        $filename = uniqid() . '_' . time() . '.jpeg';
                        $path = $photo->storeAs('uploads_order', $filename, 'public');
                        
                        if ($path) {
                            $imageAttachments[] = [
                                'filename' => $filename,
                                'path' => $path,
                                'uploaded_at' => now()->toDateTimeString()
                            ];
                        }
                    }
                }
            }
    
            if (Auth::check()) {
                SearchBurial::create([
                    'surname' => $data['surname'],
                    'name' => $data['name'],
                    'patronymic' => $data['patronymic'],
                    'date_birth' => $data['date_birth'],
                    'date_death' => $data['date_death'],
                    'landmark' => $data['landmark'] ?? null, // Исправлено
                    'location' => $data['location'],
                    'image_attachments' => !empty($imageAttachments) ? json_encode($imageAttachments) : null,
                    'user_id' => Auth::user()->id,
                ]);
                
                sendMail(admin()->email, "Новая заявка на поиск захоронения", 'Ответьте на заявку');
                return redirect()->back()->with("message_words_memory", "Ваша заявка успешно отправлена.");
            } else {
                $user_email = User::where('email', $data['email_customer'])->first();
                $user_phone = User::where('phone', $data['phone_customer'])->first();
                
                if (!$user_email && !$user_phone) {
                    $last_id = User::create([
                        'name' => $data['name_customer'],
                        'phone' => $data['phone_customer'],
                        'email' => $data['email_customer'],
                        'password' => Hash::make('123456789'),
                    ]);
    
                    SearchBurial::create([
                        'surname' => $data['surname'],
                        'name' => $data['name'],
                        'patronymic' => $data['patronymic'],
                        'date_birth' => $data['date_birth'],
                        'date_death' => $data['date_death'],
                        'landmark' => $data['landmark'] ?? null, // Исправлено
                        'location' => $data['location'],
                        'image_attachments' => !empty($imageAttachments) ? json_encode($imageAttachments) : null,
                        'user_id' => $last_id->id,
                    ]);
                    
                    sendMail(admin()->email, "Новая заявка на поиск захоронения", 'Ответьте на заявку');
                    return redirect()->back()->with("message_words_memory", "Ваша заявка успешно отправлена.");
                }
                
                return redirect()->back()->with("error", 'Такой телефон или почта уже зарегистрированы.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with("error", 'Ошибка при отправке заявки: ' . $e->getMessage());
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