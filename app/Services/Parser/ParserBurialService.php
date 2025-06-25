<?php

namespace App\Services\Parser;

use App\Models\Burial;
use App\Models\Cemetery;
use App\Models\City;
use App\Models\Edge;
use App\Models\ImagePersonal;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ParserBurialService
{
    // public static function index($request){
    //     $spreadsheet = new Spreadsheet();
    //     $file = $request->file('file');
    //     $spreadsheet = IOFactory::load($file);
    //     // Получение данных из первого листа
    //     $sheet = $spreadsheet->getActiveSheet();
    //     $burials = array_slice($sheet->toArray(),1);
    //     foreach($burials as $burial){
    //         $city=createCity($burial[2],$burial[1],);
    //         $cemetery=createCemetery($burial[3],$city->title,str_replace(',','.',$burial[9]),str_replace(',','.',$burial[10]));

    //         $status=1;
    //         if($burial[16]!='Готово'){
    //             $status=0;
    //         }
    //         if($city!=null && $cemetery!=null){

    //             $burial_create=Burial::create([
    //                 'surname'=>$burial[11],
    //                 'name'=>$burial[12],
    //                 'patronymic'=>$burial[13],
    //                 'date_death'=>$burial[15],
    //                 'date_birth'=>$burial[14],
    //                 'status'=>$status,
    //                 'href_img'=>1,
    //                 'who'=>'Гражданский',
    //                 'img'=>$burial[7],
    //                 'img_original'=>$burial[8],

    //                 'width'=>str_replace(',','.',$burial[9]),
    //                 'longitude'=>str_replace(',','.',$burial[10]),
    //                 'cemetery_id'=>$cemetery->id ,
    //                 'slug'=>slug("$burial[11]-$burial[12]-$burial[13]-$burial[14]-$burial[15]"),
    //                 'location_death'=>"Россия,$burial[1],$burial[2]",
    //             ]);
                


    //         }
    //     }
    //     return redirect()->back()->with("message_cart", 'Захоронения успешно добавлены');
       
    // }


    public static function index($request) {
    // Валидация входных данных (изменено для множества файлов)

    $validated = $request->validate([
        'files' => 'required|array',
        'files.*' => 'file|mimes:xlsx,xls',
    ]);


    $files = $request->file('files');
    $createdBurials = 0;
    $skippedRows = 0;
    $errors = [];

    foreach ($files as $file) {
        if (!$file->isValid()) {
            $errors[] = "Файл {$file->getClientOriginalName()} не валиден";
            continue;
        }

        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            
            // Получаем заголовки и данные
            $titles = $sheet->toArray()[0];
            $burialsData = array_slice($sheet->toArray(), 1);
            
            // Фильтруем пустые заголовки и создаем маппинг
            $filteredTitles = array_filter($titles, fn($value) => $value !== null);
            $columns = array_flip($filteredTitles);

            // Проверка наличия обязательных колонок
            $requiredColumns = [
                'Регион', 
                'Населённый пункт', 
                'Кладбище',
                'Фамилия',
                'Имя',
                'Дата рождения',
                'Дата смерти',
                'Широта',
                'Долгота'
            ];

            foreach ($requiredColumns as $col) {
                if (!isset($columns[$col])) {
                    $errors[] = "В файле {$file->getClientOriginalName()} отсутствует обязательная колонка: {$col}";
                    continue 2; // Переходим к следующему файлу
                }
            }

            foreach ($burialsData as $rowIndex => $burialRow) {
                try {
                    // Проверяем существование региона, города и кладбища
                    $region = Edge::where('title', $burialRow[$columns['Регион']])->first();
                    if (!$region) {
                        $skippedRows++;
                        continue;
                    }

                    $city = City::where('title', $burialRow[$columns['Населённый пункт']])
                                ->where('edge_id', $region->id)
                                ->first();
                    if (!$city) {
                        $skippedRows++;
                        continue;
                    }

                    $cemetery = Cemetery::where('title', $burialRow[$columns['Кладбище']])
                                        ->where('city_id', $city->id)
                                        ->first();
                    if (!$cemetery) {
                        $skippedRows++;
                        continue;
                    }

                    // Генерируем уникальный slug
                    $slug = self::generateUniqueSlug(
                        $burialRow[$columns['Фамилия']],
                        $burialRow[$columns['Имя']],
                        $burialRow[$columns['Отчество']] ?? null,
                        $burialRow[$columns['Дата рождения']],
                        $burialRow[$columns['Дата смерти']]
                    );

                    // Создаем захоронение
                    $burialData = [
                        'surname' => $burialRow[$columns['Фамилия']],
                        'name' => $burialRow[$columns['Имя']],
                        'patronymic' => $burialRow[$columns['Отчество']] ?? null,
                        'date_death' => $burialRow[$columns['Дата смерти']],
                        'date_birth' => $burialRow[$columns['Дата рождения']],
                        'status' => 1,
                        'href_img' => 1,
                        'url' => $burialRow[$columns['URL']],
                        'who' => 'Гражданский',
                        'img' => $burialRow[$columns['Главное фото']] ?? null,
                        'img_original' => $burialRow[$columns['Главное фото']] ?? null,
                        'width' => str_replace(',', '.', $burialRow[$columns['Широта']]),
                        'longitude' => str_replace(',', '.', $burialRow[$columns['Долгота']]),
                        'cemetery_id' => $cemetery->id,
                        'slug' => $slug,
                        'location_death' => "Россия,{$region->title},{$city->title}",
                    ];
                    
                    $burial=Burial::create($burialData);

                    if (isset($columns['Фотографии']) && !empty($burialRow[$columns['Фотографии']])) {
                        $photos = array_map('trim', explode(',', $burialRow[$columns['Фотографии']]));
                        
                        foreach ($photos as $photo) {
                            if (!empty($photo)) {
                                
                                ImagePersonal::create([
                                    'title' => $photo,
                                    'burial_id' => $burial->id,
                                    'status' => 1,
                                ]);
                            }
                        }
                    }

                    $createdBurials++;

                } catch (\Exception $e) {
                    $skippedRows++;
                    Log::error("Ошибка при импорте строки " . ($rowIndex + 2) . " в файле {$file->getClientOriginalName()}: " . $e->getMessage());
                }
            }

        } catch (\Exception $e) {
            $errors[] = "Ошибка при обработке файла {$file->getClientOriginalName()}: " . $e->getMessage();
            continue; // Переходим к следующему файлу
        }
    }

    $message = "Импорт захоронений завершен. " .
               "Обработано файлов: " . count($files) . ", " .
               "Создано захоронений: $createdBurials, " .
               "Пропущено строк: $skippedRows";

    return redirect()->back()
        ->with("message_cart", $message)
        ->withErrors($errors);
}

protected static function generateUniqueSlug($surname, $name, $patronymic, $birthDate, $deathDate)
{
    $baseSlug = slug("$surname-$name-$patronymic-$birthDate-$deathDate");
    $slug = $baseSlug;
    $count = 1;

    while (Burial::where('slug', $slug)->exists()) {
        $slug = $baseSlug . '-' . $count;
        $count++;
    }

    return $slug;
}

}