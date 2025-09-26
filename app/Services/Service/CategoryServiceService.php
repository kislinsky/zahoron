<?php

namespace App\Services\Service;


use App\Models\Acf;
use App\Models\City;
use App\Models\Edge;
use App\Models\Page;
use App\Models\Product;
use App\Models\Cemetery;
use App\Models\ImageService;
use App\Models\OrderProduct;
use App\Models\StageService;
use App\Models\AdditionProduct;
use App\Models\CategoryProduct;
use App\Models\FaqCategoryProduct;
use App\Models\FaqService;
use App\Models\ServiceReviews;

class CategoryServiceService {  
    
    public static function serviceOneTimeCleaning($service){
        $city=selectCity();
        $edge=selectCity()->edge;
                $cemetery=Cemetery::find($service->cemetery_id);

        $imgs_service=ImageService::where('service_id',$service->id)->get();
        $stages_service=StageService::orderBy('id','asc')->where('product_price_list_id',$service->id)->get();
        return view('service.single.single-one-time-cleaning',compact('imgs_service','stages_service','service','edge','city'));
    }

    public static function servicePaintingFence($service){
        $city=selectCity();
        $edge=selectCity()->edge;
        $reviews=ServiceReviews::orderBy('id','asc')->where('service_id',$service->id)->get();
        $imgs_service=ImageService::where('service_id',$service->id)->get();
        $stages_service=StageService::orderBy('id','asc')->where('product_price_list_id',$service->id)->get();
        $cemetery=Cemetery::find($service->cemetery_id);
        $faqs=FaqService::orderBy('id','desc')->where('service_id',$service->id)->get();
        return view('service.single.single-painting-fence',compact('imgs_service','reviews','stages_service','service','faqs','cemetery','edge','city'));
    }

    public static function serviceDepartureBrigadeCalculation($service){
        $city=selectCity();
        $edge=selectCity()->edge;
                $cemetery=Cemetery::find($service->cemetery_id);

        $reviews=ServiceReviews::orderBy('id','asc')->where('service_id',$service->id)->get();
        $imgs_service=ImageService::where('service_id',$service->id)->get();
        $stages_service=StageService::orderBy('id','asc')->where('product_price_list_id',$service->id)->get();
        $faqs=FaqService::orderBy('id','desc')->where('service_id',$service->id)->get();
        return view('service.single.single-departure-brigade-calculation',compact('imgs_service','reviews','stages_service','service','faqs','cemetery','edge','city'));
    }

    public static function serviceLayingFlowers($service){
        $city=selectCity();
        $edge=selectCity()->edge;
        $cemetery=Cemetery::find($service->cemetery_id);
        $reviews=ServiceReviews::orderBy('id','asc')->where('service_id',$service->id)->get();
        $imgs_service=ImageService::where('service_id',$service->id)->get();
        $stages_service=StageService::orderBy('id','asc')->where('product_price_list_id',$service->id)->get();
        $faqs=FaqService::orderBy('id','desc')->where('service_id',$service->id)->get();
        return view('service.single.single-laying-flowers',compact('imgs_service','reviews','stages_service','service','faqs','cemetery','edge','city'));
    }
}