<?php

namespace App\Services\Parser;

use App\Models\Cemetery;
use App\Models\City;

use App\Models\Edge;
use App\Models\ImageCemetery;
use App\Models\PriceService;
use App\Models\ReviewCemetery;
use App\Models\Service;
use App\Models\WorkingHoursCemetery;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

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
                $requiredColumns = ['Наименование кладбища', 'Latitude', 'Longitude', 'кадастровый номер'];
                foreach ($requiredColumns as $col) {
                    if (!isset($columns[$col])) {
                        continue 2;
                    }
                }
    
                foreach ($cemeteriesData as $rowIndex => $cemeteryRow) {
                    try {
                        // Проверка обязательных полей
                        if (empty($cemeteryRow[$columns['Наименование кладбища']])) {
                            $skippedRows++;
                            continue;
                        }
    
                        $cadastralNumber = $cemeteryRow[$columns['кадастровый номер']];
                        if (empty($cadastralNumber)) {
                            $skippedRows++;
                            continue;
                        }
    
                        // Проверка координат
                        if (empty($cemeteryRow[$columns['Latitude']])) {
                            $skippedRows++;
                            continue;
                        }
    
                        if (empty($cemeteryRow[$columns['Longitude']])) {
                            $skippedRows++;
                            continue;
                        }
    
                        
                        $objects = linkRegionDistrictCity(
                            $cemeteryRow[$columns['Край/Область'] ?? null],
                            $cemeteryRow[$columns['Муниципального округа'] ?? null],
                            $cemeteryRow[$columns['Населённый пункт'] ?? null]
                        );
    
                        $area = $objects['district'] ?? null;
                        $city = $objects['city'] ?? null;
    
                        if (!$city || !$area) {
                            $skippedRows++;
                            continue;
                        }
    
                        $status = $cemeteryRow[$columns['Статус кладбища']] == 'Открыто' ? 1 : 0;
                        

                        $time_difference = $city->utc_offset ?? null;
                        if($time_difference==null && env('API_WORK')=='true'){
                            $time_difference=differencetHoursTimezone(getTimeByCoordinates($cemeteryRow[$columns['Latitude']],$cemeteryRow[$columns['Longitude']])['timezone']);
                            $city->update(['utc_offset'=> $time_difference]);
                        }

                        $cemeteryData = [
                            'title' => $cemeteryRow[$columns['Наименование кладбища']],
                            'adres' => $cemeteryRow[$columns['Ориентир'] ?? null],
                            'content'=>$cemeteryRow[$columns['SEO Описание']]  ?? $cemeteryRow[$columns['Описание']],
                            'rating'=>$cemeteryRow[$columns['Рейтинг']],
                            'width' => $cemeteryRow[$columns['Latitude']],
                            'longitude' => $cemeteryRow[$columns['Longitude']],
                            'city_id' => $city->id,
                            'two_gis_link'=> $crematoriumRow[$columns['URL']]  ?? null,
                            'area_id' => $area->id,
                            'phone' => normalizePhone($cemeteryRow[$columns['Тел. Ответственного'] ?? null]),
                            'square' => $cemeteryRow[$columns['Общая площадь (га)'] ?? null],
                            'responsible' => $cemeteryRow[$columns['Ответственный'] ?? null],
                            'cadastral_number' => $cadastralNumber, // Сохраняем оригинальный кадастровый номер
                            'status' => $status,
                            'img_url' => 'default',
                            'href_img' => 1,
                            'count_burials'=>$cemeteryRow[$columns['Захоронения'] ?? null],
                            'inn'=>$cemeteryRow[$columns['ИНН'] ?? null],
                            'price_burial_location' => $price ?? 0,
                            'time_difference' => $time_difference,
                        ];
    
                        if ($importAction === 'create') {
                            // Проверяем существование по кадастровому номеру
                            $existing = Cemetery::where('cadastral_number', $cadastralNumber)->first();
                            if ($existing) {
                                $skippedRows++;
                                continue;
                            }
                            $cemetery=Cemetery::create($cemeteryData);
                            // Обработка режима работы при создании
                            if(isset($columns['Режим работы']) && $cemeteryRow[$columns['Режим работы']] != null) {
                                $workHours = $cemeteryRow[$columns['Режим работы']];
                                $days = parseWorkingHours($workHours);
                                
                                foreach($days as $day) {
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
                            $createdCemeteries++;
                        } elseif ($importAction === 'update') {
                            // Ищем по кадастровому номеру
                            $cemetery = Cemetery::where('cadastral_number', $cadastralNumber)->first();
                            
                            if ($cemetery) {
                                $updateData = [];
                                foreach ($updateFields as $field) {
                                    if (isset($cemeteryData[$field])) {
                                        $updateData[$field] = $cemeteryData[$field];
                                    }
                                }
                                
                                if (!empty($updateData)) {
                                    $cemetery->update($updateData);
                                    $updatedCemeteries++;
                                }

                                if(in_array('working_hours', $updateFields) && isset($columns['Режим работы'])) {
                                $workHours = $cemeteryRow[$columns['Режим работы']] ?? null;
                                if($workHours) {
                                    // Удаляем старые записи о рабочем времени
                                    WorkingHoursCemetery::where('cemetery_id', $cemetery->id)->delete();
                                    
                                    // Создаем новые записи
                                    $days = parseWorkingHours($workHours);
                                    foreach($days as $day) {
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
                            } else {
                                $skippedRows++;
                            }
                        }
                    } catch (\Exception $e) {
                        $skippedRows++;
                    }
                }
                
                $processedFiles++;
            } catch (\Exception $e) {
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


    public static function importReviews($request){
        $spreadsheet = new Spreadsheet();
        $file = $request->file('file_reviews');
        $spreadsheet = IOFactory::load($file);
        // Получение данных из первого листа
        $sheet = $spreadsheet->getActiveSheet();
        $reviews = array_slice($sheet->toArray(),1);
        foreach($reviews as $review){
            $edge=Edge::where('title',$review[2])->first();
            if($edge!=null){
                $cities=City::where('edge_id',$edge->id)->pluck('id');
                $cemetery=Cemetery::where('title',$review[3])->whereIn('city_id',$cities)->first();
                if($cemetery!=null){
                    ReviewCemetery::create([
                        'name'=>$review[5],
                        'rating'=>$review[7],
                        'content'=>$review[9],
                        'created_at'=>$review[6],
                        'cemetery_id'=>$cemetery->id,
                        'status'=>1,
                    ]);
                    $cemetery->updateRating();
                }
            }
            
        }
        return redirect()->back()->with("message_cart", 'Отзывы успешно добавлены');

    }

}