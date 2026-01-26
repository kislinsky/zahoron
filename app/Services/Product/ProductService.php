<?php

namespace App\Services\Product;

use App\Models\ActivityCategoryOrganization;
use App\Models\AdditionProduct;
use App\Models\CategoryProduct;
use App\Models\Cemetery;
use App\Models\City;
use App\Models\CommentProduct;
use App\Models\District;
use App\Models\ImageProduct;
use App\Models\MemorialMenu;
use App\Models\Mortuary;
use App\Models\Organization;
use App\Models\Page;
use App\Models\Product;
use App\Models\ProductParameters;
use App\Models\User;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\Http\Request;

class ProductService
{
    public static function singleProduct($slug){
        $product=Product::where('slug',$slug)->first();
        if($product==null || $product->view!=1){
            return redirect()->back();
        }

        addView('product',$product->id,user()->id ?? null,'site');


        $id=$product->id;
        $organization=$product->organization;
        $agent=$organization->user;
        $additionals=[];
        $ids_additionals=$product->category;
        if($ids_additionals->additional!=null){
            $additionals=AdditionProduct::whereIn('id',json_decode($ids_additionals->additional))->get();
        }
        $size=explode('|',$product->size);
        $comments=$product->reviews();
        $images=$product->getImages;
        $parameters=$product->getParam;
        $category=$product->category;
        $sales=ActivityCategoryOrganization::where('organization_id',$organization->id)->where('category_children_id',$category->id)->where('sales','!=',null)->get();
        $city=selectCity();  
        $category_products = Product::where('city_id',selectCity()->id)
            ->where('view', 1)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->whereNot('organization_id', $product->organization_id)
            ->inRandomOrder()
            ->limit(8)
            ->get();
        $cemeteries=Cemetery::orderBy('priority', 'desc')->whereIn('id',explode(',', rtrim($product->organization->cemetery_ids,',')))->get();
        
        $cities=$city->area->edge->area->flatMap(function($area_one) {
            return $area_one->cities; // Здесь предполагается, что есть связь mortuaries
        });
        $mortuaries=Mortuary::whereIn('city_id',$cities->pluck('id'))->get();

        
        if($category->type=='funeral-service'){
            SEOTools::setTitle(formatContent(getSeo('product-service-single','title'),$product));
            SEOTools::setDescription(formatContent(getSeo('product-service-single','description'),$product));
            $title_h1=formatContent(getSeo('product-service-single','h1'),$product);
        }
        else{
            SEOTools::setTitle(formatContent(getSeo('product-single','title'),$product));
            SEOTools::setDescription(formatContent(getSeo('product-single','description'),$product));
            $title_h1=formatContent(getSeo('product-single','h1'),$product);
        }

        $random_organizations_with_calls=Organization::where('city_id', $product->organization->city_id)
            ->where('id', '!=', $product->organization_id) // если нужно исключить текущую
            ->where('calls', '>', 0) // если haveCalls() проверяет это поле
            ->inRandomOrder()
            ->limit(3)
            ->get();

        if($category->slug=='pominal-nyh-obedy'){
            $district=$product->district;
            $memorial_menu=$product->memorialMenu;
            return view('product.single.single-menu',compact('random_organizations_with_calls','title_h1','product','sales','agent','city','district','images','organization','memorial_menu','category','additionals','comments','category_products'));
        }

        if($category->slug=='pominal-nye-zaly'){
            $district=$product->district;
            return view('product.single.single-hall',compact('random_organizations_with_calls','title_h1','product','sales','agent','city','district','images','organization','category','additionals','comments','category_products'));
        }

        if($category->slug=='organizacia-pohoron'){
            return view('product.single.single-organization-funeral',compact('random_organizations_with_calls','title_h1','mortuaries','product','cemeteries','sales','agent','city','images','organization','parameters','category','additionals','comments','category_products'));
        }

        if($category->slug=='organizacia-kremacii'){
            return view('product.single.single-cremation',compact('random_organizations_with_calls','title_h1','product','mortuaries','sales','agent','city','images','organization','parameters','category','additionals','comments','category_products'));
        }

        if($category->slug=='podgotovka-otpravki-gruza-200'){
            return view('product.single.single-shipment-200-cargo',compact('random_organizations_with_calls','title_h1','product','mortuaries','sales','agent','city','images','organization','parameters','category','additionals','comments','category_products'));
        }
        
        if($category->slug=='knopka-mogil'){
            return view('product.single.single-button-grave',compact('random_organizations_with_calls','title_h1','product','cemeteries','sales','agent','city','images','organization','parameters','category','additionals','comments','category_products'));
        }

        if($category->slug=='knopka-mogil'){
            return view('product.single.single-button-grave',compact('random_organizations_with_calls','title_h1','product','cemeteries','sales','agent','city','images','organization','parameters','category','additionals','comments','category_products'));
        }
        

        return view('product.single.single',compact('random_organizations_with_calls','cemeteries','title_h1','agent','product','organization','sales','images','parameters','category','size','additionals','comments','category_products'));
    }



    public static function marketplace($slug,$data){

        addView('page',Page::where('title','marketplace')->first()->id,user()->id ?? null,'site');

        $cat_slug=CategoryProduct::where('slug',$slug)->first();
        $data['category']=categoryProductChoose()->id;
        if($cat_slug!=null){
            $data['category']=$cat_slug->id;
        }



        $city=selectCity();
        $page=2;
        $materials_filter=Product::pluck('material')->unique()->filter(function ($value) { return !is_null($value); });
        $layerings=Product::pluck('layering')->unique()->filter(function ($value) { return !is_null($value); });
        $sort='Сортировка';
        if(isset($data['sort']) && $data['sort']!=null){
           $sort=$data['sort'];
        }
        $reviews=reviewProducts($data);
        $cats=CategoryProduct::orderBy('id','desc')->where('parent_id',null)->get();
        $products=filterProducts($data);
        $faqs=faqCatsProduct($data);
        $cemeteries_all = $city->cemeteries->sortByDesc('priority');
        $cemetery=cemeteryProduct($data);
        $district=null;
        if(isset($data['district_id'])  && $data['district_id']!='undefined'){
           $district=District::find($data['district_id']);
        }
        $category=ajaxCatContent($data);
        $districts_all=$city->districts;

        if($products->total()<3){
            SEOMeta::setRobots('noindex, nofollow');
        }

        SEOTools::setTitle(formatContentCategory(getSeo($category->slug.'-marketplace','title'),$category,$products));
        SEOTools::setDescription(formatContentCategory(getSeo($category->slug.'-marketplace','description'),$category,$products));
        $title_h1=formatContentCategory(getSeo($category->slug.'-marketplace','h1'),$category,$products);

        $pages_navigation=[['Главная',route('index')],['Маркетплэйс',route('marketplace')],[$category->title]];

        
        return view('product.marketplace',compact('pages_navigation','title_h1','district','layerings','sort','districts_all','cemeteries_all','reviews','products','city','cats','materials_filter','faqs','cemetery','category','page'));

    }



    // public static function category($data,$category){
    //     $city=selectCity();
    //     $page=2;
    //     $materials_filter=Product::pluck('material')->unique()->filter(function ($value) { return !is_null($value); });
    //     $layerings=Product::pluck('layering')->unique()->filter(function ($value) { return !is_null($value); });
    //     $price_all=cartPrice();
    //     $sort='Сортировка';
    //     if(isset($data['sort']) && $data['sort']!=null){
    //        $sort=$data['sort'];
    //     }
    //     $reviews=reviewProducts($data);
    //     $cats=CategoryProduct::orderBy('id','desc')->where('parent_id',null)->get();
    //     $products=filterProducts($data);
    //     $faqs=faqCatsProduct($data);
    //     $cemeteries_all=$city->cemeteries();
    //     $cemetery=cemeteryProduct($data);
    //     $district=null;
    //     if(isset($data['district_id'])  && $data['district_id']!='undefined'){
    //        $district=District::find($data['district_id']);
    //     }
    //     $category=ajaxCatContent($data);
    //     $districts_all=$city->districts();
    //     return view('product.marketplace',compact('district','layerings','sort','districts_all','cemeteries_all','reviews','products','city','cats','price_all','materials_filter','faqs','cemetery','category','page'));

    // }




    public static function ajaxCatReviews($data){
        $reviews=reviewProducts($data);
        return view('product.components.catalog.reviews',compact('reviews'));
    }


    public static function filterShow($data){
        $category=categoryProductChoose();
        if(isset($data['category'])){
            if($data['category']!='undefined'){
                $category=CategoryProduct::findOrFail($data['category']);
            }
        }
        $products=filterProducts($data);
        if($category->parent_id==36){
            return view("product.components.catalog.products-show-beautification", compact("products",'category'));
        }
        if($category->parent_id==31){
            return view("product.components.catalog.products-show-funeral-service", compact("products",'category'));
       }
       if($category->parent_id==45){
            return view("product.components.catalog.products-show-organization-commemorations", compact("products",'category'));
       }

        return view("product.components.catalog.products-show-beautification", compact("products",'category'));
    }

    public static function ajaxProductCat($data){ 
        $faqs=faqCatsProduct($data);
        return view("product.components.catalog.faq-show", compact("faqs"));
    }

    public static function ajaxCemeteryCat($data){
        $cemetery=cemeteryProduct($data);
        return view("product.components.catalog.cemetery-show", compact("cemetery"));
    }
    public static function ajaxCatContent($data){
        $category=ajaxCatContent($data);
        return view("product.components.catalog.cat-content-show", compact("category"));
    }
    public static function ajaxCatManual($data){
        $category=ajaxCatManual($data);
        return view("product.components.catalog.cat-manual-show", compact("category"));
    }

    public static function ajaxTitle($data){
        // $cemetery=null;
        // $district=null;
        $category=null;
        if(isset($data['category'])){
            $category=CategoryProduct::find($data['category']);
        }
        
        // if(isset($data['district_id'])){
        //     $district=District::find($data['district_id']);
        // }
        // if(isset($data['cemetery_id'])){
        //     $cemetery=Cemetery::find($data['cemetery_id']);
        // }
        $city=selectCity();
        $products=filterProducts($data);
        $title_h1=formatContentCategory(getSeo($category->slug.'-marketplace','h1'),$category,$products);

        return view("product.components.catalog.title", compact('title_h1'));
    }

    public static function addReview($data){
        $product=Product::find($data['product_id']);
        CommentProduct::create([
            'name'=>$data['name'],
            'surname'=>$data['surname'],
            'product_id'=>$product->id,
            'category_id'=>$product->category_id,
            'organization_id'=>$product->organization_id,
            'content'=>$data['message'],
        ]);
        return redirect()->back()->with("message_words_memory", 'Отзыв отправлен на проверку');

    }
    
  public static function search($request)
    {
        $query = $request->get('query', '');
        
        if (strlen($query) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Минимум 2 символа для поиска'
            ]);
        }
        
        // Базовый запрос
        $productsQuery = Product::query()
            ->where('title', 'LIKE', "%{$query}%");
        
        // Фильтр по городу (если используется)
        $cityId = selectCity()->id ?? null;
        if ($cityId) {
            $productsQuery->where('city_id', $cityId);
        }
        
        // Фильтр по организации (если передан)
        if ($request->has('organization_id') && $request->organization_id) {
            $productsQuery->where('organization_id', $request->organization_id);
        }
        
        // Загружаем связи и выполняем запрос
        $products = $productsQuery
            ->with(['organization', 'category', 'getImages'])
            ->where('view', 1) // используйте ваше поле view вместо status
            ->limit(20)
            ->get()
            ->map(function ($product) {
                
                // Проверяем наличие (адаптируйте под вашу логику)
                $inStock = true; // по умолчанию, адаптируйте под вашу логику
                
                // Расчет скидки (используем статический вызов)
                $discountPercent = self::calculateDiscountPercent($product->price, $product->old_price);
                
                // Проверяем новинку
                $isNew = false;
                if ($product->created_at) {
                    $isNew = $product->created_at->gt(now()->subDays(30));
                }
                
                return [
                    'id' => $product->id,
                    'title' => $product->title,
                    'slug' => $product->slug,
                    'price' => $product->price,
                    'old_price' => $product->old_price,
                    'rating' => $product->rating ?? 0,
                    'image_url' => $product->getImages->first()->url(),
                    'route' => $product->route(),
                    'reviews_count' => $product->reviewsAccept()->count(), // используйте ваш метод
                    'category_name' => $product->category->title ?? '',
                    'organization_name' => $product->organization->title ?? '',
                    'in_stock' => $inStock,
                    'quantity' => $product->quantity ?? 0,
                    'discount_percent' => $discountPercent,
                    'is_new' => $isNew,
                ];
            });
        
        return response()->json([
            'success' => true,
            'data' => $products,
            'count' => $products->count()
        ]);
    }

    private static function calculateDiscountPercent($price, $oldPrice)
    {
        if (!$oldPrice || $oldPrice <= 0 || $price >= $oldPrice) {
            return null;
        }
        
        $discount = (($oldPrice - $price) / $oldPrice) * 100;
        return round($discount);
    }
}