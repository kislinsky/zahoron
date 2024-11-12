<?php

namespace App\Http\Controllers;

use App\Models\WordsMemory;
use Illuminate\Http\Request;
use App\Services\WordsMemory\WordsMemoryService;

class WordsMemoryController extends Controller
{
    public static function addWordsMemory(Request $request){
       
        $data=request()->validate([
            'file'=>["nullable","image","max:10240",'mimes:png,jpg,jpeg,bmp,tiff,gif'],
            'product_id'=>['required'],
            'content'=>['required','string'],
        ]);
        return WordsMemoryService::addWordsMemory($data,$request);
    }
}
