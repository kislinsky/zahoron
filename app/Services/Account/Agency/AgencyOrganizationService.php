<?php

namespace App\Services\Account\Agency;

use App\Models\ActivityCategoryOrganization;
use App\Models\Cemetery;
use App\Models\CategoryProduct;
use App\Models\City;
use App\Models\CommentProduct;
use App\Models\ImageOrganization;
use App\Models\ImageProduct;
use App\Models\MemorialMenu;
use App\Models\Organization;
use App\Models\Product;
use App\Models\ProductParameters;
use App\Models\ReviewsOrganization;
use App\Models\TypeApplication;
use App\Models\TypeService;
use App\Models\UserRequestsCount;
use App\Models\WorkingHoursOrganization;

class AgencyOrganizationService {

    public static function settings($id){
        $organization=Organization::find($id);
        $cities=City::orderBy('title','asc')->get();
        $cemeteries=[];
        if($organization->cemetery_ids!=null){
            $cemeteries=Cemetery::whereIn('id',array_filter(explode(',',$organization->cemetery_ids)))->get();
        }
        $categories=CategoryProduct::where('parent_id',null)->get();
        $categories_children=childrenCategoryProducts($categories[0]);
        $categories_organization=ActivityCategoryOrganization::where('organization_id',$organization->id)->get();
        $days=WorkingHoursOrganization::where('organization_id',$organization->id)->orderByRaw("FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')")->get();

        if($organization->user_id==user()->id){
            return view('account.agency.organization.settings',compact('days','cemeteries','cities','organization','categories_children','categories','categories_organization'));
        }
        return redirect()->back()->with('error','Эта организация не принадлежит вам');
    }


    public static function createPage(){
        $cities=City::orderBy('title','asc')->get();
        $cemeteries=[];
        $categories=CategoryProduct::where('parent_id',null)->get();
        $categories_children=childrenCategoryProducts($categories[0]);
        return view('account.agency.organization.create',compact('cemeteries','cities','categories_children','categories'));

    } 

    public static function create($data){

        if (isset($data['cemetery_ids']) && is_array($data['cemetery_ids'])) {
            $data['cemetery_ids'] = implode(",", $data['cemetery_ids']) . ',';
        }


        $filename=generateRandomString().".jpeg";
        $data['img']->storeAs("uploads_organization", $filename, "public");
        
        $filename_main=generateRandomString().".jpeg";
        $data['img_main']->storeAs("uploads_organization", $filename, "public");

        // Создаем организацию
        $organization = Organization::create([
            'title' => $data['title'],
            'status'=>0,
            'content' => $data['content'],
            'cemetery_ids' => $data['cemetery_ids'],
            'phone' => $data['phone'],
            'telegram' => $data['telegram'],
            'user_id'=>user()->id,
            'img_file'=>'uploads_organization/'.$filename,
            'img_main_file'=>'uploads_organization/'.$filename_main,
            'whatsapp' => $data['whatsapp'],
            'email' => $data['email'],
            'city_id' => $data['city_id'],
            'slug'=>slugOrganization($data['title']),
            'next_to' => $data['next_to'],
            'underground' => $data['underground'],
            'adres' => $data['adres'],
            'width' => $data['width'],
            'longitude' => $data['longitude'],
            'available_installments' => $data['available_installments'] ?? false,
            'found_cheaper' => $data['found_cheaper'] ?? false,
            'state_compensation' => $data['state_compensation'] ?? false,
        ]);


        
        // Создаем связи с категориями
        if (isset($data['categories_organization']) && isset($data['price_cats_organization'])) {
            foreach ($data['categories_organization'] as $key => $category_organization) {
                $cat = CategoryProduct::find($category_organization);
                ActivityCategoryOrganization::create([
                    'organization_id' => $organization->id,
                    'category_main_id' => $cat->parent_id,
                    'category_children_id' => $cat->id,
                    'rating' => $organization->rating,
                    'price' => $data['price_cats_organization'][$key],
                ]);
            }
        }

        // Обрабатываем рабочие часы
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $holidays = $data['holiday_day'] ?? [];
        $working_days = $data['working_day'] ?? [];

        foreach ($days as $key => $day) {
            if (in_array($day, $holidays)) {
                WorkingHoursOrganization::create([
                    'day' => $day,
                    'holiday' => 1,
                    'organization_id' => $organization->id,
                ]);
            } else {
                $time_start_work = explode(' - ', $working_days[$key])[0] ?? null;
                $time_end_work = explode(' - ', $working_days[$key])[1] ?? null;
                WorkingHoursOrganization::create([
                    'day' => $day,
                    'time_start_work' => $time_start_work,
                    'time_end_work' => $time_end_work,
                    'organization_id' => $organization->id,
                ]);
            }
        }


        if(isset($data['images']) ){
            if(count($data['images'])>0  && count($data['images'])<6){
                foreach($data['images'] as $image){
                    $filename=generateRandomString().".jpeg";
                    $image->storeAs("uploads_organization", $filename, "public");
                    ImageOrganization::create([
                        'img_file'=>'uploads_organization/'.$filename,
                        'href_img'=>0,
                        'organization_id'=>$organization->id,
                    ]);
                }
            }else{
                return redirect()->back()->with('error','Превышено допустимое количество файлов');
            }
            
        }

        return redirect()->route('home')->with('message_cart', 'Организация отправлена на модерацию');
    }


    public static function update($data){
        $organization=Organization::findOrFail($data['id']);
        $cemeteries='';
        if(isset($data['cemetery_ids']) && $data['cemetery_ids']!=null){
            $cemeteries=implode(",", $data['cemetery_ids']).',';

        }
        $organization->update([
            'title'=>$data['title'],
            'content'=>$data['content'],
            'cemetery_ids'=>$cemeteries,
            'phone'=>$data['phone'],
            'telegram'=>$data['telegram'],
            'whatsapp'=>$data['whatsapp'],
            'email'=>$data['email'],
            'city_id'=>$data['city_id'],
            'next_to'=>$data['next_to'],
            'underground'=>$data['underground'],
            'adres'=>$data['adres'],
            'width'=>$data['width'],
            'longitude'=>$data['longitude'],
        
        ]);

        if(isset($data['img'])){
            $filename=generateRandomString().".jpeg";
            $data['img']->storeAs("uploads_organization", $filename, "public");
            $organization->update([
                'img_file'=>'uploads_organization/'.$filename,
                'href_img'=>0,
            ]);
        }

        if(isset($data['img_main'])){
            $filename_main=generateRandomString().".jpeg";
            $data['img']->storeAs("uploads_organization", $filename_main, "public");
            $organization->update([
                'img_main_file'=>'uploads_organization/'.$filename_main,
                'href_main_img'=>0,

            ]);

        }


        ActivityCategoryOrganization::where('organization_id',$organization->id)->delete();
        if(isset($data['categories_organization'])){
            foreach($data['categories_organization'] as $key=>$category_organization){
                $cat=CategoryProduct::find($category_organization);
                $active_cetagory_organizaiton=ActivityCategoryOrganization::create([
                    'organization_id'=>$organization->id,
                    'category_main_id'=>$cat->parent_id,
                    'category_children_id'=>$cat->id,
                    'rating'=>$organization->rating,
                    'price'=>$data['price_cats_organization'][$key],
                ]);
            }
        }
      

        WorkingHoursOrganization::where('organization_id',$organization->id)->delete();

        $days=[
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday',
            'Sunday'
        ];
        $holidays=[];
        if(isset($data['holiday_day'])){
            $holidays=$data['holiday_day'];
        }
        
        $working_days=$data['working_day'];
        foreach($days as $key=>$day){
            
            if(in_array($day,$holidays)){
                $workings_hours=WorkingHoursOrganization::create([
                    'day'=>$day,
                    'holiday'=>1,
                    'organization_id'=>$organization->id,
                ]);
            }else{
                $time_start_work=explode(' - ',$working_days[$key])[0];
                $time_end_work=explode(' - ',$working_days[$key])[1];
                $workings_hours=WorkingHoursOrganization::create([
                    'day'=>$day,
                    'time_start_work'=>$time_start_work,
                    'time_end_work'=>$time_end_work,
                    'organization_id'=>$organization->id,
                ]);
            }
        }

        if(!isset($data['available_installments'])){
            $organization->update([
                'available_installments'=>0
            ]);
        }else{
            $organization->update([
                'available_installments'=>1
            ]);
        }

        if(!isset($data['found_cheaper'])){
            $organization->update([
                'found_cheaper'=>0
            ]);
        }else{
            $organization->update([
                'found_cheaper'=>1
            ]);
        }
        
        

        if(!isset($data['state_compensation'])){
            $organization->update([
                'state_compensation'=>0
            ]);
        }else{
            $organization->update([
                'state_compensation'=>1
            ]);
        }


        if (isset($data['images'])) {
            if (count($data['images']) > 0) {
                // Удаляем старые изображения
                foreach ($organization->images as $image) {
                    $image->delete();
                }
        
                foreach ($data['images'] as $image) {
                    if ($image instanceof \Illuminate\Http\UploadedFile) {
                        // Если это файл, сохраняем его на сервер
                        $filename = uniqid() . '.' . $image->getClientOriginalExtension();
                        $path = $image->storeAs('uploads_organization', $filename, 'public');
        
                        ImageOrganization::create([
                            'img_file' => $path, // Путь к файлу
                            'href_img' => 0, // 0 означает, что это файл
                            'organization_id' => $data['id'], // ID организации
                        ]);
                    } elseif (str_starts_with($image, 'data:image')) {
                        // Если это Base64, декодируем и сохраняем как файл
                        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $image));
                        $filename = uniqid() . '.jpg'; // Предполагаем формат JPEG
                        $path = 'uploads_organization/' . $filename;
        
                        // Сохраняем файл на сервер
                        file_put_contents(public_path('storage/' . $path), $imageData);
        
                        ImageOrganization::create([
                            'img_file' => $path, // Путь к файлу
                            'href_img' => 0, // 0 означает, что это файл
                            'organization_id' => $data['id'], // ID организации
                        ]);
                    } else {
                        // Если это ссылка, сохраняем её в базу данных
                        ImageOrganization::create([
                            'img_url' => $image, // Сохраняем ссылку
                            'href_img' => 1, // 1 означает, что это ссылка
                            'organization_id' => $data['id'], // ID организации
                        ]);
                    }
                }
            } else {
                return redirect()->back()->with('error', 'Превышено допустимое количество файлов');
            }
        }

        return redirect()->back()->with('message_cart','Организация успешно обновлена');
    }





    public static function searchOrganizations($data){
        $city=selectCity();
        $s=null;
        if(isset($data['city_id'])){
            $city=City::find($data['city_id']);
        }
        if(isset($data['s']) && $data['s']!=null){
            $s=$data['s'];
            $organizations=Organization::where('title','like','%'.$data['s'].'%')->where('city_id',$city->id)->where('role','organization')->paginate(10);
        }
        else{
            $organizations=Organization::where('city_id',$city->id)->where('role','organization')->paginate(10);
        }
        $cities=City::all();
        return view('account.agency.organization.add-organization',compact('organizations','city','cities','s'));
    }
    


    public static function aplications(){
        $types_aplications=TypeApplication::where('buy_for_organization',1)->get();
        $organization=user()->organization();
        $user=user();
        return view('account.agency.organization.pay.buy-applications',compact('user','organization','types_aplications'));
    }

    public static function callbackPayAplucations($data){
        $organization=user()->organization();
        $service=TypeService::find(1);
        $count_new=10;
        $aplication=UserRequestsCount::where('type_service_id',$service->id)->where('organization_id',$organization->id)->first();
        if($aplication!=null){
            $aplication->update([
                'count'=>$aplication->count+$count_new,
            ]);
        }else{
            $aplication=UserRequestsCount::create([
                'organization_id'=>$organization->id,
                'type_service_id'=>$service->id,
                'type_service_id'=>$service->type_application_id,
                'count'=>$count_new,
            ]);
        }
        redirect()->route('account.agency.applications');
    }


    public static function addProduct(){  
        $categories=CategoryProduct::where('parent_id',null)->get();
        $categories_children=childrenCategoryProducts($categories[0]);
        return view('account.agency.organization.product.add-product',compact('categories','categories_children'));
    }

    public static function allProducts($data){  
        $city=selectCity();
        $categories=CategoryProduct::where('parent_id',null)->get();
        $categories_children=childrenCategoryProducts($categories[0]);
        $organization=user()->organization();
        $products=collect();
        if($organization!=null){
            $products=filtersProductsOrganizations($data);
        }
        return view('account.agency.organization.product.products',compact('city','categories','categories_children','products'));
    }

    public static function deleteProduct($id){
        Product::find($id)->delete();
        return redirect()->back()->with('message_cart','Товар успешно удален');

    }

    public static function updatePriceProduct($data){
       $product=Product::find($data['product_id']);
       if($product->price_sale!=null){
            $product->update([
                'price_sale'=>$data['price'],
                'total_price'=>$data['price']
            ]);
            return "<div class='green_btn'>Готово</div>";

       }
       $product->update([
        'price'=>$data['price'],
        'total_price'=>$data['price']
        ]);
        return "<div class='green_btn'>Готово</div>";
    }

    public static function searchProduct($data){
        $organization=user()->organization();
        if(isset( $data['s']) && $data['s']!=null){
            $s=$data['s'];
            $products=Product::where('title','like','%'.$s.'%')->where('organization_id',$organization->id)->paginate(10);
            return view('account.agency.components.product.show',compact('products'));
        }

        $products=Product::where('organization_id',$organization->id)->paginate(10);
        return view('account.agency.components.product.show',compact('products'));
    }

    public static function filtersProduct($data){
        $organization=user()->organization();
        if(isset($data['parent_category_id'])){

            $categories_children=CategoryProduct::where('parent_id',$data['parent_category_id'])->pluck('id');
            $products=Product::where('organization_id',$organization->id)->whereIn('category_id',$categories_children)->paginate(10);

            return view('account.agency.components.product.show',compact('products'));
        }

        $products=Product::where('organization_id',$organization->id)->where('category_id',$data['category_id'])->paginate(10);
        return view('account.agency.components.product.show',compact('products'));
    }

    public static function createProduct($data){
        
        $organization=user()->organization();

        $slug=slugCheckProduct(slug($data['title']));

        if($data['price_sale']>$data['price']){
            return redirect()->back()->with('error','Обычная цена больше скидочной');
        }
        
        $product=Product::create([
            'title'=>$data['title'],
            'content'=>$data['content'],
            'price'=>$data['price'],
            'category_id'=>$data['cat_children'],
            'total_price'=>$data['price'],
            'category_parent_id'=>$data['cat'],
            'organization_id'=>$organization->id,
            'slug'=>$slug,
            'city_id'=>$organization->city_id,
        ]);

        if(isset($data['price_sale']) ){
            $product->update([
                'price_sale'=>$data['price_sale'],
                'total_price'=>$data['price_sale'],
            ]);
        }

        $cat=CategoryProduct::find($data['cat_children']);

        if($cat->type=='beatification'){
            if(isset($data['material'])){
                $product->update([
                    'material'=>$data['material']
                ]);
            }
            if(isset($data['size'])){
                $product->update([
                    'size'=>$data['size']
                ]);
            }
            if(isset($data['your_size'])){
                $product->update([
                    'size'=>$product->size.'|'.$data['your_size']
                ]);
            }
        }
        elseif($cat->type=='funeral-service'){
            if(isset($data['parameters'])){
                foreach(explode('|',$data['parameters']) as $parametr){
                    ProductParameters::create([
                        'title'=>$parametr,
                        'product_id'=>$product->id
                    ]);
                }
            }
            
        }
        
        elseif($cat->type=='organization-commemorations'){
            if(isset($data['width'])){
                $product->update([
                    'location_width'=>$data['width']
                ]);  
            }
            if(isset($data['longitude'])){
                $product->update([
                    'location_longitude'=>$data['longitude']
                ]);  
            }
            if(isset($data['menus'])){
                foreach(explode('|',$data['menus']) as $menu_item){
                    $items_menu=explode(':',$menu_item);
                    MemorialMenu::create([
                        'title'=>$items_menu[0],
                        'product_id'=>$product->id,
                        'content'=>$items_menu[1],
                    ]);
                }
            }

        }

        if(isset($data['images']) ){
            if(count($data['images'])>0  && count($data['images'])<6){
                foreach($data['images'] as $image){
                    $filename=generateRandomString().".jpeg";
                    $image->storeAs("uploads_product", $filename, "public");
                    ImageProduct::create([
                        'title'=>'uploads_product/'.$filename,
                        'product_id'=>$product->id,
                    ]);
                }
            }else{
                return redirect()->back()->with('error','Превышено допустимое количество файлов');
            }
            
        }
        return redirect()->back()->with('message_cart','Товар успешно добавлен');
    }

    public static function reviewsOrganization(){
        $organization=user()->organization();
        $reviews=collect();
        if($organization!=null){
            $reviews=ReviewsOrganization::orderBy('id','desc')->where('organization_id',$organization->id)->paginate(10);
        }
        return view('account.agency.organization.reviews.reviews-organization',compact('reviews'));
    }

    public static function reviewsProduct(){
        $organization=user()->organization();
        $reviews=collect();
        if($organization!=null){
            $reviews=CommentProduct::orderBy('id','desc')->where('organization_id',$organization->id)->paginate(10);
        }   
        return view('account.agency.organization.reviews.reviews-product',compact('reviews'));
    }


    public static function reviewOrganizationDelete($id){
        $review=ReviewsOrganization::find($id);
        $organization=$review->organization();
        $review->delete();
        $organization->updateRating();
        return redirect()->back()->with('message_cart','Отзыв успешно удален');

    }

    public static function reviewProductDelete($id){
        $review=CommentProduct::find($id);
        $review->delete();
        return redirect()->back()->with('message_cart','Отзыв успешно удален');

    }
    

    public static function reviewOrganizationAccept($id){
        $review=ReviewsOrganization::find($id);
        $review->update(['status'=>1]);
        $review->organization()->updateRating();
        return redirect()->back()->with('message_cart','Статус успешно обновлен');

    }

    public static function reviewProductAccept($id){
        $review=CommentProduct::find($id);
        $review->update(['status'=>1]);
        return redirect()->back()->with('message_cart','Статус успешно обновлен');

    }

    public static function updateReviewOrganization($data){
        $review=ReviewsOrganization::find($data['id_review'])->update(['content'=>$data['content_review']]);
        return 'готово';
    }

    public static function updateReviewProduct($data){
        $review=CommentProduct::find($data['id_review'])->update(['content'=>$data['content_review']]);
        return 'готово';
    }

    public static function updateOrganizationResponseReviewOrganization($data){
        $review=ReviewsOrganization::find($data['id_review'])->update(['organization_response'=>$data['organization_response_review']]);
        return $data['organization_response_review'];
    }

    public static function updateOrganizationResponseReviewProduct($data){
        $review=CommentProduct::find($data['id_review'])->update(['organization_response'=>$data['organization_response_review']]);
        return 'готово';
    }


    public static function ordersNew(){
        $organization=user()->organization();
        $orders=collect();
        if($organization!=null){
            $orders=$organization->ordersNew;
        }
        return view('account.agency.organization.product.orders.new',compact('orders'));
    }


    public static function ordersInWork(){
        $organization=user()->organization();
        $orders=collect();
        if($organization!=null){
            $orders=$organization->ordersInWork;
        }
        return view('account.agency.organization.product.orders.in-work',compact('orders'));
    }


    public static function ordersCompleted(){
        $organization=user()->organization();
        $orders=collect();
        if($organization!=null){
            $orders=$organization->ordersCompleted;
        }
        // $orders=user()->organization()->with('ordersCompleted')->paginate(1);
        return view('account.agency.organization.product.orders.completed',compact('orders'));
    }

    public static function orderComplete($order){
        $order->update(['status'=>2]);
        return redirect()->back()->with('message_cart','Статус успешно обновлен');
    }

    public static function orderAccept($order){
        $service=getTypeService('products');
        if($service!=null){
            if($service->count()>0 && $order->status==0){
                $order->update([
                    'status'=>1,
                ]);
                $service->updateCount($service->count()-1);
                return redirect()->back()->with('message_cart','Заявка успешно принята');
            }
        }
        return redirect()->back()->with('error','Закончились заявки');

    }

    public static function payApplication($type_service,$count){
        $price=$type_service->price*$count;
        $description="Покупка заявок {$type_service->title_ru}";
        $result = createPayment($price,$description,route('home'));

        if ($result['success']) { 
            // Перенаправляем пользователя на страницу оплаты
            return redirect()->away($result['redirect_url']);
        } else {
            // Обработка ошибки
            return redirect()->back()->with('error','Ошибка оплаты');
        }

    }
    
}

