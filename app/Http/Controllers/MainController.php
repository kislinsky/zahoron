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


        $templates = [
            // SMS для организаций (уведомления о новых заявках)
            [
                'id' => 16,
                'name' => 'смс сообщение при заявке поп ап поминок для организаций',
                'type' => 'SMS',
                'template' => 'Новая заявка на поминки. Клиент: {{ client_name }}, телефон: {{ phone }}',
                'subject' => null,
                'variables' => json_encode(['client_name', 'phone']),
                'is_active' => true,
            ],
            [
                'id' => 11,
                'name' => 'смс сообщение для организаций при заявке рит услуг',
                'type' => 'SMS',
                'template' => 'Новая заявка на ритуальные услуги. Клиент: {{ client_name }}, услуга: {{ service_type }}',
                'subject' => null,
                'variables' => json_encode(['client_name', 'service_type']),
                'is_active' => true,
            ],
            [
                'id' => 8,
                'name' => 'смс сообщение для организаций при заявке облагораживания',
                'type' => 'SMS',
                'template' => 'Новая заявка на облагораживание. Клиент: {{ client_name }}',
                'subject' => null,
                'variables' => json_encode(['client_name']),
                'is_active' => true,
            ],

            // SMS для клиентов (информационные)
            [
                'id' => 15,
                'name' => 'сообщение при заявке pop up поминки для пользователя',
                'type' => 'SMS',
                'template' => 'Ваша заявка на поминки принята. Ожидайте звонка менеджера.',
                'subject' => null,
                'variables' => json_encode([]),
                'is_active' => true,
            ],
            [
                'id' => 14,
                'name' => 'сообщение при заявке продукта для пользователя',
                'type' => 'SMS',
                'template' => 'Заявка на продукт {{ product_name }} принята. Номер: {{ order_id }}',
                'subject' => null,
                'variables' => json_encode(['product_name', 'order_id']),
                'is_active' => true,
            ],
            [
                'id' => 12,
                'name' => 'смс сообщение при заявке продукта',
                'type' => 'SMS',
                'template' => 'Заявка на продукт {{ product_name }} принята',
                'subject' => null,
                'variables' => json_encode(['product_name']),
                'is_active' => true,
            ],
            [
                'id' => 7,
                'name' => 'покупка услуг по уходу за захоронением',
                'type' => 'SMS',
                'template' => 'Услуга ухода за захоронением активирована для захоронения {{ burial }}',
                'subject' => null,
                'variables' => json_encode(['burial']),
                'is_active' => true,
            ],
            [
                'id' => 6,
                'name' => 'покупка товара или услуги с маркетплэйса',
                'type' => 'SMS',
                'template' => 'Заказ #{{ order_id }} оформлен. Статус в личном кабинете.',
                'subject' => null,
                'variables' => json_encode(['order_id']),
                'is_active' => true,
            ],
            [
                'id' => 5,
                'name' => 'покупка геолокации захоронения',
                'type' => 'SMS',
                'template' => 'Геолокация для захоронения активирована.',
                'subject' => null,
                'variables' => json_encode([ 'map_link']),
                'is_active' => true,
            ],
            [
                'id' => 4,
                'name' => 'сообщение при заявке pop up поминки',
                'type' => 'SMS',
                'template' => 'Заявка на поминки отправлена. Ожидайте звонка.',
                'subject' => null,
                'variables' => json_encode([]),
                'is_active' => true,
            ],
            [
                'id' => 3,
                'name' => 'сообщение при заявке pop up ритуальные услуги',
                'type' => 'SMS',
                'template' => 'Заявка на ритуальные услуги принята. Ожидайте звонка.',
                'subject' => null,
                'variables' => json_encode([]),
                'is_active' => true,
            ],
            [
                'id' => 2,
                'name' => 'сообщение при заявке pop up умерший',
                'type' => 'SMS',
                'template' => 'Заявка принята. ',
                'subject' => null,
                'variables' => json_encode([]),
                'is_active' => true,
            ],
            [
                'id' => 1,
                'name' => 'сообщение при заявке pop up облогораживание',
                'type' => 'SMS',
                'template' => 'Заявка на облагораживание отправлена. Ожидайте звонка.',
                'subject' => null,
                'variables' => json_encode([]),
                'is_active' => true,
            ],

            // Email для организаций
            [
                'id' => 17,
                'name' => 'email сообщение при поп ап заявке поминок для организаций',
                'type' => 'Email',
                'template' => 'Новая заявка на поминки<br><br>Клиент: {{ client_name }}<br>Телефон: {{ phone }}<br>Email: {{ client_email }}<br>Дата: {{ desired_date }}<br><br>Примите заявку в личном кабинете.',
                'subject' => 'Новая заявка на поминки',
                'variables' => json_encode(['client_name', 'phone', 'client_email', 'desired_date']),
                'is_active' => true,
            ],
            [
                'id' => 10,
                'name' => 'email сообщение для организаций при заявке рит услуг',
                'type' => 'Email',
                'template' => 'Новая заявка на ритуальные услуги<br><br>Услуга: {{ service_type }}<br>Клиент: {{ client_name }}<br>Телефон: {{ phone }}<br>Комментарий: {{ comment }}<br><br>Примите заявку в личном кабинете.',
                'subject' => 'Новая заявка на ритуальные услуги',
                'variables' => json_encode(['service_type', 'client_name', 'phone', 'comment']),
                'is_active' => true,
            ],
            [
                'id' => 9,
                'name' => 'email сообщение для организаций при заявке облагораживания',
                'type' => 'Email',
                'template' => 'Новая заявка на облагораживание<br><br>Клиент: {{ client_name }}<br>Телефон: {{ phone }}<br>Работы: {{ desired_services }}<br><br>Примите заявку в личном кабинете.',
                'subject' => 'Новая заявка на облагораживание',
                'variables' => json_encode(['client_name', 'phone', 'desired_services']),
                'is_active' => true,
            ],

            // Email для клиентов (информационные)
            [
                'id' => 13,
                'name' => 'email сообщение при заявке продукта',
                'type' => 'Email',
                'template' => 'Ваша заявка на продукт {{ product_name }} принята<br><br>Номер заявки: #{{ order_id }}<br>Дата: {{ order_date }}.',
                'subject' => 'Заявка на продукт принята',
                'variables' => json_encode(['product_name', 'order_id', 'order_date']),
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            DB::table('message_templates')->updateOrInsert(
                ['id' => $template['id']],
                [
                    'name' => $template['name'],
                    'type' => $template['type'],
                    'template' => $template['template'],
                    'subject' => $template['subject'],
                    'variables' => $template['variables'],
                    'is_active' => $template['is_active'],
                    'updated_at' => Carbon::now(),
                ]
            );
        }


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
        $faqs=Faq::orderBy('id', 'desc')->get();
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
