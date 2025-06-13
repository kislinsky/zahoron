<?php

namespace App\Services\Parser;

use App\Models\Edge;
use App\Models\ImageChurch;
use App\Models\Church;
use App\Models\WorkingHoursChurch;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ParserChurchService
{

  public static function index($request) {
    // Валидация только файлов и типа импорта
    $validated = $request->validate([
        'files' => 'required|array',
        'files.*' => 'file|mimes:xlsx,xls',
        'import_type' => 'required|in:create,update',
        'columns_to_update' => 'nullable|array',
    ]);

    $files = $request->file('files');
    $importAction = $request->input('import_type', 'create');
    $updateFields = $request->input('columns_to_update', []);
    
    $processedFiles = 0;
    $createdChurches = 0;
    $updatedChurches = 0;
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
            $titles = $sheet->toArray()[0];
            $churchesData = array_slice($sheet->toArray(), 1);
            $filteredTitles = array_filter($titles, fn($value) => $value !== null);
            $columns = array_flip($filteredTitles);

            foreach ($churchesData as $rowIndex => $churchRow) {
                try {
                    // Получаем ID если есть колонка ID
                    $objectId = isset($columns['ID']) ? rtrim($churchRow[$columns['ID']] ?? '', '!') : null;

                    // Для режима update пропускаем если нет ID
                    if ($importAction === 'update' && !$objectId) {
                        $skippedRows++;
                        continue;
                    }

                    // Получаем связанные объекты (регион, район, город)
                    $objects = linkRegionDistrictCity(
                        $churchRow[$columns['Регион'] ?? null] ?? null,
                        $churchRow[$columns['Район'] ?? null] ?? null,
                        $churchRow[$columns['Населённый пункт'] ?? null] ?? null
                    );

                    $area = $objects['district'] ?? null;
                    $city = $objects['city'] ?? null;

                    // Получаем разницу во времени если есть координаты
                    $time_difference = $city->utc_offset ?? null;
                    
                    if ($time_difference == null && env('API_WORK') == 'true' && 
                        isset($columns['Latitude']) && isset($columns['Longitude']) &&
                        !empty($churchRow[$columns['Latitude']]) && !empty($churchRow[$columns['Longitude']])) {
                        $time_difference = differencetHoursTimezone(getTimeByCoordinates(
                            $churchRow[$columns['Latitude']], 
                            $churchRow[$columns['Longitude']]
                        )['timezone']);
                        
                        
                        if ($city) {
                            $city->update(['utc_offset' => $time_difference]);
                        }
                    }

                    if($time_difference==null){
                        $time_difference=0;
                    }             

                    // Формируем данные для церкви
                    $churchData = [
                        'title' => $churchRow[$columns['Название организации'] ?? null] ?? null,
                        'address' => $churchRow[$columns['Адрес'] ?? null] ?? null,
                        'latitude' => $churchRow[$columns['Latitude'] ?? null] ?? null,
                        'rating' => $churchRow[$columns['Рейтинг'] ?? null] ?? null,
                        'longitude' => $churchRow[$columns['Longitude'] ?? null] ?? null,
                        'city_id' => $city->id ?? null,
                        'phone' => normalizePhone($churchRow[$columns['Телефоны'] ?? null] ?? null),
                        'content' => $churchRow[$columns['SEO Описание'] ?? null] ?? ($churchRow[$columns['Описание'] ?? null] ?? null),
                        'img_url' => $churchRow[$columns['Логотип'] ?? null] ?? 'default',
                        'href_img' => 1,
                        'two_gis_link' => $churchRow[$columns['URL'] ?? null] ?? null,
                        'time_difference' => $time_difference,
                        'url_site' => $churchRow[$columns['Сайт'] ?? null] ?? null,
                    ];
                    // Обработка логотипа
                    if (isset($columns['Логотип']) && $churchRow[$columns['Логотип']] != 'default') {
                        if ($churchRow[$columns['Логотип']] != null && !isBrokenLink($churchRow[$columns['Логотип']])) {
                            $churchData['img_url'] = $churchRow[$columns['Логотип']];
                        } else {
                            $churchData['img_url'] = 'default';
                        }
                    }

                    if ($importAction === 'create') {
                        
                        // Для создания - если нет ID, пропускаем (или можно генерировать, если нужно)
                        if (!$objectId) {
                            $skippedRows++;
                            continue;
                        }

                        // Проверяем, существует ли уже запись с таким ID
                        if (Church::find($objectId)) {
                            $skippedRows++;
                            continue;
                        }

                        // Создаем новую запись
                        $churchData['id'] = $objectId;
                        $church = Church::create($churchData);
                        $createdChurches++;

                        // Обработка режима работы
                        if (isset($columns['Режим работы']) && !empty($churchRow[$columns['Режим работы']])) {
                            $workHours = $churchRow[$columns['Режим работы']];
                            $days = parseWorkingHours($workHours);
                            
                            foreach ($days as $day) {
                                $holiday = ($day['time_start_work'] == 'Выходной') ? 1 : 0;
                                WorkingHoursChurch::create([
                                    'day' => $day['day'],
                                    'time_start_work' => $day['time_start_work'],
                                    'time_end_work' => $day['time_end_work'],
                                    'holiday' => $holiday,
                                    'church_id' => $church->id,
                                ]);
                            }
                        }

                        // Обработка фотографий
                        if (isset($columns['Фотографии']) && !empty($churchRow[$columns['Фотографии']])) {
                            $urls_array = explode(', ', $churchRow[$columns['Фотографии']]);
                            foreach ($urls_array as $img) {
                                if ($img != null && !isBrokenLink($img)) {
                                    ImageChurch::create([
                                        'img_url' => $img,
                                        'href_img' => 1,
                                        'church_id' => $church->id,
                                    ]);
                                }
                            }
                        }
                    } elseif ($importAction === 'update') {
                        // Для обновления - находим существующую запись
                        $church = Church::find($objectId);
           
                        if ($church) {
                            // Обновляем только указанные поля
                            $dataToUpdate = [];
                            foreach ($updateFields as $field) {
                                if (array_key_exists($field, $churchData) && !is_null($churchData[$field])) {
                                    $dataToUpdate[$field] = $churchData[$field];
                                }
                            }

                            if (!empty($dataToUpdate)) {
                                $church->update($dataToUpdate);
                                $updatedChurches++;
                            }

                            // Обработка режима работы при обновлении
                            if (in_array('working_hours', $updateFields) && isset($columns['Режим работы']) && !empty($churchRow[$columns['Режим работы']])) {
                                WorkingHoursChurch::where('church_id', $church->id)->delete();
                                
                                $workHours = $churchRow[$columns['Режим работы']];
                                $days = parseWorkingHours($workHours);
                                
                                foreach ($days as $day) {
                                    $holiday = ($day['time_start_work'] == 'Выходной') ? 1 : 0;
                                    WorkingHoursChurch::create([
                                        'day' => $day['day'],
                                        'time_start_work' => $day['time_start_work'],
                                        'time_end_work' => $day['time_end_work'],
                                        'holiday' => $holiday,
                                        'church_id' => $church->id,
                                    ]);
                                }
                            }

                            // Обработка фотографий при обновлении
                            if (in_array('galerey', $updateFields) && isset($columns['Фотографии']) && !empty($churchRow[$columns['Фотографии']])) {
                                ImageChurch::where('church_id', $church->id)->delete();
                                
                                $urls_array = explode(', ', $churchRow[$columns['Фотографии']]);
                                foreach ($urls_array as $img) {
                                    if ($img != null && !isBrokenLink($img)) {
                                        ImageChurch::create([
                                            'img_url' => $img,
                                            'href_img' => 1,
                                            'church_id' => $church->id,
                                        ]);
                                    }
                                }
                            }
                        } else {
                            $skippedRows++;
                        }
                    }
                } catch (\Exception $e) {
                    $skippedRows++;
                    $errors[] = "Ошибка в строке " . ($rowIndex + 2) . ": " . $e->getMessage();
                }
            }
            
            $processedFiles++;
        } catch (\Exception $e) {
            $errors[] = "Ошибка при обработке файла {$file->getClientOriginalName()}: " . $e->getMessage();
        }
    }

    $message = "Импорт церквей завершен. " .
               "Файлов обработано: $processedFiles, " .
               "Создано церквей: $createdChurches, " .
               "Обновлено церквей: $updatedChurches, " .
               "Пропущено строк: $skippedRows";

    return redirect()->back()
        ->with("message_cart", $message)
        ->withErrors($errors);
  }
}