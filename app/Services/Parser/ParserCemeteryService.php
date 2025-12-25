<?php

namespace App\Services\Parser;

use App\Models\CallStat;
use App\Models\Cemetery;

use App\Models\City;
use App\Models\Edge;
use App\Models\ImageCemetery;
use App\Models\Organization;
use App\Models\PriceService;
use App\Models\ReviewCemetery;
use App\Models\Service;
use App\Models\WorkingHoursCemetery;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Rap2hpoutre\FastExcel\FastExcel;

class ParserCemeteryService
{

    public static function index($request) {
        // Валидация входных данных
        $validated = $request->validate([
            'files' => 'required|array',
            'files.*' => 'file|mimes:xlsx,xls',
            'price_geo' => 'nullable|numeric',
            'import_type' => 'required|in:create,update',
            'columns_to_update' => 'nullable|array',
        ]);
        $files = $request->file('files');
        $price = $request->input('price_geo');
        $importAction = $request->input('import_type', 'create');
        $updateFields = $request->input('columns_to_update', []);
        
        $processedFiles = 0;
        $createdCemeteries = 0;
        $updatedCemeteries = 0;
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
                $cemeteriesData = array_slice($sheet->toArray(), 1);
                $filteredTitles = array_filter($titles, fn($value) => $value !== null);
                $columns = array_flip($filteredTitles);

                // Проверка наличия обязательных колонок
                $requiredColumns = ['ID 2GIS', 'Название кладбища', ];
                foreach ($requiredColumns as $col) {
                    if (!isset($columns[$col])) {
                        $errors[] = "В файле {$file->getClientOriginalName()} отсутствует обязательная колонка: {$col}";
                        continue 2;
                    }
                }

                foreach ($cemeteriesData as $rowIndex => $cemeteryRow) {
                    try {
                        $objectId = transformId(isset($columns['ID 2GIS']) ? (int)rtrim($cemeteryRow[$columns['ID 2GIS']] ?? '', '!') : null);
                        
                        // Проверка обязательных полей
                        if ($objectId == null) {
                            $skippedRows++;
                            continue;
                        }

                        // Проверка обязательных полей
                        if (empty($cemeteryRow[$columns['Название кладбища']])) {
                            $skippedRows++;
                            continue;
                        }


                        // // Проверка координат
                        // if (empty($cemeteryRow[$columns['Широта']])) {
                        //     $skippedRows++;
                        //     continue;
                        // }
    
                        // if (empty($cemeteryRow[$columns['Долгота']])) {
                        //     $skippedRows++;
                        //     continue;
                        // }

                        $objects = linkRegionDistrictCity(
                            $cemeteryRow[$columns['Регион'] ?? null],
                            $cemeteryRow[$columns['Район'] ?? null],
                            $cemeteryRow[$columns['Населённый пункт'] ?? null]
                        );

                        $area = $objects['district'] ?? null;
                        $city = $objects['city'] ?? null;

                        if (!$city || !$area) {
                            $skippedRows++;
                            continue;
                        }

                        $status = 1;
                        if ($cemeteryRow[$columns['Статус']] == null || $cemeteryRow[$columns['Статус']] != 'Действующая организация') {
                            $status = 0;
                        }                        

                        $time_difference = $city->utc_offset ?? null;
                        if ($time_difference == null && env('API_WORK') == 'true') {
                            $timeData = getTimeByCoordinates($cemeteryRow[$columns['Широта']], $cemeteryRow[$columns['Долгота']]);
                            $time_difference = differencetHoursTimezone($timeData['timezone'] ?? 'UTC');
                            $city->update(['utc_offset' => $time_difference]);
                        }
                        if ($time_difference == null) {
                            $time_difference = 0;
                        }   
                        
                        // Полный набор данных для кладбища
                        $cemeteryData = [
                            'id' => $objectId,
                            'title' => $cemeteryRow[$columns['Название кладбища']],
                            'slug' => Str::slug($cemeteryRow[$columns['Название кладбища']]),
                            'adres' => $cemeteryRow[$columns['Адрес кладбища'] ?? null],
                            'responsible_person_address' => $cemeteryRow[$columns['Адрес (ответственного лица)'] ?? null],
                            'responsible_organization' => $cemeteryRow[$columns['Ответственная организация'] ?? null],
                            'okved' => $cemeteryRow[$columns['Okved'] ?? null],
                            'inn' => (int)($cemeteryRow[$columns['ИНН'] ?? null] ?? 0),
                            'city_id' => $city->id,
                            'area_id' => $area->id,
                            'width' => $cemeteryRow[$columns['Широта']],
                            'longitude' => $cemeteryRow[$columns['Долгота']],
                            'rating' => (float)str_replace(',', '.', $cemeteryRow[$columns['Рейтинг'] ?? 0]),
                            'phone' => normalizePhone($cemeteryRow[$columns['Телефон'] ?? null]),
                            'email' => $cemeteryRow[$columns['Емейл'] ?? null],
                            'img_url' => 'default',
                            'href_img' => 1,
                            'time_difference' => $time_difference,
                            'responsible' => $cemeteryRow[$columns['Ответственное лицо (ФИО)'] ?? null],
                            'cadastral_number' => $cemeteryRow[$columns['Кадастровый номер'] ?? null],
                            'price_burial_location' => $price ?? 5900,
                            'two_gis_link' => $cemeteryRow[$columns['URL'] ?? null],
                            'status' => $status,
                            'date_foundation' => $cemeteryRow[$columns['Дата парсинга'] ?? null],
                            'address_responsible_person' => $cemeteryRow[$columns['Адрес (ответственного лица)'] ?? null],
                            'responsible_person_full_name' => $cemeteryRow[$columns['Ответственное лицо (ФИО)'] ?? null],
                        ];

                        if ($importAction === 'create') {
                            // Проверяем существование по ID или two_gis_link
                            $existing = Cemetery::where('id', $objectId)
                                                ->orWhere('two_gis_link', $objectId)
                                                ->first();
                            if ($existing) {
                                $skippedRows++;
                                continue;
                            }    

                            $cemetery = Cemetery::create($cemeteryData);
                            $createdCemeteries++;

                            // Обработка режима работы при создании
                            if (isset($columns['Режим работы']) && $cemeteryRow[$columns['Режим работы']] != null) {
                                $workHours = $cemeteryRow[$columns['Режим работы']];
                                $days = parseWorkingHours($workHours);
                                
                                foreach ($days as $day) {
                                    $holiday = ($day['time_start_work'] == 'Выходной') ? 1 : 0;
                                    WorkingHoursCemetery::create([
                                        'day' => $day['day'],
                                        'time_start_work' => $day['time_start_work'],
                                        'time_end_work' => $day['time_end_work'],
                                        'holiday' => $holiday,
                                        'cemetery_id' => $cemetery->id,
                                    ]);
                                }
                            }

                            // Обработка фотографий при создании
                            if (isset($columns['Фотографии']) && !empty($cemeteryRow[$columns['Фотографии']])) {
                                $urls_array = explode(', ', $cemeteryRow[$columns['Фотографии']]);
                                foreach ($urls_array as $img) {
                                    if ($img != null) {
                                        ImageCemetery::create([
                                            'img_url' => $img,
                                            'href_img' => 1,
                                            'cemetery_id' => $cemetery->id,
                                        ]);
                                    }
                                }
                            }

                        } elseif ($importAction === 'update') {



                            // Ищем кладбище по two_gis_link или ID
                            $cemetery = Cemetery::where('two_gis_link', $objectId)
                                                ->orWhere('id', $objectId)
                                                ->first();

                            if ($cemetery) {
                                $updateData = [];
                                foreach ($updateFields as $field) {
                                    if (isset($cemeteryData[$field])) {
                                        $updateData[$field] = $cemeteryData[$field];
                                    }
                                }

                                // Обновляем slug если обновляется title
                                if (isset($updateData['title']) && !isset($updateData['slug'])) {
                                    $updateData['slug'] = Str::slug($updateData['title']);
                                }

                                if (!empty($updateData)) {
                                    $cemetery->update($updateData);
                                    $updatedCemeteries++;
                                }

                                // Обработка режима работы при обновлении
                                if (in_array('working_hours', $updateFields) && isset($columns['Режим работы'])) {
                                    $workHours = $cemeteryRow[$columns['Режим работы']] ?? null;
                                    if ($workHours) {
                                        // Удаляем старые записи о рабочем времени
                                        WorkingHoursCemetery::where('cemetery_id', $cemetery->id)->delete();
                                        
                                        // Создаем новые записи
                                        $days = parseWorkingHours($workHours);
                                        foreach ($days as $day) {
                                            $holiday = ($day['time_start_work'] == 'Выходной') ? 1 : 0;
                                            WorkingHoursCemetery::create([
                                                'day' => $day['day'],
                                                'time_start_work' => $day['time_start_work'],
                                                'time_end_work' => $day['time_end_work'],
                                                'holiday' => $holiday,
                                                'cemetery_id' => $cemetery->id,
                                            ]);
                                        }
                                    }
                                }

                                // Обработка фотографий при обновлении
                                if (in_array('galerey', $updateFields) && isset($columns['Фотографии']) && !empty($cemeteryRow[$columns['Фотографии']])) {
                                    ImageCemetery::where('cemetery_id', $cemetery->id)->delete();
                                    
                                    $urls_array = explode(', ', $cemeteryRow[$columns['Фотографии']]);
                                    foreach ($urls_array as $img) {
                                        if ($img != null) {
                                            ImageCemetery::create([
                                                'img_url' => $img,
                                                'href_img' => 1,
                                                'cemetery_id' => $cemetery->id,
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
    
        $message = "Импорт кладбищ завершен. " .
                   "Файлов обработано: $processedFiles, " .
                   "Создано кладбищ: $createdCemeteries, " .
                   "Обновлено кладбищ: $updatedCemeteries, " .
                   "Пропущено строк: $skippedRows";
    
        return redirect()->back()
            ->with("message_cart", $message)
            ->withErrors($errors);
    }


    public static function importReviews($request)
{
    $file = $request->file('file_reviews');
    
    if (!$file) {
        return redirect()->back()->with("error_cart", "Файл не загружен");
    }
    
    $addedCalls = 0;
    $updatedCalls = 0;
    $skippedCalls = 0;
    $errors = [];
    
    // Номер для исключения
    $excludedNumber = '79625582224';
    
    try {
        $handle = fopen($file->getPathname(), 'r');
        
        if ($handle === false) {
            return redirect()->back()->with("error_cart", "Не удалось открыть файл");
        }
        
        // Читаем заголовки
        $headers = fgetcsv($handle, 0, ';');
        
        if ($headers === false) {
            return redirect()->back()->with("error_cart", "Неверный формат CSV файла");
        }
        
        // Нормализуем заголовки
        $headers = array_map(function($header) {
            $header = preg_replace('/^\x{FEFF}/u', '', $header);
            $header = trim($header, " \t\n\r\0\x0B\"'");
            return $header;
        }, $headers);
        
        // Определяем индексы колонок
        $columnIndexes = [
            'call_type' => array_search('Тип обращения', $headers),
            'caller_number' => array_search('Номер клиента', $headers),
            'date_start' => array_search('Время', $headers),
            'duration' => array_search('Длительность', $headers),
            'utm_source' => array_search('Источник (utm)', $headers),
            'utm_medium' => array_search('Канал', $headers),
            'utm_campaign' => array_search('Кампания', $headers),
            'utm_term' => array_search('Ключевое слово', $headers),
            'utm_content' => array_search('Контент', $headers),
            'first_url' => array_search('URL входа', $headers),
            'country_code' => array_search('Страна', $headers),
            'region_code' => array_search('Регион', $headers),
            'is_new' => array_search('Уникальное', $headers),
            'is_quality' => array_search('Качественное', $headers),
            'is_duplicate' => array_search('Подтвержденный лид', $headers),
            'last_group' => array_search('Группа', $headers),
            'city' => array_search('Регион', $headers),
            'url' => array_search('URL входа', $headers),
        ];
        
        $rowNumber = 1;
        
        // Читаем данные построчно
        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            $rowNumber++;
            
            // Пропускаем если массив пустой
            if (empty($row) || count(array_filter($row)) === 0) {
                $skippedCalls++;
                continue;
            }
            
            // Получаем данные с проверкой индексов
            $callType = isset($columnIndexes['call_type']) && isset($row[$columnIndexes['call_type']]) 
                ? trim($row[$columnIndexes['call_type']]) 
                : '';
            
            $callerNumber = isset($columnIndexes['caller_number']) && isset($row[$columnIndexes['caller_number']])
                ? trim($row[$columnIndexes['caller_number']])
                : '';
            
            $dateStart = isset($columnIndexes['date_start']) && isset($row[$columnIndexes['date_start']])
                ? trim($row[$columnIndexes['date_start']])
                : '';
            
            // Пропускаем строки без основных данных
            if (empty($callType) && empty($callerNumber) && empty($dateStart)) {
                $skippedCalls++;
                continue;
            }
            
            // Проверяем обязательные поля
            if (empty($callerNumber)) {
                $errors[] = "Строка {$rowNumber}: Отсутствует номер клиента";
                $skippedCalls++;
                continue;
            }
            
            // ПРОВЕРКА НА ИСКЛЮЧЕННЫЙ НОМЕР
            if ($callerNumber === $excludedNumber) {
                $skippedCalls++;
                continue;
            }
            
            if (empty($dateStart)) {
                $errors[] = "Строка {$rowNumber}: Отсутствует время обращения";
                $skippedCalls++;
                continue;
            }
            
            try {
                // Пробуем разные форматы даты
                $dateStartFormatted = null;
                
                // 1. Формат "dd.mm.yyyy HH:MM"
                $date = \DateTime::createFromFormat('d.m.Y H:i', $dateStart);
                if ($date !== false) {
                    $dateStartFormatted = $date->format('Y-m-d H:i:s');
                } else {
                    // 2. Формат "dd.mm.yyyy"
                    $date = \DateTime::createFromFormat('d.m.Y', $dateStart);
                    if ($date !== false) {
                        $dateStartFormatted = $date->format('Y-m-d H:i:s');
                    } else {
                        // 3. Пробуем стандартный парсер
                        $timestamp = strtotime($dateStart);
                        if ($timestamp !== false) {
                            $dateStartFormatted = date('Y-m-d H:i:s', $timestamp);
                        }
                    }
                }
                
                if (empty($dateStartFormatted)) {
                    $errors[] = "Строка {$rowNumber}: Неверный формат даты '{$dateStart}'";
                    $skippedCalls++;
                    continue;
                }
                
                // Получаем остальные данные
                $duration = isset($columnIndexes['duration']) && isset($row[$columnIndexes['duration']])
                    ? trim($row[$columnIndexes['duration']])
                    : '0';
                
                // Преобразуем длительность
                $durationSeconds = 0;
                if (!empty($duration) && $duration !== '(none)') {
                    if (strpos($duration, ':') !== false) {
                        $parts = explode(':', $duration);
                        $minutes = intval($parts[0] ?? 0);
                        $seconds = intval($parts[1] ?? 0);
                        $durationSeconds = $minutes * 60 + $seconds;
                    } else {
                        $durationSeconds = intval($duration);
                    }
                }
                
                // Получаем URL
                $firstUrl = isset($columnIndexes['first_url']) && isset($row[$columnIndexes['first_url']])
                    ? trim($row[$columnIndexes['first_url']])
                    : '';
                
                // Извлекаем organization_id из URL
                $organizationId = self::extractOrganizationIdFromUrl($firstUrl);
                
                // Остальные поля
                $utmSource = isset($columnIndexes['utm_source']) && isset($row[$columnIndexes['utm_source']])
                    ? trim($row[$columnIndexes['utm_source']])
                    : '';
                
                $utmMedium = isset($columnIndexes['utm_medium']) && isset($row[$columnIndexes['utm_medium']])
                    ? trim($row[$columnIndexes['utm_medium']])
                    : '';
                
                // Булевы значения
                $isNew = isset($columnIndexes['is_new']) && isset($row[$columnIndexes['is_new']])
                    ? trim($row[$columnIndexes['is_new']])
                    : '';
                
                $isQuality = isset($columnIndexes['is_quality']) && isset($row[$columnIndexes['is_quality']])
                    ? trim($row[$columnIndexes['is_quality']])
                    : '';
                
                // Преобразуем
                $isNewBool = stripos($isNew, 'новые') !== false || 
                            stripos($isNew, 'да') !== false || 
                            $isNew === '1';
                
                $isQualityBool = stripos($isQuality, 'качественное') !== false || 
                                stripos($isQuality, 'да') !== false || 
                                $isQuality === '1';
                
                // Генерируем уникальные ID
                // Делаем call_id более уникальным, добавляя микросекунды
                $callId = md5($callerNumber . $dateStartFormatted . $durationSeconds . microtime(true));
                $numberHash = md5($callerNumber);
                
                // Проверяем, существует ли уже запись с таким call_id
                // ИЛИ используем уникальный ключ по комбинации полей
                $existingCall = CallStat::where('caller_number', $callerNumber)
                    ->where('date_start', $dateStartFormatted)
                    ->where('duration', $durationSeconds)
                    ->first();
                
                if ($existingCall) {
                    // Обновляем существующую запись
                    $existingCall->update([
                        'organization_id' => $organizationId,
                        'utm_source' => $utmSource === '(none)' ? null : $utmSource,
                        'utm_medium' => $utmMedium === '(none)' ? null : $utmMedium,
                        'is_new' => $isNewBool,
                        'is_quality' => $isQualityBool,
                        'call_type' => $callType,
                        'webhook_type' => $callType === 'Звонок' ? 'call' : 'web',
                        'url' => $firstUrl,
                        'first_url' => $firstUrl,
                        'updated_at' => now(),
                    ]);
                    
                    $updatedCalls++;
                } else {
                    // Создаем новую запись
                    CallStat::create([
                        'organization_id' => $organizationId,
                        'utm_source' => $utmSource === '(none)' ? null : $utmSource,
                        'utm_medium' => $utmMedium === '(none)' ? null : $utmMedium,
                        'caller_number' => $callerNumber,
                        'date_start' => $dateStartFormatted,
                        'duration' => $durationSeconds,
                        'is_new' => $isNewBool,
                        'is_quality' => $isQualityBool,
                        'call_id' => $callId,
                        'number_hash' => $numberHash,
                        'call_type' => $callType,
                        'webhook_type' => $callType === 'Звонок' ? 'call' : 'web',
                        'url' => $firstUrl,
                        'first_url' => $firstUrl,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    
                    $addedCalls++;
                }
                
            } catch (\Exception $e) {
                $errors[] = "Строка {$rowNumber}: Ошибка обработки - " . $e->getMessage();
                $skippedCalls++;
                continue;
            }
        }
        
        fclose($handle);
        
    } catch (\Exception $e) {
        return redirect()->back()->with("error_cart", "Ошибка при обработке файла: " . $e->getMessage());
    }
    
    $message = "Импорт завершен. Добавлено: {$addedCalls}, Обновлено: {$updatedCalls}, Пропущено: {$skippedCalls}";
    
    if (!empty($errors)) {
        $message .= "<br><br>Первые 10 ошибок:<br>" . implode("<br>", array_slice($errors, 0, 10));
        if (count($errors) > 10) {
            $message .= "<br>... и ещё " . (count($errors) - 10) . " ошибок";
        }
        
    }
    
    return redirect()->back()->with("message_cart", $message);
}
private static function extractOrganizationIdFromUrl($url)
{
    if (empty($url)) {
        return null;
    }
    
    // Паттерн для URL типа zahoron.ru/город/organization/slug
    $pattern = '/zahoron\.ru\/([^\/]+)\/organization\/([^\/\?]+)/i';
    
    if (preg_match($pattern, $url, $matches)) {
        $orgSlug = $matches[2];
        $orgSlug = explode('?', $orgSlug)[0];
        
        // Ищем организацию по слагу
        $organization = Organization::where('slug', $orgSlug)->first();
        
        if ($organization) {
            return $organization->id;
        }
        
        // Пробуем найти по части слага (убираем ID в конце)
        $cleanedSlug = preg_replace('/-\d+$/', '', $orgSlug);
        if ($cleanedSlug !== $orgSlug) {
            $organization = Organization::where('slug', $cleanedSlug)->first();
            if ($organization) {
                return $organization->id;
            }
        }
    }
    
    return null;
}

//     public static function importReviews($request)
// {
//     $file = $request->file('file_reviews');
//     $spreadsheet = IOFactory::load($file);
//     $sheet = $spreadsheet->getActiveSheet();
    
//     $headers = $sheet->rangeToArray('A1:' . $sheet->getHighestColumn() . '1')[0];
//     $headers = array_map('strtolower', $headers);
    
//     $columnIndexes = [
//         'cemetery_id' => array_search('id', $headers),
//         'name' => array_search('Имя', $headers),
//         'date' => array_search('Дата', $headers),
//         'rating' => array_search('Оценка', $headers),
//         'content' => array_search('Отзыв', $headers),
//     ];

//     foreach ($columnIndexes as $key => $index) {
//         if ($index === false) {
//             return redirect()->back()->with("error_cart", "Отсутствует обязательная колонка: " . $key);
//         }
//     }

//     $reviews = array_slice($sheet->toArray(), 1);
//     $addedReviews = 0;
//     $skippedReviews = 0;
//     $errors = [];

//     foreach ($reviews as $rowIndex => $review) {
//         $rowNumber = $rowIndex + 2;
        
//         try {
//             if (empty(array_filter($review))) {
//                 $skippedReviews++;
//                 continue;
//             }
            
//             $cemeteryId =rtrim($review[$columnIndexes['cemetery_id']] ?? '', '!');
//             $reviewerName = $review[$columnIndexes['name']] ?? null;
//             $reviewDate = $review[$columnIndexes['date']] ?? null;
//             $rating = $review[$columnIndexes['rating']] ?? null;
//             $content = $review[$columnIndexes['content']] ?? null;
            
//             if (empty($cemeteryId)) {
//                 $errors[] = "Строка {$rowNumber}: Не указан ID кладбища";
//                 $skippedReviews++;
//                 continue;
//             }

//             $cemetery = Cemetery::find(transformID($cemeteryId));
//             if (!$cemetery) {
//                 $errors[] = "Строка {$rowNumber}: Кладбище с ID {$cemeteryId} не найдено";
//                 $skippedReviews++;
//                 continue;
//             }
            
//             if (!$cemetery->city) {
//                 $errors[] = "Строка {$rowNumber}: У кладбища не указан город";
//                 $skippedReviews++;
//                 continue;
//             }
            
//             if (!is_numeric($rating) || $rating < 1 || $rating > 5) {
//                 $errors[] = "Строка {$rowNumber}: Рейтинг должен быть числом от 1 до 5";
//                 $skippedReviews++;
//                 continue;
//             }
            
//             if (!empty($reviewDate)) {
//                 $reviewDate = trim(preg_replace('/отредактирован/ui', '', $reviewDate));
                
//                 $russianMonths = [
//                     'января' => '01', 'февраля' => '02', 'марта' => '03',
//                     'апреля' => '04', 'мая' => '05', 'июня' => '06',
//                     'июля' => '07', 'августа' => '08', 'сентября' => '09',
//                     'октября' => '10', 'ноября' => '11', 'декабря' => '12'
//                 ];
                
//                 if (preg_match('/^(\d{1,2})\s+([а-яё]+)\s+(\d{4})$/ui', $reviewDate, $matches)) {
//                     $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
//                     $month = strtolower($matches[2]);
//                     $year = $matches[3];
                    
//                     if (isset($russianMonths[$month])) {
//                         $reviewDate = "{$year}-{$russianMonths[$month]}-{$day}";
//                     } else {
//                         $errors[] = "Строка {$rowNumber}: Неизвестный месяц '{$matches[2]}' в дате '{$reviewDate}'";
//                         $skippedReviews++;
//                         continue;
//                     }
//                 } 
//                 elseif (($timestamp = strtotime($reviewDate)) !== false) {
//                     $reviewDate = date('Y-m-d', $timestamp);
//                 } else {
//                     $errors[] = "Строка {$rowNumber}: Не удалось распознать дату '{$reviewDate}'";
//                     $skippedReviews++;
//                     continue;
//                 }
//             } else {
//                 $reviewDate = now()->format('Y-m-d');
//             }
            
//             ReviewCemetery::create([
//                 'name' => $reviewerName,
//                 'rating' => $rating,
//                 'content' => $content,
//                 'created_at' => !empty($reviewDate) ? $reviewDate : now(),
//                 'cemetery_id' => $cemetery->id,
//                 'status' => 1,
//                 'city_id' => $cemetery->city->id,
//             ]);
            
//             $addedReviews++;
            
//         } catch (\Exception $e) {
//             $errors[] = "Строка {$rowNumber}: Ошибка обработки - " . $e->getMessage();
//             $skippedReviews++;
//             continue;
//         }
//     }
    
//     $message = "Импорт отзывов для кладбищ завершен. Добавлено: {$addedReviews}, Пропущено: {$skippedReviews}";
    
//     if (!empty($errors)) {
//         $message .= "<br><br>Ошибки:<br>" . implode("<br>", array_slice($errors, 0, 10));
//         if (count($errors) > 10) {
//             $message .= "<br>... и ещё " . (count($errors) - 10) . " ошибок";
//         }
//     }
    
//     return redirect()->back()->with("message_cart", $message);
// }

    /**
     * Импорт кладбищ из Filament с маппингом колонок
     *
     * @param string $file Путь к файлу в storage
     * @param array $columnMapping Маппинг системных полей на колонки файла
     * @param string $importType Режим импорта: 'create' или 'update'
     * @param array $columnsToUpdate Поля для обновления (только для режима update)
     * @param float $priceGeo Цена за геопозицию по умолчанию
     * @param string $jobId Уникальный идентификатор Job для отслеживания прогресса
     * @return array
     */
    public function importFromFilament(
        string $file,
        array $columnMapping,
        string $importType,
        array $columnsToUpdate,
        float $priceGeo,
        string $jobId
    ): array {
        try {
            $createdCemeteries = 0;
            $updatedCemeteries = 0;
            $skippedRows = 0;
            $errors = [];

            $realPath = storage_path('app/public/' . $file);
            $fileName = basename($file);

            if (!file_exists($realPath)) {
                $error = "Файл {$fileName} не найден по пути {$realPath}";
                Log::error($error);
                return [
                    'created' => 0,
                    'updated' => 0,
                    'skipped' => 0,
                    'errors' => [$error]
                ];
            }

            $totalRows = (new FastExcel)->import($realPath)->count();

            Redis::set("import_progress:{$jobId}:total", $totalRows);
            Redis::set("import_progress:{$jobId}:current", 0);
            Redis::set("import_progress:{$jobId}:status", 'В процессе');

            DB::beginTransaction();

            $rowNumber = 0;
            (new FastExcel)->import($realPath, function ($row) use (
                &$createdCemeteries,
                &$updatedCemeteries,
                &$skippedRows,
                &$errors,
                &$rowNumber,
                $columnMapping,
                $importType,
                $columnsToUpdate,
                $priceGeo,
                $jobId
            ) {
                $rowNumber++;
                try {
                    if (empty(array_filter($row))) {
                        $reason = "Строка {$rowNumber}: Пустая строка (все поля пустые)";
                        Log::info($reason);
                        $errors[] = $reason;
                        Redis::incr("import_progress:{$jobId}:current");
                        return;
                    }

                    // Функция для получения значения поля через маппинг
                    $getFieldValue = function ($sysKey) use ($columnMapping, $row) {
                        $fileColumn = $columnMapping[$sysKey] ?? null;
                        return $fileColumn && isset($row[$fileColumn])
                            ? trim((string)$row[$fileColumn])
                            : null;
                    };

                    // Проверка обязательных полей
                    $objectIdRaw = $getFieldValue('id_2gis');
                    // Для bigint unsigned сохраняем ID напрямую из Excel файла без transformId
                    $objectIdRawCleaned = $objectIdRaw ? rtrim($objectIdRaw, '!') : null;
                    // Преобразуем в число для работы с БД (для больших чисел используем прямое преобразование)
                    $objectId = $objectIdRawCleaned && is_numeric($objectIdRawCleaned) ? intval($objectIdRawCleaned) : null;
                    $title = $getFieldValue('title');

                    // Для режима update достаточно только ID 2GIS
                    if ($importType === 'update') {
                        if (!$objectId) {
                            $skippedRows++;
                            $mappedColumn = $columnMapping['id_2gis'] ?? 'не сопоставлено';
                            $reason = "Строка {$rowNumber}: Отсутствует обязательное поле ID 2GIS (колонка: '{$mappedColumn}', значение: " . ($objectIdRaw ?? 'пусто') . ")";
                            Log::warning($reason);
                            $errors[] = $reason;
                            Redis::incr("import_progress:{$jobId}:current");
                            return;
                        }
                    } else {
                        // Для режима create требуются ID и название
                        if (!$objectId || !$title) {
                            $skippedRows++;
                            $missingFields = [];
                            if (!$objectId) {
                                $mappedColumn = $columnMapping['id_2gis'] ?? 'не сопоставлено';
                                $missingFields[] = "ID 2GIS (колонка: '{$mappedColumn}', значение: " . ($objectIdRaw ?? 'пусто') . ")";
                            }
                            if (!$title) {
                                $mappedColumn = $columnMapping['title'] ?? 'не сопоставлено';
                                $missingFields[] = "Название кладбища (колонка: '{$mappedColumn}', значение: " . ($getFieldValue('title') ?? 'пусто') . ")";
                            }
                            $reason = "Строка {$rowNumber}: Отсутствуют обязательные поля: " . implode(', ', $missingFields);
                            Log::warning($reason);
                            $errors[] = $reason;
                            Redis::incr("import_progress:{$jobId}:current");
                            return;
                        }
                    }

                    // Получение связей регион/район/город (только для режима create)
                    $regionName = $getFieldValue('region');
                    $districtName = $getFieldValue('district');
                    $cityName = $getFieldValue('city');
                    $area = null;
                    $city = null;
                    $time_difference = 0;

                    // Для режима create данные о местоположении обязательны
                    if ($importType === 'create') {
                        if (!$regionName || !$districtName || !$cityName) {
                            $skippedRows++;
                            $missingFields = [];
                            if (!$regionName) {
                                $mappedColumn = $columnMapping['region'] ?? 'не сопоставлено';
                                $missingFields[] = "Регион (колонка: '{$mappedColumn}')";
                            }
                            if (!$districtName) {
                                $mappedColumn = $columnMapping['district'] ?? 'не сопоставлено';
                                $missingFields[] = "Район (колонка: '{$mappedColumn}')";
                            }
                            if (!$cityName) {
                                $mappedColumn = $columnMapping['city'] ?? 'не сопоставлено';
                                $missingFields[] = "Населённый пункт (колонка: '{$mappedColumn}')";
                            }
                            $reason = "Строка {$rowNumber}: Отсутствуют данные о местоположении: " . implode(', ', $missingFields) . " | ID: {$objectId}, Название: " . ($title ?? 'не указано');
                            Log::warning($reason);
                            $errors[] = $reason;
                            Redis::incr("import_progress:{$jobId}:current");
                            return;
                        }

                        $objects = linkRegionDistrictCity($regionName, $districtName, $cityName);
                        $area = $objects['district'] ?? null;
                        $city = $objects['city'] ?? null;

                        if (!$city || !$area) {
                            $skippedRows++;
                            $notFound = [];
                            if (!$city) {
                                $notFound[] = "город '{$cityName}'";
                            }
                            if (!$area) {
                                $notFound[] = "район '{$districtName}'";
                            }
                            $reason = "Строка {$rowNumber}: Не найдены в базе данных: " . implode(', ', $notFound) . " | Регион: {$regionName}, Район: {$districtName}, Город: {$cityName} | ID: {$objectId}, Название: {$title}";
                            Log::warning($reason);
                            $errors[] = $reason;
                            Redis::incr("import_progress:{$jobId}:current");
                            return;
                        }

                        // Обработка часового пояса для create
                        $time_difference = $city->utc_offset ?? null;
                        $width = $getFieldValue('width');
                        $longitude = $getFieldValue('longitude');

                        if ($time_difference == null && env('API_WORK') == 'true' && $width && $longitude) {
                            $timeData = getTimeByCoordinates($width, $longitude);
                            $time_difference = differencetHoursTimezone($timeData['timezone'] ?? 'UTC');
                            $city->update(['utc_offset' => $time_difference]);
                        }
                        if ($time_difference == null) {
                            $time_difference = 0;
                        }
                    } elseif ($importType === 'update') {
                        // Для режима update данные о местоположении опциональны
                        // Если они указаны, используем их, иначе оставим существующие значения
                        if ($regionName && $districtName && $cityName) {
                            $objects = linkRegionDistrictCity($regionName, $districtName, $cityName);
                            $area = $objects['district'] ?? null;
                            $city = $objects['city'] ?? null;

                            if ($city && $area) {
                                $time_difference = $city->utc_offset ?? null;
                                $width = $getFieldValue('width');
                                $longitude = $getFieldValue('longitude');

                                if ($time_difference == null && env('API_WORK') == 'true' && $width && $longitude) {
                                    $timeData = getTimeByCoordinates($width, $longitude);
                                    $time_difference = differencetHoursTimezone($timeData['timezone'] ?? 'UTC');
                                    $city->update(['utc_offset' => $time_difference]);
                                }
                                if ($time_difference == null) {
                                    $time_difference = 0;
                                }
                            }
                        }
                    }

                    // Обработка статуса
                    $status = 1;
                    $statusValue = $getFieldValue('status');
                    if ($statusValue && $statusValue != 'Действующая организация') {
                        $status = 0;
                    }

                    $width = $getFieldValue('width');
                    $longitude = $getFieldValue('longitude');

                    // Подготовка данных кладбища
                    $cemeteryData = [
                        'id' => $objectId,
                        'title' => $title,
                        'slug' => $title ? Str::slug($title) : ($objectId ? (string)$objectId : 'cemetery-' . uniqid()),
                        'adres' => $getFieldValue('address'),
                        'responsible_person_address' => $getFieldValue('responsible_person_address'),
                        'responsible_organization' => $getFieldValue('responsible_organization'),
                        'okved' => $getFieldValue('okved'),
                        'inn' => (int)($getFieldValue('inn') ?? 0),
                        'rating' => (float)str_replace(',', '.', $getFieldValue('rating') ?? 0),
                        'phone' => normalizePhone($getFieldValue('phone')),
                        'email' => $getFieldValue('email'),
                        'responsible' => $getFieldValue('responsible_person_full_name'),
                        'cadastral_number' => $getFieldValue('cadastral_number'),
                        'two_gis_link' => $getFieldValue('two_gis_link'),
                        'status' => $status,
                        'date_foundation' => $getFieldValue('date_foundation'),
                        'address_responsible_person' => $getFieldValue('responsible_person_address'),
                        'responsible_person_full_name' => $getFieldValue('responsible_person_full_name'),
                    ];

                    // Добавляем width и longitude только если они указаны или для режима create устанавливаем значения по умолчанию
                    if ($importType === 'create') {
                        // Для create устанавливаем значения по умолчанию, если они не указаны
                        $cemeteryData['width'] = $width !== null && $width !== '' ? $width : '0';
                        $cemeteryData['longitude'] = $longitude !== null && $longitude !== '' ? $longitude : '0';
                    } else {
                        // Для update добавляем только если значения указаны и поле в списке обновляемых
                        if ($width !== null && $width !== '' && in_array('width', $columnsToUpdate)) {
                            $cemeteryData['width'] = $width;
                        }
                        if ($longitude !== null && $longitude !== '' && in_array('longitude', $columnsToUpdate)) {
                            $cemeteryData['longitude'] = $longitude;
                        }
                    }

                    // Добавляем данные о местоположении только если они есть
                    if ($city && $area) {
                        $cemeteryData['city_id'] = $city->id;
                        $cemeteryData['area_id'] = $area->id;
                        $cemeteryData['time_difference'] = $time_difference;
                    }

                    // Для режима create добавляем обязательные поля
                    if ($importType === 'create') {
                        $cemeteryData['img_url'] = 'default';
                        $cemeteryData['href_img'] = 1;
                        $cemeteryData['price_burial_location'] = $priceGeo;
                    }

                    if ($importType === 'create') {
                        // Проверяем существование по ID или two_gis_link
                        $existing = Cemetery::where('id', $objectId)
                            ->orWhere('two_gis_link', $objectId)
                            ->first();

                        if ($existing) {
                            $skippedRows++;
                            $reason = "Строка {$rowNumber}: Запись уже существует в базе данных | ID: {$objectId}, Название: {$title} | Существующая запись ID: {$existing->id}";
                            Log::info($reason);
                            $errors[] = $reason;
                            Redis::incr("import_progress:{$jobId}:current");
                            return;
                        }

                        $cemetery = Cemetery::create($cemeteryData);
                        $createdCemeteries++;

                        // Обработка режима работы
                        $workHours = $getFieldValue('working_hours');
                        if ($workHours) {
                            $days = parseWorkingHours($workHours);
                            foreach ($days as $day) {
                                $holiday = ($day['time_start_work'] == 'Выходной') ? 1 : 0;
                                WorkingHoursCemetery::create([
                                    'day' => $day['day'],
                                    'time_start_work' => $day['time_start_work'],
                                    'time_end_work' => $day['time_end_work'],
                                    'holiday' => $holiday,
                                    'cemetery_id' => $cemetery->id,
                                ]);
                            }
                        }

                        // Обработка фотографий
                        $photos = $getFieldValue('photos');
                        if ($photos) {
                            $urls_array = explode(', ', $photos);
                            foreach ($urls_array as $img) {
                                if ($img) {
                                    ImageCemetery::create([
                                        'img_url' => $img,
                                        'href_img' => 1,
                                        'cemetery_id' => $cemetery->id,
                                    ]);
                                }
                            }
                        }

                    } elseif ($importType === 'update') {
                        // Ищем кладбище по two_gis_link или ID
                        $cemetery = Cemetery::where('two_gis_link', $objectId)
                            ->orWhere('id', $objectId)
                            ->first();

                        if ($cemetery) {
                            $updateData = [];
                            foreach ($columnsToUpdate as $field) {
                                if (isset($cemeteryData[$field])) {
                                    $updateData[$field] = $cemeteryData[$field];
                                }
                            }

                            // Обновляем slug если обновляется title
                            if (isset($updateData['title']) && !isset($updateData['slug'])) {
                                $updateData['slug'] = Str::slug($updateData['title']);
                            }

                            if (!empty($updateData)) {
                                $cemetery->update($updateData);
                                $updatedCemeteries++;
                            }

                            // Обработка режима работы при обновлении
                            if (in_array('working_hours', $columnsToUpdate)) {
                                $workHours = $getFieldValue('working_hours');
                                if ($workHours) {
                                    WorkingHoursCemetery::where('cemetery_id', $cemetery->id)->delete();
                                    $days = parseWorkingHours($workHours);
                                    foreach ($days as $day) {
                                        $holiday = ($day['time_start_work'] == 'Выходной') ? 1 : 0;
                                        WorkingHoursCemetery::create([
                                            'day' => $day['day'],
                                            'time_start_work' => $day['time_start_work'],
                                            'time_end_work' => $day['time_end_work'],
                                            'holiday' => $holiday,
                                            'cemetery_id' => $cemetery->id,
                                        ]);
                                    }
                                }
                            }

                            // Обработка фотографий при обновлении
                            if (in_array('photos', $columnsToUpdate)) {
                                $photos = $getFieldValue('photos');
                                if ($photos) {
                                    ImageCemetery::where('cemetery_id', $cemetery->id)->delete();
                                    $urls_array = explode(', ', $photos);
                                    foreach ($urls_array as $img) {
                                        if ($img) {
                                            ImageCemetery::create([
                                                'img_url' => $img,
                                                'href_img' => 1,
                                                'cemetery_id' => $cemetery->id,
                                            ]);
                                        }
                                    }
                                }
                            }
                        } else {
                            $skippedRows++;
                            $reason = "Строка {$rowNumber}: Кладбище не найдено в базе данных для обновления | ID: {$objectId}, Название: {$title}";
                            Log::warning($reason);
                            $errors[] = $reason;
                        }
                    }

                    if (($createdCemeteries + $updatedCemeteries) % 100 === 0) {
                        Log::info("Processed " . ($createdCemeteries + $updatedCemeteries) . " cemeteries...");
                    }

                    Redis::incr("import_progress:{$jobId}:current");
                } catch (\Exception $e) {
                    $skippedRows++;
                    $objectIdInfo = '';
                    $titleInfo = '';
                    try {
                        $getFieldValue = function ($sysKey) use ($columnMapping, $row) {
                            $fileColumn = $columnMapping[$sysKey] ?? null;
                            return $fileColumn && isset($row[$fileColumn])
                                ? trim((string)$row[$fileColumn])
                                : null;
                        };
                        $objectIdRaw = $getFieldValue('id_2gis');
                        // Для bigint unsigned сохраняем ID напрямую из Excel файла без transformId
                        $objectIdRawCleaned = $objectIdRaw ? rtrim($objectIdRaw, '!') : null;
                        $objectId = $objectIdRawCleaned && is_numeric($objectIdRawCleaned) ? intval($objectIdRawCleaned) : null;
                        $title = $getFieldValue('title'); 
                        $objectIdInfo = $objectId ? "ID: {$objectId}" : "ID: не определен";
                        $titleInfo = $title ? ", Название: {$title}" : ", Название: не определено";
                    } catch (\Exception $ex) {
                        // Игнорируем ошибки при попытке получить информацию
                    }
                    $reason = "Строка {$rowNumber}: Исключение при обработке | {$objectIdInfo}{$titleInfo} | Ошибка: {$e->getMessage()} | Файл: {$e->getFile()}:{$e->getLine()}";
                    Log::error($reason);
                    $errors[] = $reason;
                    Redis::incr("import_progress:{$jobId}:current");
                }
            });

            DB::commit();

            Redis::set("import_progress:{$jobId}:status", 'Выполнен');
            Redis::set("import_progress:{$jobId}:created", $createdCemeteries);
            Redis::set("import_progress:{$jobId}:updated", $updatedCemeteries);
            Redis::set("import_progress:{$jobId}:skipped", $skippedRows);
            
            // Сохраняем ошибки в Redis для последующего просмотра
            if (!empty($errors)) {
                Redis::set("import_progress:{$jobId}:errors", json_encode($errors, JSON_UNESCAPED_UNICODE));
                Log::info("Импорт завершен. Создано: {$createdCemeteries}, Обновлено: {$updatedCemeteries}, Пропущено: {$skippedRows}. Всего ошибок/предупреждений: " . count($errors));
            } else {
                Log::info("Импорт завершен успешно. Создано: {$createdCemeteries}, Обновлено: {$updatedCemeteries}, Пропущено: {$skippedRows}");
            }

            return [
                'created' => $createdCemeteries,
                'updated' => $updatedCemeteries,
                'skipped' => $skippedRows,
                'errors' => $errors,
            ];

        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            $error = "Критическая ошибка импорта: {$e->getMessage()} in {$e->getFile()}:{$e->getLine()}";
            Log::error($error);

            Redis::set("import_progress:{$jobId}:status", 'Ошибка');
            Redis::set("import_progress:{$jobId}:errors", json_encode([$error]));

            return [
                'created' => 0,
                'updated' => 0,
                'skipped' => 0,
                'errors' => [$error],
            ];
        }
    }

    /**
     * Извлекает заголовки из загруженного файла.
     *
     * @param TemporaryUploadedFile $file
     * @return array
     * @throws Exception
     */
    public function getFileHeaders(TemporaryUploadedFile $file): array
    {
        $fastExcel = new FastExcel();

        try {
            $collection = $fastExcel
                ->withoutHeaders()
                ->import($file->getRealPath());

            if ($collection->isEmpty()) {
                throw new Exception('Файл не содержит данных.');
            }

            $headers = $collection->first();

            if (!is_array($headers)) {
                throw new Exception('Некорректный формат данных в первой строке.');
            }

            return array_filter($headers, function ($header) {
                return !empty(trim((string)$header));
            });

        } catch (Exception $e) {
            throw new Exception("Не удалось прочитать заголовки из файла: " . $e->getMessage());
        }
    }
}