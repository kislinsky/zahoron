<?php

namespace App\Http\Controllers;

use App\Models\CategoryService;
use App\Services\CategoryProduct\CategoryProductService;
use Illuminate\Http\Request;

class CategoryProductController extends Controller
{
    public static function ajaxCategoryChildrenUl(Request $request){
        $data=request()->validate([
            'cat_id'=>["required"],
        ]);
        return CategoryProductService::ajaxCategoryChildrenUl($data['cat_id']);
    }

    public static function ajaxCategoryChildrenUlForFilter(Request $request){
        $data=request()->validate([
            'category_id'=>["required"],
        ]);
        return CategoryProductService::ajaxCategoryChildrenUlForFilter($data['category_id']);
    }
    
}
