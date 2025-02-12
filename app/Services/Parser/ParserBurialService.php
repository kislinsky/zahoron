<?php

namespace App\Services\Parser;

use App\Models\Burial;
use App\Models\Cemetery;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ParserBurialService
{
    public static function index($request){
        $spreadsheet = new Spreadsheet();
        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file);
        // Получение данных из первого листа
        $sheet = $spreadsheet->getActiveSheet();
        $burials = array_slice($sheet->toArray(),1);
        foreach($burials as $burial){
            $city=createCity($burial[2],$burial[1],);
            $cemetery=createCemetery($burial[3],$city->title,str_replace(',','.',$burial[9]),str_replace(',','.',$burial[10]));

            $status=1;
            if($burial[16]!='Готово'){
                $status=0;
            }
            if($city!=null && $cemetery!=null){

                $burial_create=Burial::create([
                    'surname'=>$burial[11],
                    'name'=>$burial[12],
                    'patronymic'=>$burial[13],
                    'date_death'=>$burial[15],
                    'date_birth'=>$burial[14],
                    'status'=>$status,
                    'href_img'=>1,
                    'who'=>'Гражданский',
                    'img'=>$burial[7],
                    'img_original'=>$burial[8],

                    'width'=>str_replace(',','.',$burial[9]),
                    'longitude'=>str_replace(',','.',$burial[10]),
                    'cemetery_id'=>$cemetery->id ,
                    'slug'=>slug("$burial[11]-$burial[12]-$burial[13]-$burial[14]-$burial[15]"),
                    'location_death'=>"Россия,$burial[1],$burial[2]",
                ]);
                


            }
        }
        return redirect()->back()->with("message_cart", 'Захоронения успешно добавлены');
       
    }


}