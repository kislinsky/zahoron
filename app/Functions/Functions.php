<?php

namespace App\Functions;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class Functions 
{
    public static function addImgMemoryWords($img,$request) {
        $filename    = time().$img->getClientOriginalName();
        $file = $request->file('file');
        $file->move(public_path() . '/uploads_memory_words',$filename);
        return $filename;
        
    }
}