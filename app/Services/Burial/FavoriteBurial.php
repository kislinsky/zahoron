<?php

namespace App\Services\Burial;


use App\Models\News;
use App\Models\Burial;
use App\Models\Service;
use App\Models\WordsMemory;
use Illuminate\Http\Request;
use App\Models\FavouriteBurial;
use Illuminate\Support\Facades\Auth;



class FavoriteBurial{  
    public static function favoriteAdd($id){
        if(Auth::check()){
            $user=Auth::user();
            $product=Burial::findOrFail($id);
            $favorite=FavouriteBurial::where('burial_id',$product->id)->where('user_id',$user->id)->get();
            if(count($favorite)>0){
                return redirect()->back()->with("error", "Захоронение уже есть в избранное.");
            }
            FavouriteBurial::create([
                'burial_id'=>$product->id,
                'user_id'=>$user->id,
            ]);
            return redirect()->back()->with("message_cart", "Захоронение добавлено в избранное.");
        }return redirect()->back()->with("error", "Зарегстрируйтесь или войдите в аккаунт");
        

    }

    public static function favoriteDelete($id){
        FavouriteBurial::findOrFail($id)->delete();
        return redirect()->back();

    }

}

