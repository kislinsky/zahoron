<?php

namespace App\Http\Controllers;

    use App\Models\Service;
    use App\Services\ProductPriceList\ProductPriceListService;
    use Illuminate\Http\Request;
    use App\Services\Service\IndexService;

class ProductPriceListController extends Controller
{
    public static function priceList(){
        return ProductPriceListService::priceList();
    }

    public static function serviceCategory($slug){
        return ProductPriceListService::serviceCategory($slug);
    }

    public static function singleProduct($slug){
        return ProductPriceListService::singleProduct($slug);
    }
    
    public static function ajaxProducts(Request $request){
        $data=request()->validate([
            'city_id'=>['required'],
        ]);
        return ProductPriceListService::ajaxProducts($data['city_id']);
    }
}
