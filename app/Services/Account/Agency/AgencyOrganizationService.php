<?php

namespace App\Services\Account\Agency;

use App\Models\ActivityCategoryOrganization;
use App\Models\Cemetery;
use App\Models\CategoryProduct;
use App\Models\City;
use App\Models\CommentProduct;
use App\Models\ImageProduct;
use App\Models\MemorialMenu;
use App\Models\Organization;
use App\Models\Product;
use App\Models\ProductParameters;
use App\Models\ReviewsOrganization;
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


    public static function update($data){
        $organization=Organization::findOrFail($data['id']);

        $cemeteries='';
        if(isset($data['cemetery_ids'])){
            $cemeteries=implode(",", $data['cemetery_ids']).',';

        }
        $organization->update([
            'title'=>$data['title'],
            'mini_content'=>$data['mini_content'],
            'content'=>$data['content'],
            'cemetery_ids'=>$cemeteries,
            'phone'=>$data['phone'],
            'telegram'=>$data['telegram'],
            'whatsapp'=>$data['whatsapp'],
            'email'=>$data['email'],
            'city_id'=>$data['city'],
            'next_to'=>$data['next_to'],
            'underground'=>$data['underground'],
            'adres'=>$data['adres'],
            'width'=>$data['width'],
            'longitude'=>$data['longitude'],
        
        ]);
        ActivityCategoryOrganization::where('organization_id',$organization->id)->delete();
        if(isset($data['categories_organization'])){
            foreach($data['categories_organization'] as $key=>$category_organization){
                $cat=CategoryProduct::find($category_organization);
                $active_cetagory_organizaiton=ActivityCategoryOrganization::create([
                    'organization_id'=>$organization->id,
                    'category_main_id'=>$cat->parent_id,
                    'category_children_id'=>$cat->id,
                    'city_id'=>$organization->city_id,
                    'rating'=>$organization->rating,
                    'cemetery_ids'=>$cemeteries,
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
        
        if(!isset($data['сonclusion_contract'])){
            $organization->update([
                'сonclusion_contract'=>0
            ]);
        }else{
            $organization->update([
                'сonclusion_contract'=>1
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
        $organization=user()->organization();
        $user=user();
        return view('account.agency.organization.pay.buy-applications',compact('user','organization'));
    }


    public static function buyAplicationsFuneralServices($count){  
        $organization=user()->organization();
        $organization->update([
            'applications_funeral_services'=>$organization->applications_funeral_services+$count,
        ]);
        return redirect()->back()->with('message_cart','Зявки успешно куплены');
    }

    public static function buyAplicationsCallsOrganization($count){  
        $organization=user()->organization();
        $organization->update([
            'calls_organization'=>$organization->calls_organization+$count,
        ]);
        return redirect()->back()->with('message_cart','Зявки успешно куплены');
    }

    public static function buyAplicationsProductRequestsFromMarketplace($count){ 
        $organization=user()->organization();
        $organization->update([
            'product_requests_from_marketplace'=>$organization->product_requests_from_marketplace+$count,
        ]);
        return redirect()->back()->with('message_cart','Зявки успешно куплены');
    }

    public static function buyAplicationsImprovemenGraves($count){  
        $organization=user()->organization();
        $organization->update([
            'applications_improvemen_graves'=>$organization->applications_improvemen_graves+$count,
        ]);
        return redirect()->back()->with('message_cart','Зявки успешно куплены');
    }

    public static function buyAplicationsMemorial($count){  
        $organization=user()->organization();
        $organization->update([
            'aplications_memorial'=>$organization->aplications_memorial+$count,
        ]);
        return redirect()->back()->with('message_cart','Зявки успешно куплены');
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
        $products=filtersProductsOrganizations($data);
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
                        'title'=>$filename,
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
        $reviews=ReviewsOrganization::orderBy('id','desc')->where('organization_id',$organization->id)->paginate(10);
        return view('account.agency.organization.reviews.reviews-organization',compact('reviews'));
    }

    public static function reviewsProduct(){
        $organization=user()->organization();
        $reviews=CommentProduct::orderBy('id','desc')->where('organization_id',$organization->id)->paginate(10);
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
        $orders=$organization->ordersNew;
        return view('account.agency.organization.product.orders.new',compact('orders'));
    }


    public static function ordersInWork(){
        $organization=user()->organization();
        $orders=$organization->ordersInWork;
        return view('account.agency.organization.product.orders.in-work',compact('orders'));
    }


    public static function ordersCompleted(){
        $organization=user()->organization();
        $orders=$organization->ordersCompleted;
        // $orders=user()->organization()->with('ordersCompleted')->paginate(1);
        return view('account.agency.organization.product.orders.completed',compact('orders'));
    }

    public static function orderComplete($order){
        $order->update(['status'=>2]);
        return redirect()->back()->with('message_cart','Статус успешно обновлен');
    }

    public static function orderAccept($order){
        $organization=user()->organization();
        if($organization->product_requests_from_marketplace>0){
            $order->update(['status'=>1]);
            $organization->update([
                'product_requests_from_marketplace'=>$organization->product_requests_from_marketplace-1,
            ]);
            return redirect()->back()->with('message_cart','Заказ успешно принят');
        }
        return redirect()->back()->with('error','Закончились заявки');

    }
    
}

