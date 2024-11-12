<?php

namespace App\Services\CategoryProduct;

use App\Models\CategoryProduct;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



class CategoryProductService {
    public static function ajaxCategoryChildrenUl($id){
        $cats=CategoryProduct::orderBy('id','desc')->where('parent_id',$id)->get();
        return view('components.category.children-product',compact('cats'));
    }
    
}