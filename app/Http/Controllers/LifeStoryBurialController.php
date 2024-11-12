<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\LifeStory\LifeStoryService;

class LifeStoryBurialController extends Controller
{
    public static function lifeStoryAdd(Request $request,$id) {
        $data=request()->validate([
            'content_life_story'=>['required','string'],
        ]);
        return LifeStoryService::lifeStoryAdd($data,$id);
    }
}
