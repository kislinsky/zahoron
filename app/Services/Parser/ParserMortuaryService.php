<?php

namespace App\Services\Parser;

use App\Models\Cemetery;
use App\Models\City;

use App\Models\Edge;
use App\Models\ImageCemetery;
use App\Models\ImageMortuary;
use App\Models\Mortuary;
use App\Models\ReviewMortuary;
use App\Models\WorkingHoursMortuary;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ParserMortuaryService
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
    $createdMortuaries = 0;
    $updatedMortuaries = 0;
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
            $mortuariesData = array_slice($sheet->toArray(), 1);
            $filteredTitles = array_filter($titles, fn($value) => $value !== null);
            $columns = array_flip($filteredTitles);

            foreach ($mortuariesData as $rowIndex => $mortuaryRow) {
                try {
                    // Получаем ID если есть колонка ID
                    $objectId = isset($columns['ID']) ? rtrim($mortuaryRow[$columns['ID']] ?? '', '!') : null;

                    // Для режима update пропускаем если нет ID
                    if ($importAction === 'update' && !$objectId) {
                        $skippedRows++;
                        continue;
                    }

                    // Получаем связанные объекты (регион, район, город)
                    $objects = linkRegionDistrictCity(
                        $mortuaryRow[$columns['Регион'] ?? null] ?? null,
                        $mortuaryRow[$columns['Район'] ?? null] ?? null,
                        $mortuaryRow[$columns['Населённый пункт'] ?? null] ?? null
                    );
                    
                    $area = $objects['district'] ?? null;
                    $city = $objects['city'] ?? null;

                    // Получаем разницу во времени если есть координаты
                    $time_difference = $city->utc_offset ?? null;
                    if ($time_difference == null && env('API_WORK') == 'true' && 
                        isset($columns['Latitude']) && isset($columns['Longitude']) &&
                        !empty($mortuaryRow[$columns['Latitude']]) && !empty($mortuaryRow[$columns['Longitude']])) {
                        $time_difference = differencetHoursTimezone(getTimeByCoordinates(
                            $mortuaryRow[$columns['Latitude']], 
                            $mortuaryRow[$columns['Longitude']]
                        )['timezone']);
                        
                        if ($city) {
                            $city->update(['utc_offset' => $time_difference]);
                        }
                         if($time_difference==null){
                                $time_difference=0;
                            }
                    }

                    if($time_difference==null){
                        $time_difference=0;
                    }   
                    // Формируем данные для морга
                    $mortuaryData = [
                        'title' => $mortuaryRow[$columns['Название организации'] ?? null] ?? null,
                        'adres' => $mortuaryRow[$columns['Адрес'] ?? null] ?? null,
                        'width' => $mortuaryRow[$columns['Latitude'] ?? null] ?? null,
                        'rating' => $mortuaryRow[$columns['Рейтинг'] ?? null] ?? null,
                        'longitude' => $mortuaryRow[$columns['Longitude'] ?? null] ?? null,
                        'city_id' => $city->id ?? null,
                        'phone' => normalizePhone($mortuaryRow[$columns['Телефоны'] ?? null] ?? null),
                        'content' => $mortuaryRow[$columns['SEO Описание'] ?? null] ?? ($mortuaryRow[$columns['Описание'] ?? null] ?? null),
                        'img_url' => $mortuaryRow[$columns['Логотип'] ?? null] ?? 'default',
                        'href_img' => 1,
                        'two_gis_link' => $mortuaryRow[$columns['URL'] ?? null] ?? null,
                        'time_difference' => $time_difference,
                        'url_site' => $mortuaryRow[$columns['Сайт'] ?? null] ?? null,
                    ];

                    // Обработка логотипа
                    if (isset($columns['Логотип']) && $mortuaryRow[$columns['Логотип']] != 'default') {
                        if ($mortuaryRow[$columns['Логотип']] != null && !isBrokenLink($mortuaryRow[$columns['Логотип']])) {
                            $mortuaryData['img_url'] = $mortuaryRow[$columns['Логотип']];
                        } else {
                            $mortuaryData['img_url'] = 'default';
                        }
                    }

                    if ($importAction === 'create') {
                        // Для создания - если нет ID, пропускаем (или можно генерировать, если нужно)
                        if (!$objectId) {
                            $skippedRows++;
                            continue;
                        }

                        // Проверяем, существует ли уже запись с таким ID
                        if (Mortuary::find($objectId)) {
                            $skippedRows++;
                            continue;
                        }

                        // Создаем новую запись
                        $mortuaryData['id'] = $objectId;
                        $mortuary = Mortuary::create($mortuaryData);
                        $createdMortuaries++;

                        // Обработка режима работы
                        if (isset($columns['Режим работы']) && !empty($mortuaryRow[$columns['Режим работы']])) {
                            $workHours = $mortuaryRow[$columns['Режим работы']];
                            $days = parseWorkingHours($workHours);
                            
                            foreach ($days as $day) {
                                $holiday = ($day['time_start_work'] == 'Выходной') ? 1 : 0;
                                WorkingHoursMortuary::create([
                                    'day' => $day['day'],
                                    'time_start_work' => $day['time_start_work'],
                                    'time_end_work' => $day['time_end_work'],
                                    'holiday' => $holiday,
                                    'mortuary_id' => $mortuary->id,
                                ]);
                            }
                        }

                        // Обработка фотографий
                        if (isset($columns['Фотографии']) && !empty($mortuaryRow[$columns['Фотографии']])) {
                            $urls_array = explode(', ', $mortuaryRow[$columns['Фотографии']]);
                            foreach ($urls_array as $img) {
                                if ($img != null && !isBrokenLink($img)) {
                                    ImageMortuary::create([
                                        'img_url' => $img,
                                        'href_img' => 1,
                                        'mortuary_id' => $mortuary->id,
                                    ]);
                                }
                            }
                        }
                    } elseif ($importAction === 'update') {
                        // Для обновления - находим существующую запись
                        $mortuary = Mortuary::find($objectId);
           
                        if ($mortuary) {
                            // Обновляем только указанные поля
                            $dataToUpdate = [];
                            foreach ($updateFields as $field) {
                                if (array_key_exists($field, $mortuaryData) && !is_null($mortuaryData[$field])) {
                                    $dataToUpdate[$field] = $mortuaryData[$field];
                                }
                            }

                            if (!empty($dataToUpdate)) {
                                $mortuary->update($dataToUpdate);
                                $updatedMortuaries++;
                            }

                            // Обработка режима работы при обновлении
                            if (in_array('working_hours', $updateFields) && isset($columns['Режим работы']) && !empty($mortuaryRow[$columns['Режим работы']])) {
                                WorkingHoursMortuary::where('mortuary_id', $mortuary->id)->delete();
                                
                                $workHours = $mortuaryRow[$columns['Режим работы']];
                                $days = parseWorkingHours($workHours);
                                
                                foreach ($days as $day) {
                                    $holiday = ($day['time_start_work'] == 'Выходной') ? 1 : 0;
                                    WorkingHoursMortuary::create([
                                        'day' => $day['day'],
                                        'time_start_work' => $day['time_start_work'],
                                        'time_end_work' => $day['time_end_work'],
                                        'holiday' => $holiday,
                                        'mortuary_id' => $mortuary->id,
                                    ]);
                                }
                            }

                            // Обработка фотографий при обновлении
                            if (in_array('galerey', $updateFields) && isset($columns['Фотографии']) && !empty($mortuaryRow[$columns['Фотографии']])) {
                                ImageMortuary::where('mortuary_id', $mortuary->id)->delete();
                                
                                $urls_array = explode(', ', $mortuaryRow[$columns['Фотографии']]);
                                foreach ($urls_array as $img) {
                                    if ($img != null && !isBrokenLink($img)) {
                                        ImageMortuary::create([
                                            'img_url' => $img,
                                            'href_img' => 1,
                                            'mortuary_id' => $mortuary->id,
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

    $message = "Импорт моргов завершен. " .
               "Файлов обработано: $processedFiles, " .
               "Создано моргов: $createdMortuaries, " .
               "Обновлено моргов: $updatedMortuaries, " .
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
        
        // Получаем заголовки из первой строки
        $headers = $sheet->rangeToArray('A1:' . $sheet->getHighestColumn() . '1')[0];
        $headers = array_map('strtolower', $headers);
        
        // Определяем индексы колонок по заголовкам
        $columnIndexes = [
            'mortuary_id' => array_search('id', $headers),
            'name' => array_search('Имя', $headers),
                'date' => array_search('Дата', $headers),
                'rating' => array_search('Оценка', $headers),
                'content' => array_search('Отзыв', $headers),
        ];
        
        // Проверяем, что все необходимые колонки найдены
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
                // Проверяем, что строка не пустая
                if (empty(array_filter($review))) {
                    $skippedReviews++;
                    continue;
                }
                
                // Получаем значения по индексам колонок
                $mortuaryId = $review[$columnIndexes['mortuary_id']] ?? null;
                $reviewerName = $review[$columnIndexes['name']] ?? null;
                $reviewDate = $review[$columnIndexes['date']] ?? null;
                $rating = $review[$columnIndexes['rating']] ?? null;
                $content = $review[$columnIndexes['content']] ?? null;
                
                // Проверка обязательных полей
                if (empty($mortuaryId)) {
                    $errors[] = "Строка {$rowNumber}: Не указан ID морга";
                    $skippedReviews++;
                    continue;
                }
                
                $mortuary = Mortuary::find($mortuaryId);
                if (!$mortuary) {
                    $errors[] = "Строка {$rowNumber}: Морг с ID {$mortuaryId} не найден";
                    $skippedReviews++;
                    continue;
                }
                
                // Проверка что у морга есть город
                if (!$mortuary->city) {
                    $errors[] = "Строка {$rowNumber}: У морга не указан город";
                    $skippedReviews++;
                    continue;
                }
                
                // Проверка рейтинга
                if (!is_numeric($rating) || $rating < 1 || $rating > 5) {
                    $errors[] = "Строка {$rowNumber}: Рейтинг должен быть числом от 1 до 5";
                    $skippedReviews++;
                    continue;
                }
                // Проверка и преобразование даты
                if (!empty($reviewDate)) {
                    // Удаляем возможные лишние пробелы и слово "отредактирован" если есть
                    $reviewDate = trim(preg_replace('/отредактирован/ui', '', $reviewDate));
                    
                    // Список русских названий месяцев
                    $russianMonths = [
                        'января' => '01', 'февраля' => '02', 'марта' => '03',
                        'апреля' => '04', 'мая' => '05', 'июня' => '06',
                        'июля' => '07', 'августа' => '08', 'сентября' => '09',
                        'октября' => '10', 'ноября' => '11', 'декабря' => '12'
                    ];
                    
                    // Проверяем русский формат "28 июля 2019"
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
                    // Пробуем другие форматы через strtotime
                    elseif (($timestamp = strtotime($reviewDate)) !== false) {
                        $reviewDate = date('Y-m-d', $timestamp);
                    } else {
                        $errors[] = "Строка {$rowNumber}: Не удалось распознать дату '{$reviewDate}'";
                        $skippedReviews++;
                        continue;
                    }
                } else {
                    $reviewDate = now()->format('Y-m-d'); // Если дата не указана, используем текущую
                }
                
                // Создаем отзыв
                ReviewMortuary::create([
                    'name' => $reviewerName,
                    'rating' => $rating,
                    'content' => $content,
                    'created_at' => !empty($reviewDate) ? $reviewDate : now(),
                    'mortuary_id' => $mortuary->id,
                    'status' => 1,
                    'city_id' => $mortuary->city->id,
                ]);
                
                $addedReviews++;
                
            } catch (\Exception $e) {
                $errors[] = "Строка {$rowNumber}: Ошибка обработки - " . $e->getMessage();
                $skippedReviews++;
                continue;
            }
        }
        
        $message = "Импорт отзывов завершен. " .
                "Добавлено отзывов: {$addedReviews}, " .
                "Пропущено: {$skippedReviews}";
        
        if (!empty($errors)) {
            $message .= "<br><br>Ошибки:<br>" . implode("<br>", array_slice($errors, 0, 10));
            if (count($errors) > 10) {
                $message .= "<br>... и ещё " . (count($errors) - 10) . " ошибок";
            }
        }
        
        return redirect()->back()
            ->with("message_cart", $message);
    }
}