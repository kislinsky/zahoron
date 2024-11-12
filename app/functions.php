<?php

use App\Models\Acf;
use App\Models\ActivityCategoryOrganization;
use App\Models\City;
use App\Models\Edge;
use App\Models\Page;
use App\Models\Product;
use App\Models\Cemetery;
use App\Models\ImageService;
use App\Models\OrderProduct;
use App\Models\StageService;
use App\Models\AdditionProduct;
use App\Models\Burial;
use App\Models\CategoryProduct;
use App\Models\CategoryProductPriceList;
use App\Models\CategoryProductProvider;
use App\Models\CommentProduct;
use App\Models\FaqCategoryProduct;
use App\Models\FaqService;
use App\Models\Mortuary;
use App\Models\Organization;
use App\Models\ReviewsOrganization;
use App\Models\Service;
use App\Models\ServiceReviews;
use App\Models\User;
use Ausi\SlugGenerator\SlugGenerator;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;





function mainCities(){
    $cities=City::orderBy('title','asc')->take(8)->get();
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

function totalOrderService($services){
    $sum=0;
    foreach($services as $service){
        $sum+=$service->price;
    }
    return $sum;

}

function statusOrder($status){
   if($status==0){
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
    if($category->parent_id==36){
        if(isset($data['cemetery_id'])  && $data['cemetery_id']!='undefined'){
            $products=$products->where('cemetery_id',$data['cemetery_id']);
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
    if($category->parent_id==45){
        if(isset($data['district_id'])  && $data['district_id']!='undefined'){
            $products=$products->where('district_id',$data['district_id']);
         }
    }
    return $products->paginate(12);

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
    $price=$product->price;
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


function sendSms($phone,$message){
    $body = file_get_contents("https://sms.ru/sms/send?api_id=ABEA29AD-63BB-1657-6B5D-8F7501A7825C&to=".$phone."&msg=".urlencode($message)."&json=1"); 
    return $json = json_decode($body);
}


function organizationRatingFuneralAgenciesPrices($city){
    $sorted_organizations_ids=ActivityCategoryOrganization::whereIn('category_children_id',[32,33,34,35])->where('price','!=',null)->where('city_id',$city)->pluck('organization_id');
    $orgainizations=Organization::whereIn('id',$sorted_organizations_ids)->get()->map(function ($organization) {
        $price_1=ActivityCategoryOrganization::where('category_children_id',32)->where('organization_id',$organization->id)->get();
        $price_2=ActivityCategoryOrganization::where('category_children_id',33)->where('organization_id',$organization->id)->get();
        $price_3=ActivityCategoryOrganization::where('category_children_id',34)->where('organization_id',$organization->id)->get();
        $price_4=ActivityCategoryOrganization::where('category_children_id',35)->where('organization_id',$organization->id)->get();
        if(count($price_1)>0 && count($price_2)>0 && count($price_3)>0 && count($price_4)>0 ){
            $organization->all_price=$price_1->first()->price+$price_2->first()->price+$price_3->first()->price+$price_4->first()->price;
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

    $sorted_organizations_ids=ActivityCategoryOrganization::whereIn('category_children_id',[29,30,39])->where('price','!=',null)->where('city_id',$city)->pluck('organization_id');
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
    $sorted_organizations=ActivityCategoryOrganization::where('category_children_id',46)->where('price','!=',null)->orderBy('price','asc')->where('city_id',$city)->get();
    return $sorted_organizations->take(10);
}


function savingsPrice($id){
    $city=selectCity();
    $orgainizations=ActivityCategoryOrganization::where('category_children_id',$id)->where('price','!=',null)->orderBy('price','asc')->where('city_id',$city->id)->get();

    if(count($orgainizations)>0){
        $price=$orgainizations->last()->price-$orgainizations->first()->price;
        return $price;
    }
    return $price=null;

    
}

function reviewsOrganization($city){
    $reviews_organization=ReviewsOrganization::orderBy('id','desc')->where('status',1)->where('city_id',$city)->get()->take(8);
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
    if($product->category_id==46 || $product->category_id==47 || $product->category_id==32 || $product->category_id==33 || $product->category_id==34 || $product->category_id==35){
        return '<a href="'.$product->route().'" class="blue_btn">'.'Оформить</a>';
    }
    return '<div id_product="'. $product->id .'" class="blue_btn add_to_cart_product">Купить</div>';

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
  if(strlen($x)<=$length)
  {
    echo $x;
  }
  else
  {
    $y=substr($x,0,$length) . '... <br> <div class="open_all_content_block">Читать</div>';
    echo $y;
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

function countReviewsOrganization($organization){
    $reviews=ReviewsOrganization::orderBy('id','desc')->where('organization_id',$organization->id)->where('status',1)->get();
    if($reviews!=null && count($reviews)>0){
        return count($reviews);
    }
    return null;
}

function orgniaztionsFilters($data){
    $city=selectCity();
    if(isset($data['category_id'])){
        $organizations_category=ActivityCategoryOrganization::where('category_children_id',$data['category_id'])->where('city_id',$city->id)->where('role','organization');
    }else{
        $organizations_category=ActivityCategoryOrganization::where('category_children_id',categoryProductChoose()->id)->where('city_id',$city->id)->where('role','organization');
    }
    if(isset($data['cemetery_id']) && $data['cemetery_id']!=null && $data['cemetery_id']!='null'){
        $cemetery_id=$data['cemetery_id'];
        $organizations_category=$organizations_category->where(function($item) use ($cemetery_id){
            return $item->orWhere('cemetery_ids',"LIKE", "%,".$cemetery_id.",%")->orWhere('cemetery_ids',"LIKE", $cemetery_id.",%")->orWhere('cemetery_ids',"LIKE", "%,".$cemetery_id);
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
                $organization_choose=$organization->organization();
                if( $organization_choose->openOrNot()=='Открыто'){
                    $organization->open=1;
                    return $organization;
                }
            });
            $organizations_category=$organizations_category->whereIn('id',$organizations_category_ids->where('open',1)->pluck('id'));
        }  
    }
    return $organizations_category->paginate(15);
    
}

function organizationsPrices($data){
    $city=selectCity();
    $category_id=categoryProductChoose()->id;
    if(isset($data['category_id'])){
        $category_id=$data['category_id'];
    }
    $organizations_prices=ActivityCategoryOrganization::where('category_children_id',$category_id)->where('city_id',$city->id)->where('role','organization');
    if (isset($data['cemetery_id']) && $data['cemetery_id']!=null && $data['cemetery_id']!='null' ){
        $cemetery_id=$data['cemetery_id'];
        $organizations_prices=$organizations_prices->where(function($item) use ($cemetery_id){
            return $item->orWhere('cemetery_ids',"LIKE", "%,".$cemetery_id.",%")->orWhere('cemetery_ids',"LIKE", $cemetery_id.",%")->orWhere('cemetery_ids',"LIKE", "%,".$cemetery_id);
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
                $organization_choose=$organization->organization();
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
        $price_min=$organizations_prices->min('price');
        $price_middle=round($organizations_prices->avg('price'));
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
        $organizations_category=ActivityCategoryOrganization::where('category_children_id',$data['category_id'])->where('city_id',$city->id)->where('role','organization-provider');
    }else{
        $organizations_category=ActivityCategoryOrganization::where('category_children_id',categoryProductProviderChoose()->id)->where('city_id',$city->id)->where('role','organization-provider');
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
                $organization_choose=$organization->organization();
                if( $organization_choose->openOrNot()=='Открыто'){
                    $organization->open=1;
                    return $organization;
                }
            });
            $organizations_category=$organizations_category->whereIn('id',$organizations_category_ids->where('open',1)->pluck('id'));
        }  
    }

    return $organizations_category->paginate(15);
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
    
    $organizations_prices=ActivityCategoryOrganization::where('category_children_id',$category_id)->where('city_id',$city->id)->where('role','organization-provider');
   
    if(isset($data['filter_work']) && $data['filter_work']!=null){
        if($data['filter_work']=='on'){
            $organizations_prices_ids=$organizations_prices->get()->map(function ($organization) {
                $organization_choose=$organization->organization();
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
        $price_min=$organizations_prices->min('price');
        $price_middle=round($organizations_prices->avg('price'));
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


function cityWithOrganizationProvider(){
    $ids_city=ActivityCategoryOrganization::where('role','organization-provider')->pluck('city_id');
    $cities=City::whereIn('id',$ids_city)->get();
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

    return $generator->generate($item); 

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


function getCoordinatesCity($city){
    $apiKey='f85b1a2e01a144d496d767cb921c8b60';
    $client = new Client();
    $response = $client->get("https://api.opencagedata.com/geocode/v1/json?q=Город в россии {$city}&key=f85b1a2e01a144d496d767cb921c8b60");
    return $data = json_decode($response->getBody(), true)['result'][0]['geometry'];
    
}


function randomProductsPlace(){
    $products=Product::inRandomOrder()->where('city_id',selectCity()->id)->whereIn('category_id',[29,30])->get()->take(2);
    return $products;
}



function phoneImport($phone){
    if($phone!=null){
        return explode(':',$phone)[1];
    }
    return null;
}



function parseWorkingHours($input) {
    // Массив с названиями дней недели
    $days = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];
    $daysEnglish = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

    // Массив для хранения результатов
    $result = [];

    // Регулярное выражение для поиска форматов
    if (preg_match('/([ПнВтСрЧтПтСбВс]+)(?:-([ПнВтСрЧтПтСбВс]+))?:\s*([0-9]{2}:[0-9]{2})-([0-9]{2}:[0-9]{2})/', $input, $matches)) {
        // Формат "Пн-Вс: 09:00-17:00"
        $startDay = array_search($matches[1], $days);
        $endDay = array_search($matches[2], $days);
        $startTime = $matches[3];
        $endTime = $matches[4];

        for ($i = $startDay; $i <= $endDay; $i++) {
            $result[] = [
                'day' => $daysEnglish[$i],
                'time_start_work' => $startTime,
                'time_end_work' => $endTime,
            ];
        }
    } elseif (preg_match('/([ПнВтСрЧтПтСбВс]+):\s*([0-9]{2}:[0-9]{2})-([0-9]{2}:[0-9]{2})/', $input, $matches)) {
        // Формат "Пн: 09:00-17:00"
        $dayIndex = array_search($matches[1], $days);
        $startTime = $matches[2];
        $endTime = $matches[3];

        $result[] = [
            'day' => $daysEnglish[$dayIndex],
            'time_start_work' => $startTime,
            'time_end_work' => $endTime,
        ];
    } elseif (preg_match('/([ПнВтСрЧтПтСбВс]+):\s*(Выходной)/', $input, $matches)) {
        // Формат "Пн: Выходной"
        $dayIndex = array_search($matches[1], $days);
        $result[] = [
            'day' => $daysEnglish[$dayIndex],
            'time_start_work' => 'Выходной',
            'time_end_work' => 'Выходной',
        ];
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

    $city=City::where('title',$city_name)->first();
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
            
        ]);
    }
    return $city;

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
     return $result[0]['data'];
     
 }