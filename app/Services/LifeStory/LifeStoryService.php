<?php

namespace App\Services\LifeStory;


use App\Models\Burial;
use Illuminate\Http\Request;
use App\Models\LifeStoryBurial;
use Illuminate\Support\Facades\Auth;



class LifeStoryService {
    
    public static function lifeStoryAdd($data,$id){
        $burial=Burial::findOrFail($id);
        LifeStoryBurial::create([
            'user_id'=>Auth::user()->id,
            'content'=>$data['content_life_story'],
            'burial_id'=>$burial->id,
        ]);
        return redirect()->back()->with('message_words_memory','Сообщение отправлено на проверку.');
    }
}