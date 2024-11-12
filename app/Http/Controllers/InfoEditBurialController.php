<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\InfoEditBurial\InfoEditBurialService;

class InfoEditBurialController extends Controller
{
    public static function infoBurialEdit(Request $request,$id) {
        $data=request()->validate([
            'surname_editing_burial'=>['required','string'],
            'name_editing_burial'=>['required','string'],
            'patronymic_editing_burial'=>['required','string'],
            'date_birth_editing_burial'=>['required','date'],
            'date_death_editing_burial'=>['required','date'],
            'who_editing_burial'=>['required','string'],
        ]);
        return InfoEditBurialService::infoBurialEdit($data,$id);
    }

    public static function imagePersonalBurialEdit(Request $data) {
        $data=request()->validate([
            'burial_id_image_personal'=>['required','integer'],
            'file_burials_image_personal'=>['required'],
        ]);
        return InfoEditBurialService::imagePersonalBurialEdit($data);
    
    }

    public static function imageMonumentBurialEdit(Request $data) {
        $data=request()->validate([
            'burial_id_image_monument'=>['required','integer'],
            'file_burials_image_monument'=>['required'],
        ]);
        return InfoEditBurialService::imageMonumentBurialEdit($data);
    
    }
}
