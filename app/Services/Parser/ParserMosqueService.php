<?php

namespace App\Services\Parser;

use App\Models\Edge;
use App\Models\ImageMosque;
use App\Models\Mosque;
use App\Models\ReviewMosque;
use App\Models\WorkingHoursMosque;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ParserMosqueService
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
        $createdMosques = 0;
        $updatedMosques = 0;
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
                $mosquesData = array_slice($sheet->toArray(), 1);
                $filteredTitles = array_filter($titles, fn($value) => $value !== null);
                $columns = array_flip($filteredTitles);

                foreach ($mosquesData as $rowIndex => $mosqueRow) {
                    try {
                        // Получаем ID если есть колонка ID
                        $objectId = isset($columns['ID']) ? rtrim($mosqueRow[$columns['ID']] ?? '', '!') : null;

                        // Для режима update пропускаем если нет ID
                        if ($importAction === 'update' && !$objectId) {
                            $skippedRows++;
                            continue;
                        }

                        // Получаем связанные объекты (регион, район, город)
                        $objects = linkRegionDistrictCity(
                            $mosqueRow[$columns['Регион'] ?? null] ?? null,
                            $mosqueRow[$columns['Район'] ?? null] ?? null,
                            $mosqueRow[$columns['Населённый пункт'] ?? null] ?? null
                        );
                        
                        $area = $objects['district'] ?? null;
                        $city = $objects['city'] ?? null;

                        // Получаем разницу во времени если есть координаты
                        $time_difference = $city->utc_offset ?? null;
                        if ($time_difference == null && env('API_WORK') == 'true' && 
                            isset($columns['Latitude']) && isset($columns['Longitude']) &&
                            !empty($mosqueRow[$columns['Latitude']]) && !empty($mosqueRow[$columns['Longitude']])) {
                            $time_difference = differencetHoursTimezone(getTimeByCoordinates(
                                $mosqueRow[$columns['Latitude']], 
                                $mosqueRow[$columns['Longitude']]
                            )['timezone']);
                            
                            if ($city) {
                                $city->update(['utc_offset' => $time_difference]);
                            }
                            
                        }
                        if($time_difference==null){
                            $time_difference=0;
                        }   
                        // Формируем данные для мечети
                        $mosqueData = [
                            'title' => $mosqueRow[$columns['Название организации'] ?? null] ?? null,
                            'address' => $mosqueRow[$columns['Адрес'] ?? null] ?? null,
                            'latitude' => $mosqueRow[$columns['Latitude'] ?? null] ?? null,
                            'rating' => $mosqueRow[$columns['Рейтинг'] ?? null] ?? null,
                            'longitude' => $mosqueRow[$columns['Longitude'] ?? null] ?? null,
                            'city_id' => $city->id ?? null,
                            'phone' => normalizePhone($mosqueRow[$columns['Телефоны'] ?? null] ?? null),
                            'content' => $mosqueRow[$columns['SEO Описание'] ?? null] ?? ($mosqueRow[$columns['Описание'] ?? null] ?? null),
                            'img_url' => $mosqueRow[$columns['Логотип'] ?? null] ?? 'default',
                            'href_img' => 1,
                            'two_gis_link' => $mosqueRow[$columns['URL'] ?? null] ?? null,
                            'time_difference' => $time_difference,
                            'url_site' => $mosqueRow[$columns['Сайт'] ?? null] ?? null,
                        ];

                        
                        // Обработка логотипа
                        if (isset($columns['Логотип']) && $mosqueRow[$columns['Логотип']] != 'default') {
                            if ($mosqueRow[$columns['Логотип']] != null && !isBrokenLink($mosqueRow[$columns['Логотип']])) {
                                $mosqueData['img_url'] = $mosqueRow[$columns['Логотип']];
                            } else {
                                $mosqueData['img_url'] = 'default';
                            }
                        }

                        if ($importAction === 'create') {
                            // Для создания - если нет ID, пропускаем (или можно генерировать, если нужно)
                            if (!$objectId) {
                                $skippedRows++;
                                continue;
                            }

                            // Проверяем, существует ли уже запись с таким ID
                            if (Mosque::find($objectId)) {
                                $skippedRows++;
                                continue;
                            }

                            // Создаем новую запись
                            $mosqueData['id'] = $objectId;
                            $mosque = Mosque::create($mosqueData);
                            $createdMosques++;

                            // Обработка режима работы
                            if (isset($columns['Режим работы']) && !empty($mosqueRow[$columns['Режим работы']])) {
                                $workHours = $mosqueRow[$columns['Режим работы']];
                                $days = parseWorkingHours($workHours);
                                
                                foreach ($days as $day) {
                                    $holiday = ($day['time_start_work'] == 'Выходной') ? 1 : 0;
                                    WorkingHoursMosque::create([
                                        'day' => $day['day'],
                                        'time_start_work' => $day['time_start_work'],
                                        'time_end_work' => $day['time_end_work'],
                                        'holiday' => $holiday,
                                        'mosque_id' => $mosque->id,
                                    ]);
                                }
                            }

                            // Обработка фотографий
                            if (isset($columns['Фотографии']) && !empty($mosqueRow[$columns['Фотографии']])) {
                                $urls_array = explode(', ', $mosqueRow[$columns['Фотографии']]);
                                foreach ($urls_array as $img) {
                                    if ($img != null && !isBrokenLink($img)) {
                                        ImageMosque::create([
                                            'img_url' => $img,
                                            'href_img' => 1,
                                            'mosque_id' => $mosque->id,
                                        ]);
                                    }
                                }
                            }
                        } elseif ($importAction === 'update') {
                            // Для обновления - находим существующую запись
                            $mosque = Mosque::find($objectId);
               
                            if ($mosque) {
                                // Обновляем только указанные поля
                                $dataToUpdate = [];
                                foreach ($updateFields as $field) {
                                    if (array_key_exists($field, $mosqueData) && !is_null($mosqueData[$field])) {
                                        $dataToUpdate[$field] = $mosqueData[$field];
                                    }
                                }

                                if (!empty($dataToUpdate)) {
                                    $mosque->update($dataToUpdate);
                                    $updatedMosques++;
                                }

                                // Обработка режима работы при обновлении
                                if (in_array('working_hours', $updateFields) && isset($columns['Режим работы']) && !empty($mosqueRow[$columns['Режим работы']])) {
                                    WorkingHoursMosque::where('mosque_id', $mosque->id)->delete();
                                    
                                    $workHours = $mosqueRow[$columns['Режим работы']];
                                    $days = parseWorkingHours($workHours);
                                    
                                    foreach ($days as $day) {
                                        $holiday = ($day['time_start_work'] == 'Выходной') ? 1 : 0;
                                        WorkingHoursMosque::create([
                                            'day' => $day['day'],
                                            'time_start_work' => $day['time_start_work'],
                                            'time_end_work' => $day['time_end_work'],
                                            'holiday' => $holiday,
                                            'mosque_id' => $mosque->id,
                                        ]);
                                    }
                                }

                                // Обработка фотографий при обновлении
                                if (in_array('galerey', $updateFields) && isset($columns['Фотографии']) && !empty($mosqueRow[$columns['Фотографии']])) {
                                    ImageMosque::where('mosque_id', $mosque->id)->delete();
                                    
                                    $urls_array = explode(', ', $mosqueRow[$columns['Фотографии']]);
                                    foreach ($urls_array as $img) {
                                        if ($img != null && !isBrokenLink($img)) {
                                            ImageMosque::create([
                                                'img_url' => $img,
                                                'href_img' => 1,
                                                'mosque_id' => $mosque->id,
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

        $message = "Импорт мечетей завершен. " .
                "Файлов обработано: $processedFiles, " .
                "Создано мечетей: $createdMosques, " .
                "Обновлено мечетей: $updatedMosques, " .
                "Пропущено строк: $skippedRows";

        return redirect()->back()
            ->with("message_cart", $message)
            ->withErrors($errors);
    }


    public static function importReviews($request)
{
    $file = $request->file('file_reviews');
    $spreadsheet = IOFactory::load($file);
    $sheet = $spreadsheet->getActiveSheet();
    
    $headers = $sheet->rangeToArray('A1:' . $sheet->getHighestColumn() . '1')[0];
    $headers = array_map('strtolower', $headers);
    
    $columnIndexes = [
        'mosque_id' => array_search('id', $headers),
        'name' => array_search('Имя', $headers),
        'date' => array_search('Дата', $headers),
        'rating' => array_search('Оценка', $headers),
        'content' => array_search('Отзыв', $headers),
    ];
    
    foreach ($columnIndexes as $key => $index) {
        if ($index === false) {
            return redirect()->back()->with("error_cart", "Отсутствует обязательная колонка: " . $key);
        }
    }

    $reviews = array_slice($sheet->toArray(), 1);
    $addedReviews = 0;
    $skippedReviews = 0;
    $errors = [];

    foreach ($reviews as $rowIndex => $review) {
        $rowNumber = $rowIndex + 2;
        
        try {
            if (empty(array_filter($review))) {
                $skippedReviews++;
                continue;
            }
            
            $mosqueId = $review[$columnIndexes['mosque_id']] ?? null;
            $reviewerName = $review[$columnIndexes['name']] ?? null;
            $reviewDate = $review[$columnIndexes['date']] ?? null;
            $rating = $review[$columnIndexes['rating']] ?? null;
            $content = $review[$columnIndexes['content']] ?? null;
            
            if (empty($mosqueId)) {
                $errors[] = "Строка {$rowNumber}: Не указан ID мечети";
                $skippedReviews++;
                continue;
            }
            
            $mosque = Mosque::find($mosqueId);
            if (!$mosque) {
                $errors[] = "Строка {$rowNumber}: Мечеть с ID {$mosqueId} не найдена";
                $skippedReviews++;
                continue;
            }
            
            if (!$mosque->city) {
                $errors[] = "Строка {$rowNumber}: У мечети не указан город";
                $skippedReviews++;
                continue;
            }
            
            if (!is_numeric($rating) || $rating < 1 || $rating > 5) {
                $errors[] = "Строка {$rowNumber}: Рейтинг должен быть числом от 1 до 5";
                $skippedReviews++;
                continue;
            }
            
            if (!empty($reviewDate)) {
                $reviewDate = trim(preg_replace('/отредактирован/ui', '', $reviewDate));
                
                $russianMonths = [
                    'января' => '01', 'февраля' => '02', 'марта' => '03',
                    'апреля' => '04', 'мая' => '05', 'июня' => '06',
                    'июля' => '07', 'августа' => '08', 'сентября' => '09',
                    'октября' => '10', 'ноября' => '11', 'декабря' => '12'
                ];
                
                if (preg_match('/^(\d{1,2})\s+([а-яё]+)\s+(\d{4})$/ui', $reviewDate, $matches)) {
                    $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                    $month = strtolower($matches[2]);
                    $year = $matches[3];
                    
                    if (isset($russianMonths[$month])) {
                        $reviewDate = "{$year}-{$russianMonths[$month]}-{$day}";
                    } else {
                        $errors[] = "Строка {$rowNumber}: Неизвестный месяц '{$matches[2]}' в дате '{$reviewDate}'";
                        $skippedReviews++;
                        continue;
                    }
                } 
                elseif (($timestamp = strtotime($reviewDate)) !== false) {
                    $reviewDate = date('Y-m-d', $timestamp);
                } else {
                    $errors[] = "Строка {$rowNumber}: Не удалось распознать дату '{$reviewDate}'";
                    $skippedReviews++;
                    continue;
                }
            } else {
                $reviewDate = now()->format('Y-m-d');
            }
            
            ReviewMosque::create([
                'name' => $reviewerName,
                'rating' => $rating,
                'content' => $content,
                'created_at' => !empty($reviewDate) ? $reviewDate : now(),
                'mosque_id' => $mosque->id,
                'status' => 1,
                'city_id' => $mosque->city->id,
            ]);
            
            $addedReviews++;
            
        } catch (\Exception $e) {
            $errors[] = "Строка {$rowNumber}: Ошибка обработки - " . $e->getMessage();
            $skippedReviews++;
            continue;
        }
    }
    
    $message = "Импорт отзывов для мечетей завершен. Добавлено: {$addedReviews}, Пропущено: {$skippedReviews}";
    
    if (!empty($errors)) {
        $message .= "<br><br>Ошибки:<br>" . implode("<br>", array_slice($errors, 0, 10));
        if (count($errors) > 10) {
            $message .= "<br>... и ещё " . (count($errors) - 10) . " ошибок";
        }
    }
    
    return redirect()->back()->with("message_cart", $message);
}
}