<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Services\Service\IndexService;
use App\Services\ProductPriceList\ProductPriceListService;

class ServiceController extends Controller
{
   public static function single($slug){
        return IndexService::single($slug);
   }
}
