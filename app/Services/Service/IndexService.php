<?php

namespace App\Services\Service;


use App\Models\News;
use App\Models\Product;
use App\Models\Service;
use App\Services\Service\CategoryServiceService;




class IndexService {  
    public static function single($id){

        $service=Service::findOrFail($id);

        if($service->category_id==11){
            return CategoryServiceService::serviceOneTimeCleaning($service);
        }
        if($service->category_id==12){
            return CategoryServiceService::servicePaintingFence($service);
        }
        if($service->category_id==13){
            return CategoryServiceService::serviceDepartureBrigadeCalculation($service);
        }
        if($service->category_id==14){
            return CategoryServiceService::serviceLayingFlowers($service);
        }
     
    }
}