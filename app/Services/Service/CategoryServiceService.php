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
        $cemetery=Cemetery::findOrFail($service->cemetery_id);
        $city=City::findOrFail($cemetery->city_id);
        $edge=Edge::findOrFail($city->edge_id);
        $imgs_service=ImageService::where('service_id',$service->id)->get();
        $stages_service=StageService::orderBy('id','asc')->where('service_id',$service->id)->get();
        return view('service.single.single-one-time-cleaning',compact('imgs_service','stages_service','service','cemetery','edge','city'));
    }

    public static function servicePaintingFence($service){
        $cemetery=Cemetery::findOrFail($service->cemetery_id);
        $city=City::findOrFail($cemetery->city_id);
        $edge=Edge::findOrFail($city->edge_id);
        $reviews=ServiceReviews::orderBy('id','asc')->where('service_id',$service->id)->get();
        $imgs_service=ImageService::where('service_id',$service->id)->get();
        $stages_service=StageService::orderBy('id','asc')->where('service_id',$service->id)->get();
        $faqs=FaqService::orderBy('id','desc')->where('service_id',$service->id)->get();
        return view('service.single.single-painting-fence',compact('imgs_service','reviews','stages_service','service','faqs','cemetery','edge','city'));
    }

    public static function serviceDepartureBrigadeCalculation($service){
        
        $cemetery=Cemetery::findOrFail($service->cemetery_id);
        $city=City::findOrFail($cemetery->city_id);
        $edge=Edge::findOrFail($city->edge_id);
        $reviews=ServiceReviews::orderBy('id','asc')->where('service_id',$service->id)->get();
        $imgs_service=ImageService::where('service_id',$service->id)->get();
        $stages_service=StageService::orderBy('id','asc')->where('service_id',$service->id)->get();
        $faqs=FaqService::orderBy('id','desc')->where('service_id',$service->id)->get();
        return view('service.single.single-departure-brigade-calculation',compact('imgs_service','reviews','stages_service','service','faqs','cemetery','edge','city'));
    }

    public static function serviceLayingFlowers($service){
        $cemetery=Cemetery::findOrFail($service->cemetery_id);
        $city=City::findOrFail($cemetery->city_id);
        $edge=Edge::findOrFail($city->edge_id);
        $reviews=ServiceReviews::orderBy('id','asc')->where('service_id',$service->id)->get();
        $imgs_service=ImageService::where('service_id',$service->id)->get();
        $stages_service=StageService::orderBy('id','asc')->where('service_id',$service->id)->get();
        $faqs=FaqService::orderBy('id','desc')->where('service_id',$service->id)->get();
        return view('service.single.single-laying-flowers',compact('imgs_service','reviews','stages_service','service','faqs','cemetery','edge','city'));
    }
}