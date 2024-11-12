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
}
