<?php

use App\Models\Acf;
use App\Models\ActivityCategoryOrganization;
use App\Models\AdditionProduct;
use App\Models\Area;
use App\Models\Burial;
use App\Models\CategoryProduct;
use App\Models\CategoryProductPriceList;
use App\Models\CategoryProductProvider;
use App\Models\Cemetery;
use App\Models\City;
use App\Models\CommentProduct;
use App\Models\District;
use App\Models\Edge;
use App\Models\FaqCategoryProduct;
use App\Models\FaqService;
use App\Models\ImageService;
use App\Models\MessageTemplate;
use App\Models\Mortuary;
use App\Models\OrderProduct;
use App\Models\Organization;
use App\Models\Page;
use App\Models\Product;
use App\Models\ReviewsOrganization;
use App\Models\SEO;
use App\Models\Service;
use App\Models\ServiceReviews;
use App\Models\StageService;
use App\Models\TypeService;
use App\Models\User;
use App\Models\UserRequestsCount;
use App\Models\View;
use App\Models\WorkingHoursCemetery;
use App\Services\Auth\SmsService;
use App\Services\YooMoneyService;
use App\Services\ZvonokService;
use Ausi\SlugGenerator\SlugGenerator;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use PHPMailer\PHPMailer\PHPMailer;










function mainCities(){
    $cities=City::orderBy('title','asc')->where('selected_form',1)->get();
    return $cities;
}

function categoryProductChoose(){
    return CategoryProduct::where('choose_admin',1)->first();
}

function categoryProductProviderChoose(){
    return CategoryProductProvider::where('choose_admin',1)->first();
}

function childrenCategoryProducts($cat){
    return CategoryProduct::orderBy('id','desc')->where('parent_id',$cat->id)->get();
}


function childrenCategoryProductsPriceList($cat){
    return CategoryProductPriceList::orderBy('id','desc')->where('parent_id',$cat->id)->get();
}

function childrenCategoryProductsProvider($cat){
    return CategoryProductProvider::orderBy('id','desc')->where('parent_id',$cat->id)->get();
}

function childrenCategoryOrganization($organization,$category_organization){
    $categories_children=CategoryProduct::whereIn('id',ActivityCategoryOrganization::where('organization_id',$organization->id)->where('category_main_id',$category_organization->id)->pluck('category_children_id'))->get();
    return $categories_children;
}

function childrenCategoryOrganizationProvider($organization,$category_organization){
    $categories_children=CategoryProductProvider::whereIn('id',ActivityCategoryOrganization::where('organization_id',$organization->id)->where('category_main_id',$category_organization->id)->pluck('category_children_id'))->get();
    return $categories_children;
}

function sizesProducts(){
    $sizes=Product::where('size','!=',null)->pluck('size')->unique();
    $sizes_all=[];
    foreach($sizes as $size){
        $size=explode('|',$size);
        foreach($size as $size_one){
            $sizes_all[]=$size_one;
        }
    }
    return $sizes_all;
}

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
}

function generateRandomNumber($length = 4) {
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
}

function totalOrderService($services){
    $sum=0;
    foreach($services as $service){
        $sum+=$service->price;
    }
    return $sum;

}

function statusOrder($order){
   if($order->paid==0 || $order->paid==null){
        return '<div class="text_li">Не оплачен</div>';
   }
    else{
        return '<div class="text_li color_black">Оплачен</div>';
    }
}


function get_acf($id_page,$name_acf){
    $page=Page::findOrFail($id_page);
    $acf=Acf::where('name',$name_acf)->where('page_id',$page->id)->get();
    return $acf[0]->content;
}

function city_by_slug($slug){
    return City::where("slug", $slug)->first();
}
function first_city_slug(){
    return City::first()->slug;
}
function first_city_id(){
    return City::first()->id;
}
function insert_city_into_url($url, $kzn){

    // Parse the URL into its components
    $url_parts = parse_url($url);

    // Extract the path component
    $path = $url_parts['path'];
    // Use a regular expression to replace the first path segment
    $path = preg_replace('/^\/[^\/]+/', '/' . $kzn, $path, 1);
    
    if(env('API_WORK')=='true'){
        return $url_parts['scheme'] . '://' . $url_parts['host'] .  $path;
    }
    // Reconstruct the URL with the modified path
    return $url_parts['scheme'] . '://' . $url_parts['host'] . ':'.$url_parts['port']  . $path;
}

function filterProducts($data){
    $city=selectCity();
    $products=Product::orderBy('id','desc')->where('city_id',$city->id);
    if(isset($data['sort'])){
        if($data['sort']!='Сортировка' && $data['sort']!='undefined'){
            if($data['sort']=='price_down'){
                $products=Product::orderBy('total_price','desc');
            }
            if($data['sort']=='price_up'){
                $products=Product::orderBy('total_price','asc');
            }
            if($data['sort']=='date'){
                $products=Product::orderBy('id','desc');
            }
            if($data['sort']=='sale'){
                $products=Product::where('status','sale');
            }
        }
    }
    
    $category=categoryProductChoose();
    if(isset($data['category'])){
        if($data['category']!='undefined'){
            $category=CategoryProduct::findOrFail($data['category']);
        }
    }
    $products=$products->where('category_id',$category->id);
    if($category->parent->slug=='oblagorazivanie-mogil'){
        if(isset($data['cemetery_id'])  && $data['cemetery_id']!='undefined'){
            $products=$products->whereHas('organization', function ($query) use ($data) {
                $query->whereRaw("FIND_IN_SET(?, cemetery_ids)", [$data['cemetery_id']]);
            });
         }

         if(isset($data['size'])){
             if($data['size']!='Размер'  && $data['size']!='undefined'){
                 $products=$products->where('size','like','%'.$data['size'].'%');
             }
             
         }

         if(isset($data['material'])){
             if($data['material']!='Материал' && $data['material']!='undefined'){
                 $products=$products->where('material',$data['material']);
             }
         }

         if(isset($data['layering'])){
            if($data['layering']!=null && $data['layering']!='undefined'){
                $products=$products->where('layering',$data['layering']);
            }
        }
    }
    if($category->parent->slug=='organizacia-pominok'){
        if(isset($data['district_id'])  && $data['district_id']!='undefined'){
            $products=$products->where('district_id',$data['district_id']);
         }
    }
    return $products->paginate(getSeo('marketplace','count'));

}

    function faqCatsProduct($data){
        if(isset($data['category'])){
            return FaqCategoryProduct::where('category_id',$data['category'])->get();
        }return [];
        
    }
    function cemeteryProduct($data){
        $city=selectCity();
        if(isset($data['cemetery_id'])){
            return  Cemetery::findOrFail($data['cemetery_id']);
        }
        return null;

        
    }
    function ajaxCatContent($data){
        if(isset($data['category'])){
            return  CategoryProduct::findOrFail($data['category']);
        }
        return categoryProductChoose();
        
        
    }
    function ajaxCatManual($data){
        if(isset($data['category'])){
            return  CategoryProduct::findOrFail($data['category']);
        }return null;
        
    }
    


function priceAdditionals($ids){
    $additionals=AdditionProduct::whereIn('id',$ids)->pluck('price');
    $sum=0;
    foreach($additionals as $additional){
        $sum+=(int)$additional;
    }
    return $sum;

}

function defaultCity(){
    return $defaultCity = City::where('selected_admin', 1)->first();
}

function selectCity(){
    if(isset($_COOKIE['city'])){
        return $city=City::findOrFail($_COOKIE['city']);
    }
    $city=City::where('selected_admin',1)->first();
    setcookie("city", $city->id, time()+20*24*60*60,'/');
    return $city;
}

function priceProductOrder($cart_item){
    $product=Product::findOrFail($cart_item[0]);
    $price=priceProduct($product);
    if($cart_item[1]!=[]){
        foreach($cart_item[1] as $additional){
            $price+=AdditionProduct::findOrFail($additional)->price;
        }
    }return $price*$cart_item[2];
    
}

function ulCemeteries($user_id){
    $ids_cemteries=OrderProduct::where('user_id',$user_id)->pluck('cemetery_id')->unique();
    $cemteries=Cemetery::whereIn('id',$ids_cemteries)->get();
    return $cemteries;
}

function serviceOneTimeCleaning($service){
    $cemetery=Cemetery::findOrFail($service->cemetery_id);
    $city=City::findOrFail($cemetery->city_id);
    $edge=Edge::findOrFail($city->edge_id);
    $imgs_service=ImageService::where('service_id',$service->id)->get();
    $stages_service=StageService::orderBy('id','asc')->where('service_id',$service->id)->get();
    return view('service.single.single-one-time-cleaning',compact('imgs_service','stages_service','service','cemetery','edge','city'));
}

function servicePaintingFence($service){
    $cemetery=Cemetery::findOrFail($service->cemetery_id);
    $city=City::findOrFail($cemetery->city_id);
    $edge=Edge::findOrFail($city->edge_id);
    $reviews=ServiceReviews::orderBy('id','asc')->where('service_id',$service->id)->get();
    $imgs_service=ImageService::where('service_id',$service->id)->get();
    $stages_service=StageService::orderBy('id','asc')->where('service_id',$service->id)->get();
    $faqs=FaqService::orderBy('id','desc')->where('service_id',$service->id)->get();
    return view('service.single.single-painting-fence',compact('imgs_service','reviews','stages_service','service','faqs','cemetery','edge','city'));
}

function serviceDepartureBrigadeCalculation($service){
    
    $cemetery=Cemetery::findOrFail($service->cemetery_id);
    $city=City::findOrFail($cemetery->city_id);
    $edge=Edge::findOrFail($city->edge_id);
    $reviews=ServiceReviews::orderBy('id','asc')->where('service_id',$service->id)->get();
    $imgs_service=ImageService::where('service_id',$service->id)->get();
    $stages_service=StageService::orderBy('id','asc')->where('service_id',$service->id)->get();
    $faqs=FaqService::orderBy('id','desc')->where('service_id',$service->id)->get();
    return view('service.single.single-departure-brigade-calculation',compact('imgs_service','reviews','stages_service','service','faqs','cemetery','edge','city'));
}

function priceProduct($product){
    if($product->price_sale!=null){
        return $product->price_sale;
    }
    return $product->price;
}


function procentPriceProduct($product){
    if($product->price_sale!=null){
        $procent=100-intdiv($product->price_sale*100,$product->price);
        return '<div class="procent_sale_product">'.$procent.'%</div>';
    }
    return ;
}


function procentSaleProduct($product){
    if($product->price_sale!=null){
        $procent=100-intdiv($product->price_sale*100,$product->price);
        return $procent;
    }
    return ;
}


function registration($number,$name){
    $user_phone=User::where('phone',$number)->get();
    if(!isset($user_phone[0])){
        $password=generateRandomString(8);
        $user=User::create([
        'name'=>$name,
        'phone'=>$number,
        'password'=>Hash::make($password),
        ]);
    }
    return $user;
}

function ddata(){
    $token = "4e4378db0c787716d3f05adaccb75002bb1ce6b6";
        $dadata = new \Dadata\DadataClient($token, null);
        $result = $dadata->findById("party", "7707083893", 1);
}



function organizationRatingFuneralAgenciesPrices($city){
    $city=City::find($city);
    $sorted_organizations_ids=ActivityCategoryOrganization::whereIn('category_children_id',[32,33,34])->where('price','!=',null)->whereHas('organization', function ($query) use ($city) {
        $query->whereHas('city', function ($q) use ($city) {
            $q->where('area_id', $city->area_id); // Ищем организации в городах того же района
        })->where('role', 'organization')->where('status',1);
    })->pluck('organization_id');
    $orgainizations=Organization::whereIn('id',$sorted_organizations_ids)->get()->map(function ($organization) {
        $price_1=ActivityCategoryOrganization::where('category_children_id',32)->where('organization_id',$organization->id)->get();
        $price_2=ActivityCategoryOrganization::where('category_children_id',33)->where('organization_id',$organization->id)->get();
        $price_3=ActivityCategoryOrganization::where('category_children_id',34)->where('organization_id',$organization->id)->get();
        
        if($price_1->first()!=null && $price_2->first()!=null && $price_3->first()!=null){
            $organization->all_price=$price_1->first()->price+$price_2->first()->price+$price_3->first()->price;
            return $organization;
        }
         
        


    });
    
    // Сортируем продукты по минимальной цене
    $sortedProducts = $orgainizations->sortBy('all_price');
    // Возвращаем 10 самых выгодных продуктов
    return $sortedProducts->take(10);

    return null;
}
   


function organizationRatingUneralBureausRavesPrices($city){
    $city=City::find($city);

    $sorted_organizations_ids=ActivityCategoryOrganization::whereIn('category_children_id',[29,30,39])->where('price','!=',null)->whereHas('organization', function ($query) use ($city) {
        $query->whereHas('city', function ($q) use ($city) {
            $q->where('area_id', $city->area_id); // Ищем организации в городах того же района
        })->where('role', 'organization')->where('status',1);
    })->pluck('organization_id');
    $orgainizations=Organization::whereIn('id',$sorted_organizations_ids)->get()->map(function ($organization) {
        $price_1=ActivityCategoryOrganization::where('category_children_id',29)->where('organization_id',$organization->id)->get();
        $price_2=ActivityCategoryOrganization::where('category_children_id',30)->where('organization_id',$organization->id)->get();
        $price_3=ActivityCategoryOrganization::where('category_children_id',39)->where('organization_id',$organization->id)->get();
        if(count($price_1)>0 && count($price_2)>0 && count($price_3)>0 ){
            $organization->all_price=$price_1->first()->price+$price_2->first()->price+$price_3->first()->price;
            return $organization;
        }

    });
    // Сортируем продукты по минимальной цене
    $sortedProducts = $orgainizations->sortBy('all_price');

    // Возвращаем 10 самых выгодных продуктов
    return $sortedProducts->take(10);
}



function organizationratingEstablishmentsProvidingHallsHoldingCommemorations($city){
    $city=City::find($city);

    $sorted_organizations=ActivityCategoryOrganization::where('category_children_id',46)->where('price','!=',null)->orderBy('price','asc')->whereHas('organization', function ($query) use ($city) {
        $query->whereHas('city', function ($q) use ($city) {
            $q->where('area_id', $city->area_id); // Ищем организации в городах того же района
        })->where('role', 'organization')->where('status',1);
    })->get();
    return $sorted_organizations->take(10);
}


function savingsPrice($id){
    $city=selectCity();
    $orgainizations=ActivityCategoryOrganization::where('category_children_id',$id)->where('price','!=',null)->orderBy('price','asc')->whereHas('organization', function ($query) use ($city) {
        $query->whereHas('city', function ($q) use ($city) {
            $q->where('area_id', $city->area_id); // Ищем организации в городах того же района
        })->where('role', 'organization')->where('status',1);
    })->get();

    if(count($orgainizations)>0){
        $price=$orgainizations->last()->price-$orgainizations->first()->price;
        return $price;
    }
    return $price=null;

    
}

function reviewsOrganization($city){
    $reviews_organization=ReviewsOrganization::orderBy('id','desc')->where('status',1)->whereHas('organization', function ($query) use ($city) {
        $query->where('city_id', $city);
    })->get()->take(8);
    return $reviews_organization;
}

function priceAdditional($price){
    if($price==0 || $price==null){
        return null;
    }
    return $price.' ₽';
}

function addToCartProduct($id){
    $product=Product::find($id);
    // if($product->category_id==46 || $product->category_id==47 || $product->category_id==32 || $product->category_id==33 || $product->category_id==34 || $product->category_id==35){
    //     return '<a href="'.$product->route().'" class="blue_btn">'.'Оформить</a>';
    // }
    // return '<div id_product="'. $product->id .'" class="blue_btn add_to_cart_product">Купить</div>';
    return '<a href="'.$product->route().'" class="blue_btn">'.'Оформить</a>';

}

function get_ip()
{
	$value = '';
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$value = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$value = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} elseif (!empty($_SERVER['REMOTE_ADDR'])) {
		$value = $_SERVER['REMOTE_ADDR'];
	}
  
	return $value;
}


function custom_echo($x, $length) 
{
    // Проверяем, что mbstring доступен
    $mb_available = function_exists('mb_substr');
    
    if ($mb_available ? mb_strlen($x) <= $length : strlen($x) <= $length) {
        echo htmlspecialchars($x, ENT_QUOTES, 'UTF-8');
    } else {
        // Обрезаем с учетом многобайтовых символов
        $trimmed = $mb_available 
            ? mb_substr($x, 0, $length, 'UTF-8')
            : substr($x, 0, $length);
        
        // Экранируем вывод и добавляем "Читать"
        echo htmlspecialchars($trimmed, ENT_QUOTES, 'UTF-8') 
            . '... <br> <div class="open_all_content_block">Читать</div>';
    }
}


function raitingOrganization($organization){
    $reviews=ReviewsOrganization::orderBy('id','desc')->where('organization_id',$organization->id)->where('status',1)->get();
    $rating_reviews=null;
    if($reviews!=null && $reviews->count()>1){
        $rating_reviews=explode('.',strval($reviews->pluck('rating')->sum()/$reviews->count()));
        if(count($rating_reviews)>1){
            $rating_reviews=$rating_reviews[0].".".substr($rating_reviews[1],0,1);
        }else{
            $rating_reviews=$reviews->pluck('rating')->sum()/$reviews->count();
        }
    }
    return $rating_reviews;
}


function raitingProduct($product){
    $reviews=CommentProduct::orderBy('id','desc')->where('product_id',$product->id)->where('status',1)->get();
    $rating_reviews=null;
    if($reviews!=null && $reviews->count()>1){
        $rating_reviews=explode('.',strval($reviews->pluck('rating')->sum()/$reviews->count()));
        if(count($rating_reviews)>1){
            $rating_reviews=$rating_reviews[0].".".substr($rating_reviews[1],0,1);
        }else{
            $rating_reviews=$reviews->pluck('rating')->sum()/$reviews->count();
        }
    }
    return $rating_reviews;
}


function countReviewsOrganization($organization){
    $reviews=ReviewsOrganization::orderBy('id','desc')->where('organization_id',$organization->id)->where('status',1)->get();
    if($reviews!=null && count($reviews)>0){
        return count($reviews);
    }
    return null;
}

function orgniaztionsFilters($data,$category){
    $city=selectCity();

    $organizations_category = ActivityCategoryOrganization::where('category_children_id', $category->id)
    ->whereHas('organization', function ($query) use ($city) {
        $query->whereHas('city', function ($q) use ($city) {
            $q->where('area_id', $city->area_id); // Ищем организации в городах того же района
        })
        ->where('role', 'organization')
        ->where('status', 1);
    });

  
    if(isset($data['cemetery_id']) && $data['cemetery_id']!=null && $data['cemetery_id']!='null'){
        $cemetery_id=$data['cemetery_id'];
        $organizations_category=$organizations_category->whereHas('organization', function ($query) use ($cemetery_id) {
            $query->where(function($item) use ($cemetery_id){
                return $item->orWhere('cemetery_ids',"LIKE", "%,".$cemetery_id.",%")->orWhere('cemetery_ids',"LIKE", $cemetery_id.",%")->orWhere('cemetery_ids',"LIKE", "%,".$cemetery_id);
            });
        });
    }  
    if(isset($data['district_id']) && $data['district_id']!=null && $data['district_id']!='null'){
        $district_id=$data['district_id'];
        $organizations_category=$organizations_category->where(function($item) use ($district_id){
            return $item->orWhere('district_ids',"LIKE", "%,".$district_id.",%")->orWhere('district_ids',"LIKE", $district_id.",%")->orWhere('district_ids',"LIKE", "%,".$district_id);
        });
    }        
    if(isset($data['sort']) && $data['sort']!=null && $data['sort']!=''){
        if($data['sort']!='Сортировка'){
            if($data['sort']=='price_down'){
                $organizations_category=$organizations_category->orderBy('price','desc');
            }
            if($data['sort']=='price_up'){
                $organizations_category=$organizations_category->orderBy('price','asc');
            }
            if($data['sort']=='date'){
                $organizations_category=$organizations_category->orderBy('id','desc');
            }
            if($data['sort']=='popular'){
                $organizations_category=$organizations_category->orderBy('rating','desc');
            }
        }
    }
    if(isset($data['filter_work']) && $data['filter_work']!=null){
        if($data['filter_work']=='on'){
            $organizations_category_ids=$organizations_category->get()->map(function ($organization) {
                $organization_choose=$organization->organization;
                if( $organization_choose->openOrNot()=='Открыто'){
                    $organization->open=1;
                    return $organization;
                }
            });
            $organizations_category=$organizations_category->whereIn('id',$organizations_category_ids->where('open',1)->pluck('id'));
        }  
    }
    return $organizations_category->whereHas('organization', function ($query) use ($city) {$query->where('status',1);})->paginate(getSeo('organizations-catalog','count'));
    
}

function organizationsPrices($data){
    $city=selectCity();
    $category_id=categoryProductChoose()->id;
    if(isset($data['category_id'])){
        $category_id=$data['category_id'];
    }
    $organizations_prices = ActivityCategoryOrganization::where('category_children_id', $category_id)
    ->whereHas('organization', function ($query) use ($city) {
        $query->whereHas('city', function ($q) use ($city) {
            $q->where('area_id', $city->area_id); // Ищем организации в городах того же района
        })
        ->where('role', 'organization')
        ->where('status', 1);
    });
    if (isset($data['cemetery_id']) && $data['cemetery_id']!=null && $data['cemetery_id']!='null' ){
        $cemetery_id=$data['cemetery_id'];
        $organizations_prices=$organizations_prices->whereHas('organization', function ($query) use ($cemetery_id) {
            $query->where(function($item) use ($cemetery_id){
                return $item->orWhere('cemetery_ids',"LIKE", "%,".$cemetery_id.",%")->orWhere('cemetery_ids',"LIKE", $cemetery_id.",%")->orWhere('cemetery_ids',"LIKE", "%,".$cemetery_id);
            });
        });
    }
    if(isset($data['district_id']) && $data['district_id']!=null && $data['district_id']!='null'){
        $district_id=$data['district_id'];
        $organizations_prices=$organizations_prices->where(function($item) use ($district_id){
            return $item->orWhere('district_ids',"LIKE", "%,".$district_id.",%")->orWhere('district_ids',"LIKE", $district_id.",%")->orWhere('district_ids',"LIKE", "%,".$district_id);
        });
    }   
    if(isset($data['filter_work']) && $data['filter_work']!=null){
        if($data['filter_work']=='on'){
            $organizations_prices_ids=$organizations_prices->get()->map(function ($organization) {
                $organization_choose=$organization->organization;
                if( $organization_choose->openOrNot()=='Открыто'){
                    $organization->open=1;
                    return $organization;
                }
            });
            $organizations_prices=$organizations_prices->whereIn('id',$organizations_prices_ids->where('open',1)->pluck('id'));
        }  
    }
    $organizations_prices=$organizations_prices->get();
    if($organizations_prices!=null && $organizations_prices->count()>0){
        $price_min = $organizations_prices->where('price', '>', 0)->min('price');
        $price_middle=round($organizations_prices->where('price','>',0)->avg('price'));
        $price_max=$organizations_prices->max('price');
        return  [$price_min,$price_middle,$price_max];
    }
    return null;
    
}


function timeDifference($time1,$time2){

    if($time1!=null && $time2!=null){
        $startTime = new DateTime($time1);
        $endTime = new DateTime($time2);
        return $interval = $startTime->diff($endTime);
    }

    return null;
}


function getTimeByCoordinates($latitude, $longitude)
{
    $apiKey='f85b1a2e01a144d496d767cb921c8b60';
    $client = new Client();
    $response = $client->get("https://api.opencagedata.com/geocode/v1/json?q={$latitude}+{$longitude}&key={$apiKey}");
    $data = json_decode($response->getBody(), true);
    
    if (isset($data['results'][0]['annotations']['timezone'])) {
        $timezone = $data['results'][0]['annotations']['timezone']['name'];
        $currentTime = new \DateTime("now", new \DateTimeZone($timezone));

        return [
            'dayOfTheWeek'=>$currentTime->format('l'),
            'timezone' => $timezone,
            'current_time' => $currentTime->format('H:i'),
        ];
    }
    return null; // Обработка случая, когда данные не найдены
}



function orgniaztionsProviderFilters($data){

    $city=selectCity();
    if(isset($data['city_id']) && $data['city_id']!=null){
        $city=City::find($data['city_id']);
    }

    if(isset($data['category_id']) && $data['category_id']!=null){
        $organizations_category = ActivityCategoryOrganization::where('category_children_id', $data['category_id'])
        ->whereHas('organization', function ($query) use ($city) {
            $query->whereHas('city', function ($q) use ($city) {
                $q->where('area_id', $city->area_id); // Ищем организации в городах того же района
            })
            ->where('role', 'organization-provider')
            ->where('status', 1);
        });
    }else{
        $organizations_category = ActivityCategoryOrganization::where('category_children_id', categoryProductProviderChoose()->id)
        ->whereHas('organization', function ($query) use ($city) {
            $query->whereHas('city', function ($q) use ($city) {
                $q->where('area_id', $city->area_id); // Ищем организации в городах того же района
            })
            ->where('role', 'organization-provider')
            ->where('status', 1);
        });
       
    }
     
    
    if(isset($data['sort']) && $data['sort']!=null && $data['sort']!=''){
        if($data['sort']!='Сортировка'){
            if($data['sort']=='price_down'){
                $organizations_category=$organizations_category->orderBy('price','desc');
            }
            if($data['sort']=='price_up'){
                $organizations_category=$organizations_category->orderBy('price','asc');
            }
            if($data['sort']=='date'){
                $organizations_category=$organizations_category->orderBy('id','desc');
            }
            if($data['sort']=='popular'){
                $organizations_category=$organizations_category->orderBy('rating','desc');
            }
        }
    }

    if(isset($data['filter_work']) && $data['filter_work']!=null){
        if($data['filter_work']=='on'){
            $organizations_category_ids=$organizations_category->get()->map(function ($organization) {
                $organization_choose=$organization->organization;
                if( $organization_choose->openOrNot()=='Открыто'){
                    $organization->open=1;
                    return $organization;
                }
            });
            $organizations_category=$organizations_category->whereIn('id',$organizations_category_ids->where('open',1)->pluck('id'));
        }  
    }

    return $organizations_category->with('organization')
        ->whereHas('organization', function($query) {
            $query->where('status', '1');
        })->paginate(getSeo('organizations-catalog','count'));
}


function organizationsProviderPrices($data){

    $city=selectCity();
    if(isset($data['city_id']) && $data['city_id']!=null){
        $city=City::find($data['city_id']);
    }

    $category_id=categoryProductProviderChoose()->id;
    if(isset($data['category_id']) && $data['category_id']!=null){
        $category_id=$data['category_id'];
    }
    
    $organizations_prices = ActivityCategoryOrganization::where('category_children_id', $category_id)
    ->whereHas('organization', function ($query) use ($city) {
        $query->whereHas('city', function ($q) use ($city) {
            $q->where('area_id', $city->area_id); // Ищем организации в городах того же района
        })
        ->where('role', 'organization-provider')
        ->where('status', 1);
    });

   
    if(isset($data['filter_work']) && $data['filter_work']!=null){
        if($data['filter_work']=='on'){
            $organizations_prices_ids=$organizations_prices->get()->map(function ($organization) {
                $organization_choose=$organization->organization;
                if( $organization_choose->openOrNot()=='Открыто'){
                    $organization->open=1;
                    return $organization;
                }
            });
            $organizations_prices=$organizations_prices->whereIn('id',$organizations_prices_ids->where('open',1)->pluck('id'));
        }  
    }

    $organizations_prices=$organizations_prices->get();
    if($organizations_prices!=null && $organizations_prices->count()>0){
        $price_min = $organizations_prices->where('price', '>', 0)->min('price');
        $price_middle=round($organizations_prices->where('price','>',0)->avg('price'));
        $price_max=$organizations_prices->max('price');
        return  [$price_min,$price_middle,$price_max];
    }
    return null;
    
}


function searchOrganization($name){
    if($name!=null){
        $organizations=Organization::where('title','like',$name.'%')->where('role','organization-provider')->where('status',1)->get()->take(15);
        return $organizations;
    }
    return null;
}


function cityWithOrganizationProvider()
{
    // Получаем ID городов из связанных организаций
    $cityIds = ActivityCategoryOrganization::with('organization')
        ->whereHas('organization', function($query) {
            $query->where('role', 'organization-provider');
        })
        ->get()
        ->pluck('organization.city_id') // Берем city_id из связанной организации
        ->unique() // Убираем дубликаты
        ->filter() // Удаляем null значения
        ->values() // Сбрасываем ключи
        ->toArray();

    // Получаем города по найденным ID
    $cities = City::whereIn('id', $cityIds)->get();

    return $cities;
}


function nameSort($name){
    if( $name!=null && $name!=''){
        if($name!='Сортировка'){
            if($name=='price_down'){
                return 'По убыванию цены';
            }
            if($name=='price_up'){
                return 'По возрастанию цены';
            }
            if($name=='date'){
                return 'По новизне';
            }
            if($name=='popular'){
                return 'По попуялрности';
            }
        }return 'Сортировка';
    }return 'Сортировка';
}


function reviewProducts($data){
    if(isset($data['category']) && $data['category']!=null && $data['category']!=''){
        $reviews=CommentProduct::where('category_id',$data['category'])->orderBy('id','desc')->get(); 
    }
    else{
        $reviews=CommentProduct::where('category_id',categoryProductChoose()->id)->orderBy('id','desc')->get(); 
    }
    return $reviews;
}

function cartPrice(){
    $price_all=0;
    if(isset($_COOKIE['add_to_cart_product'])){
        foreach(json_decode($_COOKIE['add_to_cart_product']) as $product){
            $price_product=Product::findOrFail($product[0])->price*$product[2];
            if(count($product[1])>0){
                foreach($product[1] as $additional){
                    $price_product=$price_product+AdditionProduct::findOrFail($additional)->price;
                }
            }
            $price_all=$price_all+$price_product;
        }
    }
    return $price_all;
}


function user(){

    if(Auth::check()){
        return Auth::user();
    }
    return null;
}


function slug($item){
    $generator = new SlugGenerator(); 
    if($item!=null){
        return $generator->generate($item); 
    }
    return '';
}


function slugOrganization($item, $excludeId = null) {
    $generator = new SlugGenerator();
    $baseSlug = $generator->generate($item);
    $slug = $baseSlug;
    
    $count = 1;
    
    while (Organization::when($excludeId, function($query) use ($excludeId) {
                $query->where('id', '!=', $excludeId);
            })
            ->where('slug', $slug)
            ->exists()) {
        $slug = $baseSlug . '-' . $count;
        $count++;
    }
    
    return $slug;
}

function getBurial($id){
    return $product=Burial::findOrFail($id);   
}

function servicesBurial($ids){
    return Service::whereIn('id',$ids)->get();
}

function routeMarketplace($category){
    return redirect()->route('marketplace.category',$category->slug);
}

function activateLink($name, $active_class){
    if(request()->route()->getName() == $name){
        echo $active_class;
    }
    return "";
}

function dateBurial($date){
    $date=explode('.',$date);
    $new_date="{$date[2]}-{$date[1]}-{$date[0]}";
    return $new_date;
}

function dateBurialInBase($date){
    $date=explode('-',$date);
    $new_date="{$date[2]}.{$date[1]}.{$date[0]}";
    return $new_date;
}

function allMortuary(){
    return Mortuary::all();
}

function allCemetery(){
    return Cemetery::all();
}


function getCoordinatesCity($city,$area){
    $apiKey=env('DADATA_API_KEY');
    $client = new Client();
    $response = $client->get("https://api.opencagedata.com/geocode/v1/json?q=Город в россии в {$area} - {$city}&key={$apiKey}");
    if(isset(json_decode($response->getBody(), true)['results'][0]['geometry'])){
        return $data = json_decode($response->getBody(), true)['results'][0]['geometry'];
    }
    return null;
    
} 




function randomProductsPlace($category=null){
    if($category!=null){
        $products=Product::inRandomOrder()->where('view',1)->where('city_id',selectCity()->id)->where('category_id',$category)->get()->take(2);
        return $products;
    }
    $products=Product::inRandomOrder()->where('view',1)->where('city_id',selectCity()->id)->get()->take(2);
    return $products;
}



function phoneImport($phone){
    if($phone!=null){
        return explode(':',$phone)[1];
    }
    return null;
}


function parseWorkingHours($input) {
    // Arrays for days
    $daysRu = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];
    $daysEn = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

    // Initialize result array with all days set to "Выходной"
    $result = array_fill(0, 7, [
        'day' => '',
        'time_start_work' => 'Выходной',
        'time_end_work' => 'Выходной',
    ]);

    // Fill 'day' keys with English day names
    foreach ($daysEn as $index => $day) {
        $result[$index]['day'] = $day;
    }

    // Split input by commas to get individual day-time pairs
    $pairs = explode(', ', $input);

    foreach ($pairs as $pair) {
        // Check for range format "Пн-Пт: 09:00-17:00"
        if (preg_match('/([ПнВтСрЧтПтСбВс]+)-([ПнВтСрЧтПтСбВс]+):\s*([0-9]{2}:[0-9]{2})-([0-9]{2}:[0-9]{2})/', $pair, $matches)) {
            $startDayRu = $matches[1];
            $endDayRu = $matches[2];
            $startTime = $matches[3];
            $endTime = $matches[4];

            $startDayIndex = array_search($startDayRu, $daysRu);
            $endDayIndex = array_search($endDayRu, $daysRu);

            for ($i = $startDayIndex; $i <= $endDayIndex; $i++) {
                $result[$i]['time_start_work'] = $startTime;
                $result[$i]['time_end_work'] = $endTime;
            }
        }
        // Check for single day with times "Пн: 09:00-17:00"
        elseif (preg_match('/([ПнВтСрЧтПтСбВс]+):\s*([0-9]{2}:[0-9]{2})-([0-9]{2}:[0-9]{2})/', $pair, $matches)) {
            $dayRu = $matches[1];
            $startTime = $matches[2];
            $endTime = $matches[3];

            $dayIndex = array_search($dayRu, $daysRu);

            $result[$dayIndex]['time_start_work'] = $startTime;
            $result[$dayIndex]['time_end_work'] = $endTime;
        }
        // Check for single day with "Выходной" "Вс: Выходной"
        elseif (preg_match('/([ПнВтСрЧтПтСбВс]+):\s*(Выходной)/', $pair, $matches)) {
            $dayRu = $matches[1];

            $dayIndex = array_search($dayRu, $daysRu);

            $result[$dayIndex]['time_start_work'] = 'Выходной';
            $result[$dayIndex]['time_end_work'] = 'Выходной';
        }
    }

    return $result;
}


function extractServiceNames($html) {
    // Создаем новый объект DOMDocument
    $dom = new DOMDocument();

    // Устанавливаем параметр для игнорирования ошибок
    libxml_use_internal_errors(true);
    
    // Загружаем HTML-код и устанавливаем правильную кодировку
    $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
    $dom->loadHTML($html);
    libxml_clear_errors();
    
    // Находим все <li> элементы
    $items = $dom->getElementsByTagName('li');
    
    $result = [];
    
    // Проходим по каждому элементу <li>
    foreach ($items as $item) {
        // Извлекаем текст из <strong> и удаляем двоеточие
        $strongText = $item->getElementsByTagName('strong')->item(0)->textContent ?? '';
        
        // Добавляем название услуги в массив, удаляя лишние пробелы
        $result[] = trim(rtrim($strongText, ':'));
    }
    
    return $result;
}



function createCity($city_name,$edge_name){

    if($city_name==null || $edge_name==null){
        return null;
    }

    $city=City::where('title','like','%'.preg_replace('/^г\.\s*/ui', '', $city_name).'%')->first();
    $edge=Edge::where('title',$edge_name)->first();

    if($edge==null){
        $edge=Edge::create([
            'title'=>$edge_name,
        ]);
    }

    if($city==null){
        $city=City::create([
            'title'=>$city_name,
            'slug'=>slug($city_name),
            'edge_id'=> $edge->id,
            'area_id'=>5,

            
        ]);
    }
    return $city;

}


function createArea($area_name,$edge_name){
    if($area_name==null || $edge_name==null){
        return null;
    }

    $edge=Edge::where('title',$edge_name)->first();
    if($edge==null){
        $edge=Edge::create([
            'title'=>$edge_name,
        ]);
    }
    $area=Area::where('title',$area_name)->where('edge_id',$edge->id)->first();
    if($area==null){
        $area=Area::create([
            'title'=>$area_name,
            'edge_id'=> $edge->id,
            
        ]);
    }

    return $area;

}


function createCemetery($cemetery_name,$city_name,$width,$longitude){
    $city=City::where('title','like','%'.preg_replace('/^г\.\s*/ui', '', $city_name).'%')->first();
    
    if($city==null){
        $city=City::create([
            'title'=>$city_name,
            'slug'=>slug($city_name),
        ]);
    }
    $cemetery=Cemetery::where('title',$cemetery_name)->where('city_id',$city->id)->first();
    if($cemetery==null){
        $cemetery=Cemetery::create([
            'adres'=>$city->title,
            'width'=>$width,
            'longitude'=>$longitude,
            'img_url'=>'https://api.selcdn.ru/v1/SEL_266534/Images/main/Petropavlovsk-Kamchatsky/Cemeteries/70000001057067323!/Funeral-Services.jpg',
            'href_img'=>1,
            'title'=>$cemetery_name,
            'city_id'=> $city->id,

        ]);
        $days=['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            foreach($days as $day){
                WorkingHoursCemetery::create([
                    'day'=>$day,
                    'time_start_work'=>'00:00',
                    'time_end_work'=>'24:00',
                    'holiday'=>0,
                    'cemetery_id'=>$cemetery->id,
                ]);
            }
    }

    return $cemetery;

}

function createDistrict($district_name,$city_name){

    if($city_name==null || $district_name==null){
        return null;
    }

    $city=City::where('title',$city_name)->first();
    $district=District::where('title',$district_name)->first();

    if($city==null){
        $city=City::create([
            'title'=>$city_name,
            'slug'=>slug($city_name),
        ]);
    }
    if($district==null){
        $district=District::create([
            'title'=>$district_name,
            'city_id'=> $city->id,
            
        ]);
    }
    return $district->id;

}

function priceSerivce($price){
    if($price==null ){
        return 'Не указано';
    }
    elseif($price==0){
        return 'Бесплатно';
    }
                        
    else{
        return "{$price} ₽";
    }
}


function extractServices($html) {
    $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

    // Паттерны для извлечения услуг и цен
    $paidServicesPattern = '/<h2>Платные услуги морга:<\/h2>.*?<ul>(.*?)<\/ul>/s';
    $freeServicesPattern = '/<h2>Бесплатные услуги морга:<\/h2>.*?<ul>(.*?)<\/ul>/s';

    preg_match($paidServicesPattern, $html, $paidMatches);
    preg_match($freeServicesPattern, $html, $freeMatches);

    $services = [];

    // Обработка платных услуг
    if (!empty($paidMatches)) {
        $dom = new DOMDocument();
        @$dom->loadHTML($paidMatches[1]);
        $paidItems = $dom->getElementsByTagName('li');

        foreach ($paidItems as $item) {
            $serviceName = trim($item->nodeValue);
            $services[] = [$serviceName, 3000]; // Примерная цена для платных услуг
        }
    }

    // Обработка бесплатных услуг
    if (!empty($freeMatches)) {
        $dom = new DOMDocument();
        @$dom->loadHTML($freeMatches[1]);
        $freeItems = $dom->getElementsByTagName('li');

        foreach ($freeItems as $item) {
            $serviceName = trim($item->nodeValue);
            $services[] = [$serviceName, 0]; // Бесплатные услуги имеют цену 0
        }
    }

    return $services;
}

function convertPriceToNumber($priceString)
{
    // Убираем ненужные символы: "от", пробелы и слово "рублей"
    $price = preg_replace('/[^0-9]/', '', $priceString);
    
    // Приводим к целому числу
    return (int)$price;
}


function parsePricesTable($html)
{
    // Загружаем HTML
    $dom = new \DOMDocument();
    $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
    // Избавляемся от возможных ошибок в HTML
    @$dom->loadHTML($html);
    
    // Ищем таблицу
    $table = $dom->getElementsByTagName('table')->item(0);
    
    // Инициализируем массив для хранения результатов
    $prices = [];
    if($table!=null ){
        foreach ($table->getElementsByTagName('tr') as $row) {
            $cells = $row->getElementsByTagName('td');
            
            // Если ячейка есть, добавляем в массив
            if ($cells->length > 0) {
                $serviceName = trim($cells->item(0)->nodeValue);
                $price=convertPriceToNumber(trim($cells->item(1)->nodeValue));
                $prices[] = [$serviceName, $price];
                
            }
        }
    }
    

    return $prices;
}


function translateDayOfWeek($day)
{
    $daysOfWeek = [
        'Monday' => 'Пн',
        'Tuesday' => 'Вт',
        'Wednesday' => 'Ср',
        'Thursday' => 'Чт',
        'Friday' => 'Пт',
        'Saturday' => 'Сб',
        'Sunday' => 'Вс'
    ];

    return $daysOfWeek[$day];
}



function checkOrganizationInn($inn){
    $token = "1a103347bb77291b7c8fb45fb3154a5ac99172ce";
    $dadata = new \Dadata\DadataClient($token, null);
    $result = $dadata->findById("party", $inn, 1);
    if(isset($result[0]['data'])){
        return $result[0]['data'];
    }
    return null;
     
}


function btnAddOrganization($id_organization){
    $user=user();
    $organization=Organization::find($id_organization);
    if($organization->user_id==$user->id){
        $route=route('account.agency.organization.settings',$organization->id);
        return "<a href='$route' class='blue_btn'>Доступ уже есть</a>";
    }
    return "<div id_organization='$organization->id' class='blue_btn open_form_call_organization'>Получить доступ</div>";
}

function filtersProductsOrganizations($data){
    $organization=user()->organization();
    
    if(isset($data['parent_category_id'])){
        $categories_children=CategoryProduct::where('parent_id',$data['parent_category_id'])->pluck('id');
        $products=Product::orderBy('id','desc')->where('organization_id',$organization->id)->whereIn('category_id',$categories_children)->paginate(10);
    }
    elseif(isset($data['category_id'])){
        $products=Product::orderBy('id','desc')->where('organization_id',$organization->id)->where('category_id',$data['category_id'])->paginate(10);

    }
    elseif(isset( $data['s']) && $data['s']!=null){
        $s=$data['s'];
        $products=Product::orderBy('id','desc')->where('title','like','%'.$s.'%')->where('organization_id',$organization->id)->paginate(10);
    }
    else{
        $products=Product::orderBy('id','desc')->where('organization_id',$organization->id)->paginate(10);
    }
    return $products;
}


function slugCheckProduct($slug){
    $slug=slug($slug);
    $products=Product::where('slug','like','%'.$slug.'%')->get();
    if($products->count()>0){
        return $slug.'-'.$products->count()+1;
    }
    return $slug;
}


function btnStatusReview($status){
    if($status==0){
        return "<div class='light_blue_btn'>Ожидает модерацию</div>";
    }
    elseif($status==1){
        return "<div class='green_btn'>Одобрен</div>";
    }
}


function alert($text, $type = "success", $margin_bottom = 16, $class = ""){
    return "<div class='alert alert-$type $class' role='alert' style='margin-bottom:$margin_bottom px'>$text</div>";
}

function btnOPenOrNot($item){
    if($item=='Открыто'){
        return "<div class='btn_green'>Открыто</div>";
    }
    return "<div class='red_btn'>Закрыто</div>";
}

function childrenCategoryPriceList(){
    return CategoryProductPriceList::where('parent_id','!=',null)->get();
}




function secondsUntilAM($time,$time_now)
{
    $time=explode(':',$time);
    // Текущее время
    $now = $time_now;
    // Установим дату и время следующей точки в 6 утра
    $am = $now->copy()->setTime($time[0], $time[1], 0);


    // Если текущее время уже после 6 утра, то берем 6 утра завтрашнего дня
    if ($now->greaterThanOrEqualTo($am)) {
        $am = $am->addDay();
    }

    // Разница в секундах
    return $am->diffInSeconds($now);
}

function secondsUntilEndOfTomorrow($time_now)
{
    // Текущее время
    $now = $time_now;
    // Завтрашний день в 23:59:59
    $endOfTomorrow = $now->copy()->addDay()->endOfDay();

    // Разница в секундах
    return $endOfTomorrow->diffInSeconds($now);
}


function convertToCarbon($dateString)
{
    // Убираем лишние части строки, оставляя только дату и время
    $cleanedString = preg_replace('/GMT\+[0-9]{4} \(.*\)/', '', $dateString);

    // Преобразуем строку в объект Carbon
    $carbonDate = Carbon::parse($cleanedString);

    return $carbonDate;
}


function minPriceCategoryProductOrganization($slug){
    $city=selectCity();
    $cat=CategoryProduct::where('slug',$slug)->first();
    if($cat==null){return 0;}

    $price = ActivityCategoryOrganization::whereHas('organization', function ($query) use ($city) {
        $query->whereHas('city', function ($q) use ($city) {
            $q->where('area_id', $city->area_id); // Ищем организации в городах того же района
        })
        ->where('role', 'organization')
        ->where('status', 1);
    })->where('category_children_id',$cat->id)->where('price','>',0)->min('price');

   
    if($price!=null){
        return $price;
    }
    return 10000;
}


function mainCategoryProduct(){
    return CategoryProduct::orderBy('id','desc')->where('parent_id',null)->get();
}

function mainCategoryPriceList(){
    return CategoryProductPriceList::orderBy('id','desc')->where('parent_id',null)->get();
}

function getSeo($page,$column){
    $content = SEO::where('name',$column)->whereHas('seoObject', function ($query) use ($page) {
        $query->where('title', $page);
    })->first();
    if($content!=null){
        return $content->content;
    }
    return null;
}

function formatContent($content,$model=null){
    $city=selectCity()->title;
    $title='';
    $adres='';
    $organization='';
    if($model!=null){
        $title=$model->title;
        $adres=$model->adres;
        if($model->city!=null){
            $city=$model->city->title;
        }
    }
    
    $time=date('H:i');
    $date=date('Y-m-d');
    $year= date('Y');
 
    $result=str_replace(["{title}","{city}","{adres}","{time}","{date}","{Year}","{organization}"],[$title,$city,$adres,$time,$date,$year,$organization],$content);
    return $result;

}


function formatContentCategoryProduct($content,$model){
    $city=selectCity()->title;
    $title='';
    $adres='';
    $organization='';
    if($model!=null){
        $title=$model->title;
        $adres=$model->adres;
        if($model->city!=null){
            $city=$model->city->title;
        }
    }
    
    $time=date('H:i');
    $date=date('Y-m-d');
    $year= date('Y');
 
    $result=str_replace(["{title}","{city}","{adres}","{time}","{date}","{Year}","{organization}"],[$title,$city,$adres,$time,$date,$year,$organization],$content);
    
    return $result;

}


function formatContentBurial($content,$model){
    $city=selectCity()->title;
    $name='';
    $surname='';
    $date_birth='';
    $date_death='';
    $patronymic='';
    $adres='';
    $cemetery='';
    if($model!=null){
        $name=$model->name;
        $surname=$model->surname;
        $date_birth=$model->date_birth;
        $date_death=$model->date_death;
        $patronymic=$model->patronymic;
        $adres=$model->location_death;
        $cemetery=$model->cemetery->title;
    }
    
    $result=str_replace(["{name}","{surname}","{patronymic}","{city}","{adres}","{cemetery}","{date_birth}","{date_death}"],[$name,$surname,$patronymic,$city,$adres,$cemetery,$date_birth,$date_death],$content);
    
    
    return $result;
}

function formatContentCategory($content,$category,$models,$prices=[]){
    $city=selectCity()->title;
    $category=$category->title;
    $count=$models->total();
    $time=date('H:i');
    $date=date('Y-m-d');
    $year= date('Y');
    if($models->count()>0){
        $city=$models->first()->organization->city->title;
    }
    $price_min=$prices[0];
    $price_middle=$prices[1];
    $price_max=$prices[2];

    $result=str_replace(["{category}","{city}","{count}","{time}","{date}","{Year}","{price_min}","{price_avg}","{price_max}",],[$category,$city,$count,$time,$date,$year,$price_min,$price_middle,$price_max],$content);
    
    
    return $result;

} 

function statusBurial($status){
    if($status==0){
        return 'В обработке';
    }
    if($status==1){
        return 'Готов';
    }
    if($status==3){
        return 'Не распознан';
    }
}

function dateNewFormat($date){
    $formattedDate = Carbon::createFromFormat('d.m.Y', $date)->format('Y-m-d');
    // Сохранение в базу данных или выполнение другой логики
    return  $formattedDate;
}


function differencetHoursTimezone($timezone){
    $timezone1 = $timezone;
    $timezone2 = date_default_timezone_get();
 
    $time1 = Carbon::createFromFormat('Y-m-d H:i:s', '2023-01-01 00:00:00', $timezone1);
    $time2 = Carbon::createFromFormat('Y-m-d H:i:s', '2023-01-01 00:00:00', $timezone2);
 
    $difference = $time1->diffInHours($time2,false);

    return $difference;
}


function addHoursAndGetTime($hoursDifference) {
    // Получаем текущее серверное время
    $serverTime = Carbon::now();

    // Прибавляем разницу в часах
    $newTime = $serverTime->addHours($hoursDifference);

    // Форматируем результат в нужном формате (ЧЧ:ММ)
    return $newTime->format('H:i');
}

function addHoursAndGetDay($hoursDifference) {
    // Получаем текущее серверное время
    $serverTime = Carbon::now();

    // Прибавляем разницу в часах
    $newTime = $serverTime->addHours($hoursDifference);

    // Форматируем результат в нужном формате (день недели, например Monday)
    return $newTime->format('l'); // Возвращает полный день недели
}

function getShortDay($day)
{
    $shortDays = [
        'Monday' => 'Mo',
        'Tuesday' => 'Tu',
        'Wednesday' => 'We',
        'Thursday' => 'Th',
        'Friday' => 'Fr',
        'Saturday' => 'Sa',
        'Sunday' => 'Su',
    ];
    return $shortDays[$day] ?? '';
}

function workingDaysForShema($days)
{
    if ($days->isEmpty()) {
        return "No working hours available.";
    }

    // Преобразуем коллекцию в массив с ключами-днями
    $workingHours = [];
    foreach ($days as $day) {
        // Проверяем, что start_time и end_time не пусты, и holiday != 1
        if (!empty($day->time_start_work) && !empty($day->time_end_work) && $day->holiday != 1) {
            $workingHours[$day->day] = [
                'start' => $day->time_start_work, // Например, '09:00:00'
                'end' => $day->time_end_work,     // Например, '18:00:00'
            ];
        }
    }

    // Если массив $workingHours пуст, возвращаем пустую строку
    if (empty($workingHours)) {
        return "No valid working hours found.";
    }

    // Группируем дни с одинаковыми часами работы
    $groupedDays = [];
    $previousDay = null;
    $previousHours = null;

    // Порядок дней недели для корректной группировки
    $orderedDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

    foreach ($orderedDays as $day) {
        if (isset($workingHours[$day])) {
            $shortDay = getShortDay($day);
            $hours = $workingHours[$day];

            if ($previousHours === $hours) {
                // Продолжаем диапазон
                $groupedDays[count($groupedDays) - 1]['end'] = $shortDay;
            } else {
                // Начинаем новый диапазон
                $groupedDays[] = [
                    'start' => $shortDay,
                    'end' => $shortDay,
                    'hours' => $hours,
                ];
            }

            $previousDay = $day;
            $previousHours = $hours;
        } else {
            // Сбрасываем предыдущие значения, если день не рабочий
            $previousDay = null;
            $previousHours = null;
        }
    }

    // Формируем итоговую строку
    $result = [];
    foreach ($groupedDays as $group) {
        $range = $group['start'];
        if ($group['start'] !== $group['end']) {
            $range .= '-' . $group['end'];
        }
        $result[] = $range . ' ' . substr($group['hours']['start'], 0, 5) . '-' . substr($group['hours']['end'], 0, 5);
    }

    // Объединяем результат в одну строку
    return implode(', ', $result);
}

function getTheme(){
    if(isset($_COOKIE['theme'])){
        if($_COOKIE['theme']=='black'){
            echo 'black_theme';
        }
    }else{
        if(Auth::user()){
            if(user()->theme=='dark'){
                echo 'black_theme';
            }
        }
    }
    echo '';
    

}

function sendSms($phone,$message){
    if(env('API_WORK')=='true'){
        $smsService = new SmsService();
        
        $response = $smsService->sendSms($phone, $message);
        
        if ($response['status'] == 'OK') {
            return true;
        } else {
            return null;
        }
    }
    return null;
    
}

function generateSixDigitCode() {
    return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}

function createUserWithPhone($phone,$name='',$role='user',$inn='',$organization_form=''){

    $user=User::where('phone',$phone)->get();
    if(isset($user[0])){
        return null;
    }
    $password=generateRandomString();
    if(env('API_WORK')=='true'){
        $send_sms=sendSms($phone,"Ваш пароль для входа в личный кабине: $password");
        if($send_sms!=true){
            redirect()->back()->with('error','Ошибка отправки сообщения');
        } 
    }
   
    
    $userCreate=User::create([
        'name'=>$name,
        'phone'=>$phone,
        'password'=>Hash::make($password),
        'role'=>$role,
        'organization_form'=>$organization_form,
        'inn'=>$inn,
    ]);
    return $userCreate;

}

function edges(){
    return Edge::orderBy('title','asc')->get();
}


function normalizePhone($phone) {
    $digits = preg_replace('/[^\d]/', '', $phone);

    // Если номер начинается с 8 (российский код без +7)
    if (strlen($digits) === 11 && str_starts_with($digits, '8')) {
        return '+7' . substr($digits, 1);
    }

    // Если номер без кода страны (10 цифр)
    if (strlen($digits) === 10) {
        return '+7' . $digits;
    }

    // Если уже в формате +7 (11 цифр, начинается с 7)
    if (strlen($digits) === 11 && str_starts_with($digits, '7')) {
        return '+' . $digits;
    }

    // Если ничего не подошло — возвращаем исходное значение
    return $phone;
}


function createPayment($amount, $description = 'Оплата на сайте', $returnUrl = 'https://zahoron.ru/elizovo')
{
    // Создаем экземпляр сервиса
    $yooMoneyService = new YooMoneyService();

    // Создаем платеж через сервис
    $paymentResult = $yooMoneyService->createPayment($amount, $description, $returnUrl);

    // Проверяем результат
    if (!$paymentResult['success']) {
        // Если создание платежа не удалось, возвращаем ошибку
        return [
            'success' => false,
            'error' => 'Ошибка при создании платежа',
            'details' => $paymentResult,
        ];
    }

    // Возвращаем URL для редиректа на страницу оплаты
    return [
        'success' => true,
        'redirect_url' => $paymentResult['redirect_url'],
        'payment' => $paymentResult['payment'],
    ];
}

function callbackPayment($request) {
    $client = new YooKassa\Client();
    
    // Убедитесь, что ENV-переменные корректно загружены
    $shopId = env('SHOP_ID_YOOMONEY');
    $apiKey = env('API_YOOMONEY');

    if (empty($shopId) || empty($apiKey)) {
        return response()->json(['error' => 'YooMoney credentials are missing'], 500);
    }

    // Устанавливаем авторизацию
    $client->setAuth($shopId, $apiKey);

    // Получаем ID платежа из запроса
    $paymentId = $request->query('paymentId');
    
    try {
        // Запрашиваем информацию о платеже
        $payment = $client->getPaymentInfo($paymentId);
        
        if ($payment->getStatus() === 'succeeded') {
            return response()->json(['status' => 'success', 'payment' => $payment]);
        } else {
            return response()->json(['status' => 'failed', 'payment' => $payment]);
        }
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

function getTypeService($name){
    return  TypeService::where('title',$name)->first();
}

function getCemeteriesOptions($get)
{
    $cityId = $get('city_id'); // Получаем ID выбранного города

    if (!$cityId) {
        return []; // Если город не выбран, кладбища не отображаем
    }

    // Получаем связанные районы, города и регионы
    $city = City::with('area.edge.area')->find($cityId);

    if (!$city || !$city->area || !$city->area->edge || !$city->area->edge->area) {
        return [];
    }

    // Получаем все ID районов
    $areasIds = $city->area->edge->area->pluck('id')->toArray();

    // Получаем ID всех городов в этих районах
    $citiesIds = City::whereIn('area_id', $areasIds)->pluck('id')->toArray();

    // Получаем кладбища этих городов
    return Cemetery::whereIn('city_id', $citiesIds)->pluck('title', 'id')->toArray();
}





function generateUniqueSlug(string $title, string $modelClass, int $ignoreId = null): string
{
    // Проверяем, является ли переданный класс допустимой моделью Laravel
    if (!is_subclass_of($modelClass, Model::class)) {
        throw new InvalidArgumentException("Передан неверный класс модели: {$modelClass}");
    }

    $baseSlug = slug($title); // Преобразуем название в slug
    $slug = $baseSlug;
    $i = 1;

    while ($modelClass::where('slug', $slug)
        ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
        ->exists()) 
    {
        $slug = $baseSlug . '-' . $i;
        $i++;
    }

    return $slug;
}

function sendCode($phone,$code){
    $service=new ZvonokService();
    $service->addCall(formatPhoneNumber($phone),$code);
    return true;
}

function formatPhoneNumber($phone) {
    // Удалим все символы, кроме цифр
    $cleaned = preg_replace('/\D/', '', $phone);

    // Проверим длину номера
    if (strlen($cleaned) < 10) {
        return 'Ошибка: номер слишком короткий.';
    }

    // Если номер начинает с 8, заменим на 7
    if (strlen($cleaned) == 11 && $cleaned[0] == '8') {
        $cleaned[0] = '7';
    }

    // Если номер состоит из 10 цифр, добавим код страны
    if (strlen($cleaned) == 10) {
        $cleaned = '7' . $cleaned;
    }

    // Проверяем, что номер теперь состоит из 11 цифр
    if (strlen($cleaned) != 11) {
        return 'Ошибка: номер не соответствует формату.';
    }

    // Формируем номер в нужном формате
    return '+7' . substr($cleaned, 1);
}


function changeContent($content){
    $result=str_replace(["{city}"],[selectCity()->title],$content);
    return $result;
}


function addView($entityType, $entityId, $userId = null, $source = null)
    {
        View::create([
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'user_id' => $userId,
            'session_id' => request()->session()->getId(),
            'source' => $source,
            'ip_address' => request()->ip(),
        ]);
    }


function addActiveCategory(string $input, array $excludeCategories = [],$organization,$price=null)
{
    // Разделяем строку по запятым и удаляем лишние пробелы
    $inputCategories = splitCategories($input);

    ActivityCategoryOrganization::where('organization_id',$organization->id)->delete();

    foreach ($inputCategories as $category) {

        $category_find=CategoryProduct::where('title','LIKE','%'.$category.'%')->first();

        // Проверяем, что категория допустима и не в списке исключений
        if ($category_find!=null && !in_array($category, $excludeCategories)) {
            if($price===null && $price!=0){
                $price=getPriceByCategory($category);
            }
            ActivityCategoryOrganization::create([
                'organization_id'=>$organization->id,
                'price'=>$price,
                'category_children_id'=>$category_find->id,
                'category_main_id'=>$category_find->parent_id,
                'rating'=>$organization->rating,
            ]);

        }
    }

    return 'refew';
}







function splitCategories(string $input): array
{
    $input=str_replace("Прощальные залы", "Поминальные залы", $input);

    // Разделяем строку по запятым и символу "/"
    $categories = preg_split('/[,\/]/', $input);

    // Удаляем лишние пробелы вокруг каждого элемента
    $categories = array_map('trim', $categories);

    // Убираем пустые элементы (если такие есть)
    $categories = array_filter($categories);

    return array_values($categories); // Возвращаем массив с переиндексацией
}

function getPriceByCategory(string $category): ?int
{
    // Условия для каждой категории
    if ($category == "Организация похорон") {
        return 50000;
    }
    if ($category == "Организация кремации") {
        return 45000;
    }
    if ($category == "Подготовка отправки груза 200") {
        return 60000;
    }
    if ($category == "Памятники") {
        return 20000;
    }
    if ($category == "Оградки") {
        return 1500;
    }
    if ($category == "Плитка на могилу") {
        return 1500;
    }
    if ($category == "Кресты на могилу") {
        return 800;
    }
    if ($category == "Венки") {
        return 500;
    }
    if ($category == "Вазы на могилу") {
        return 350;
    }
    if ($category == "Поминальные обед") {
        return 1500;
    }
    if ($category == "Аренда поминального зала" || $category=='Поминальные залы' ) {
        return 3000;
    }

    // Если категория не найдена, возвращаем null
    return 1000;
}

function cleanUpOrganizationImages()
{
    // Получаем все организации
    $organizations = Organization::all();

    foreach ($organizations as $organization) {
        // Получаем все изображения организации
        $images = $organization->images;

        // Массив для хранения уникальных названий изображений
        $uniqueNames = [];

        foreach ($images as $image) {
            // Проверяем, является ли название пустым или пробелом
            if (trim($image->img_url) === '') {
                // Удаляем изображение с пустым названием
                $image->delete();
                continue;
            }

            // Нормализуем URL (удаляем параметры размера)
            $normalizedUrl = preg_replace('/_\d+x\d+\.jpg$/', '.jpg', $image->img_url);

            // Проверяем, есть ли уже такое название в массиве уникальных названий
            if (in_array($normalizedUrl, $uniqueNames)) {
                // Удаляем дубликат
                $image->delete();
            } else {
                // Добавляем нормализованное название в массив уникальных названий
                $uniqueNames[] = $normalizedUrl;
            }
        }
    }

    return "Очистка завершена.";
}


function isBrokenLink(string $url, int $timeout = 5, int $cacheMinutes = 60): bool
{
    $cacheKey = 'link_status:' . md5($url);

    return Cache::remember($cacheKey, now()->addMinutes($cacheMinutes), function () use ($url, $timeout) {
        try {
            // Сначала пробуем HEAD запрос
            $response = Http::withOptions([
                    'verify' => false,
                    'allow_redirects' => [
                        'max' => 5,
                        'strict' => true,
                        'referer' => true,
                        'protocols' => ['http', 'https'],
                        'track_redirects' => true
                    ]
                ])
                ->timeout($timeout)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                    'Accept' => 'image/webp,image/apng,image/*,*/*;q=0.8',
                ])
                ->head($url);

            if ($response->successful()) {
                return false;
            }

            // Если HEAD не сработал, пробуем GET но без загрузки тела
            $response = Http::withOptions(['verify' => false])
                ->timeout($timeout)
                ->withHeaders([
                    'Range' => 'bytes=0-0', // Запрашиваем только первый байт
                ])
                ->get($url);

            return $response->failed();
            
        } catch (RequestException $e) {
            // Логируем ошибку для отладки
            return true;
        } catch (\Exception $e) {
            return false;
        }
    });
}




function linkRegionDistrictCity($regionName, $districtName, $cityName)
{

    // Находим или создаем край

    $region = Edge::firstOrCreate(['title' => $regionName]);

    // Находим или создаем округ (с привязкой к краю)

    $district = Area::firstOrCreate([

        'title' => $districtName,
        'edge_id' => $region->id,

    ]);


    $city = City::firstOrCreate([

        'title' => $cityName,

        'edge_id'=>$district->edge->id,

        'slug'=>slug($cityName),

        'area_id' => $district->id,

    ]);
    // if(env('API_WORK')=='true'){
    //     $coord=getCoordinatesCity($cityName,$district->title);

    //     if($coord!=null){

    //         $city ->update([
    //             'width'=>$coord['lat'],
    //             'longitude'=>$coord['lng'],
    //         ]);
    //     }
    // }

    return [
        'region' => $region,
        'district' => $district,
        'city' => $city,

    ];

}


function versionProject(){
    $version=env('VERSIN_PROJECT');
    if($version == 'v1'){
        return true;
    }
    return false;
}

function contentCart($content, $model) {
    return str_replace('[Населённый пункт]', $model->city->title ?? '', $content);
}

function sendMail($to, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        // Настройки SMTP
        $mail->isSMTP();
        $mail->Host = 'connect.smtp.bz';
        $mail->SMTPAuth = true;
        $mail->Username = 'main@zahoron.ru';
        $mail->Password = 'chel192_top';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8'; // Устанавливаем кодировку UTF-8
        
        // Отправитель
        $mail->setFrom('main@zahoron.ru', 'zahoron.ru', 'UTF-8');
        
        // Получатель
        $mail->addAddress($to);
        
        // Тема письма с поддержкой кириллицы
        $mail->Subject = '=?UTF-8?B?'.base64_encode($subject).'?=';
        
        // Тело письма
        $mail->Body = $body;
        $mail->Encoding = 'base64'; // Кодировка содержимого
        
        // Альтернативное текстовое тело для старых почтовых клиентов
        $mail->AltBody = strip_tags($body);
        
        // Отправка
        $mail->send();
        return true;
    } catch (Exception $e) {
        // Логирование ошибки (рекомендуется)
        error_log('Mailer Error: '.$mail->ErrorInfo);
        return false;
    }
}


function sendMessage($name_form,$attributes,$user){
    $template = MessageTemplate::getBySlug($name_form);
    if($template->is_active==1){
        
        if($attributes==[]){
            $attributes=[
                'LOGIN'=>$user->name,
                'EMAIL'=>$user->email,
                'SITENAME'=>'zahoron.ru',
                'SITELINK'=>'https://zahoron.ru',
                'ADMINEMAIL'=>User::where('role','admin')->first()->email
            ];
        }

        $message = $template->compile($attributes);

        if ($template->type === 'email') {
            sendMail('toni.vinogradov.06@inbox.ru',$template->subject,$message);
        }

        // Для SMS
        if ($template->type === 'sms') {
            sendSms($user->phone,$message);
        }
        return true;
    }

    return false;
   

}

function transformId($num, $mode = 'encode') {
    $key = 0xABC123; // Произвольный ключ (можете изменить)
    
    if ($mode === 'encode') {
        // Кодирование: XOR и битовый сдвиг
        return ($num ^ $key) << 1 | 1;
    } elseif ($mode === 'decode') {
        // Декодирование: обратные операции
        return ($num >> 1) ^ $key;
    }
    
    return null;
}