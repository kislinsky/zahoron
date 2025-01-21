<?php

namespace App\Services\WordsMemory;


use App\Models\News;
use App\Models\Product;
use App\Models\Service;
use App\Models\WordsMemory;
use App\Functions\Functions;
use Illuminate\Http\Request;



class WordsMemoryService {

    public static function addWordsMemory($data,$request){
        if(isset($data['file'])){
            $filename=generateRandomString().".jpeg";
            $data['file']->storeAs("uploads_memory_words", $filename, "public");
            WordsMemory::create([
                'img'=>$filename,
                'burial_id'=>$data['product_id'],
                'content'=>$data['content'],
            ]);
            $message='сообщение отправлено модератору';
            return redirect()->back()->with("message_words_memory", $message);
        }
        WordsMemory::create([
            'burial_id'=>$data['product_id'],
            'content'=>$data['content'],
        ]);
        $message='сообщение отправлено модератору';
        return redirect()->back()->with("message_words_memory", $message);
        
    }
    


}