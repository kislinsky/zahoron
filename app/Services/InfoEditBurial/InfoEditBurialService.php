<?php

namespace App\Services\InfoEditBurial;



use App\Models\Burial;
use Illuminate\Http\Request;
use App\Models\ImageMonument;
use App\Models\ImagePersonal;
use App\Models\InfoEditBurial;
use Illuminate\Support\Facades\Auth;



class InfoEditBurialService {
    
    public static function infoBurialEdit($data,$id){
        $burial=Burial::findOrFail($id);
        InfoEditBurial::create([
            'surname' =>$data['surname_editing_burial'],
            'name' =>$data['name_editing_burial'],
            'patronymic' =>$data['patronymic_editing_burial'],
            'date_birth' =>$data['date_birth_editing_burial'],
            'date_death' =>$data['date_death_editing_burial'],
            'who' =>$data['who_editing_burial'],
            'user_id'=>Auth::user()->id,
            'burial_id'=>$burial->id,
        ]);
        return redirect()->back()->with('message_words_memory','Сообщение отправлено на проверку.');
    }

    public static function imagePersonalBurialEdit( $data) {
        foreach($data['file_burials_image_personal'] as $file){
            $filename=generateRandomString().".jpeg";
            $file->storeAs("uploads_burial_personal", $filename, "public");
            ImagePersonal::create([
                'title'=>$filename,
                'burial_id'=>$data['burial_id_image_personal'],
            ]);
        }
        return redirect()->back()->with('message_words_memory','Сообщение отправлено на проверку.');
    }

    public static function imageMonumentBurialEdit( $data) {
        foreach($data['file_burials_image_monument'] as $file){
            $filename=generateRandomString().".jpeg";
            $file->storeAs("uploads_burial_monument", $filename, "public");
            ImageMonument::create([
                'title'=>$filename,
                'burial_id'=>$data['burial_id_image_monument'],
            ]);
        }
        return redirect()->back()->with('message_words_memory','Сообщение отправлено на проверку.');
    }
    
}