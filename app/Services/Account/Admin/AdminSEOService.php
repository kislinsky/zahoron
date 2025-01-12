<?php

namespace App\Services\Account\Admin;

use App\Models\SEO;


class AdminSEOService {

    public static function object($page){
       $object_columns=SEO::where('page',$page)->get();
       return view('account.admin.seo.update',compact('object_columns'));
    }

    public static function settings(){
        return view('account.admin.seo.settings');
    }

    public static function updateSeo($data,$page){
        $content = SEO::where('page',$page)->where('name','description')->first();
        $title = SEO::where('page',$page)->where('name','title')->first();
        $title_h1 = SEO::where('page',$page)->where('name','h1')->first();
        $content->update([
            'content'=>$data['description']
        ]);
        $title->update([
            'content'=>$data['title']
        ]);
        $title_h1->update([
            'content'=>$data['h1']
        ]);
        return redirect()->back()->with('message_cart','Поля успешно обновлены');
    }
}