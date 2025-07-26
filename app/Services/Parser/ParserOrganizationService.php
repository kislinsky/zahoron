<?php

namespace App\Services\Parser;

use App\Models\ActivityCategoryOrganization;
use App\Models\CategoryProduct;
use App\Models\Cemetery;
use App\Models\City;
use App\Models\Edge;
use App\Models\ImageOrganization;
use App\Models\Organization;
use App\Models\ReviewsOrganization;
use App\Models\WorkingHoursOrganization;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ParserOrganizationService
{
    public static function index($request) {
        $spreadsheet = new Spreadsheet();
        $files = $request->file('files');
        $importType = $request->input('import_type'); // 'new' или 'update'
        $importWithUser = $request->input('import_with_user', 0);
        $columnsToUpdate = $request->input('columns_to_update', []);
        
        $importedFiles = []; 

        // Параметры фильтрации
        $filterRegion = $request->input('filter_region');
        $filterDistrict = $request->input('filter_district');
        $filterCity = $request->input('filter_city');
    
        foreach ($files as $file) {
            if (!$file->isValid()) {
                continue;
            }
    
            $fileName = $file->getClientOriginalName(); // Получаем имя файла
            $importedFiles[] = $fileName; // Добавляем в массив

            $spreadsheet = IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
        
            $titles = $sheet->toArray()[0];
            $organizations = array_slice($sheet->toArray(), 1);
        
            $filteredTitles = array_filter($titles, fn($value) => $value !== null);
            $columns = array_flip($filteredTitles);
        
            foreach($organizations as $organization) {
                $orgId = rtrim($organization[$columns['ID']] ?? '', '!');
                $orgTitle = $organization[$columns['Название организации']] ?? null;
                $address = $organization[$columns['Адрес']] ?? null;
                $latitude = $organization[$columns['Latitude']] ?? null;
                $two_gis_link = $organization[$columns['URL']] ?? null;
                $longitude = $organization[$columns['Longitude']] ?? null;
                $phones = $organization[$columns['Телефоны']] ?? null;
                $workHours = $organization[$columns['Режим работы']] ?? null;
                $logoUrl = $organization[$columns['Логотип']] ?? 'default';
                $mainPhotoUrl = $organization[$columns['Главное фото']] ?? 'default';
                $photos = $organization[$columns['Фотографии']] ?? null;
                $services = $organization[$columns['Подраздел']] ?? null;
                $region = $organization[$columns['Регион']] ?? null;
                $cityName = $organization[$columns['Населённый пункт']] ?? null;
                $district = $organization[$columns['Район']] ?? null;
                $nameType = $organization[$columns['Вид деятельности']] ?? null;
                $urlSite = $organization[$columns['Сайт']] ?? null;
    
                if($importType != 'update') {
                    if(empty($cityName)) continue;

                    $objects=linkRegionDistrictCity($region,$district,$cityName);

                    $area = $objects['district'];
                    $city =  $objects['city'];
                }

                // Пропускаем если нет ID
                if(empty($orgId)) continue;
        
                // Ищем организацию в базе с загрузкой связанных данных
                $existingOrg = Organization::find($orgId);
                
                // Применяем фильтры по местоположению
                if ($filterRegion || $filterDistrict || $filterCity) {
                    $locationMatch = true;
                    
                    if ($filterRegion && (!$existingOrg || !$existingOrg->city || !$existingOrg->city->area || 
                        $existingOrg->city->area->name != $filterRegion)) {
                        $locationMatch = false;
                    }
                    
                    if ($locationMatch && $filterDistrict && (!$existingOrg || !$existingOrg->city || 
                        $existingOrg->city->district != $filterDistrict)) {
                        $locationMatch = false;
                    }
                    
                    if ($locationMatch && $filterCity && (!$existingOrg || !$existingOrg->city || 
                        $existingOrg->city->name != $filterCity)) {
                        $locationMatch = false;
                    }
                    
                    if (!$locationMatch) continue;
                }
        
                // Проверяем условие import_with_user
                $skipByUserCondition = ($importWithUser == 0 && $existingOrg && $existingOrg->user_id != null) || 
                                    ($importWithUser == 1 && $existingOrg && $existingOrg->user_id == null);
                
                if($skipByUserCondition) continue;
        
                // Режим "Обновление"
                if($importType == 'update') {
                    if(!$existingOrg) continue;
        
                    $updateData = [];
                    
                    // Обновляем только выбранные колонки
                    if(in_array('title', $columnsToUpdate) && isset($columns['Название организации'])) {
                        if($orgTitle) {
                            $updateData['title'] = $orgTitle;
                            $updateData['slug'] = slugOrganization($orgTitle);
                        }
                    }
                    
                    if(in_array('address', $columnsToUpdate) && isset($columns['Адрес'])) {
                        if($address) $updateData['adres'] = $address;
                    }
                    
                    if(in_array('coordinates', $columnsToUpdate)) {
                        if(isset($columns['Latitude']) && $latitude) $updateData['width'] = $latitude;
                        if(isset($columns['Longitude']) && $longitude) $updateData['longitude'] = $longitude;
                    }
                    
                    if(in_array('phone', $columnsToUpdate) && isset($columns['Телефоны'])) {
                        if($phones) $updateData['phone'] = normalizePhone(phoneImport($phones));
                    }
                    
                    if(in_array('logo', $columnsToUpdate) && isset($columns['Логотип'])) {
                        if($logoUrl) {
                            if(!isBrokenLink($logoUrl)){
                                $updateData['img_url'] = $logoUrl;
                                $updateData['href_img'] = 1;
                            }
                        }
                    }
                    
                    if(in_array('main_photo', $columnsToUpdate) && isset($columns['Главное фото'])) {
                        if($mainPhotoUrl) {
                            if(!isBrokenLink($mainPhotoUrl)){
                                $updateData['img_main_url'] = $mainPhotoUrl;
                                $updateData['href_main_img'] = 1;
                            }
                        }
                    }
        
                    if(in_array('link_website', $columnsToUpdate) && isset($columns['Сайт'])) {
                        if($urlSite) $updateData['link_website'] = $urlSite;
                    }
    
                    if(in_array('name_type', $columnsToUpdate) && isset($columns['Вид деятельности'])) {
                        if($nameType) $updateData['name_type'] = $nameType;
                    }
        
                    if(!empty($updateData)) {
                        $existingOrg->update($updateData);
                    }
        
                    // Обновляем рабочие часы
                    if(in_array('working_hours', $columnsToUpdate) && isset($columns['Режим работы'])) {
                        if($workHours) {
                            WorkingHoursOrganization::where('organization_id', $existingOrg->id)->delete();
                            
                            $days = parseWorkingHours($workHours);
                            foreach($days as $day) {
                                $holiday = ($day['time_start_work'] == 'Выходной') ? 1 : 0;
                                WorkingHoursOrganization::create([
                                    'day' => $day['day'],
                                    'time_start_work' => $day['time_start_work'],
                                    'time_end_work' => $day['time_end_work'],
                                    'holiday' => $holiday,
                                    'organization_id' => $existingOrg->id,
                                ]);
                            }
                        }
                    }
        
                    // Обновляем галерею
                    if(in_array('gallery', $columnsToUpdate) && isset($columns['Фотографии'])) {
                        if($photos) {
                            ImageOrganization::where('organization_id', $existingOrg->id)->delete();
                            
                            $urls_array = explode(', ', $photos);
                            foreach($urls_array as $img) {
                                if(!isBrokenLink($img)){
                                    ImageOrganization::create([
                                        'img_url' => $img,
                                        'href_img' => 1,
                                        'organization_id' => $existingOrg->id,
                                    ]);
                                }
                            }
                        }
                    }
        
                    // Обновляем категории
                    if(in_array('services', $columnsToUpdate) && isset($columns['Подраздел'])) {
                        if($services) {
                            addActiveCategory($services, [], $existingOrg,0);
                        }
                    }
                }
                // Режим "Новый импорт"
                else {
                    // Если организация уже существует - обновляем ее
                    if($existingOrg) {
                        $district = $organization[$columns['Район']] ?? null;
                        $services = $organization[$columns['Подраздел']] ?? null;
                        $nearby = $organization[$columns['Рядом']] ?? null;
                        $email = trim(trim($organization[$columns['E-mail']] ?? '', '('), ')');
                        $whatsapp = $organization[$columns['WhatsApp']] ?? null;
                        $telegram = $organization[$columns['Telegram']] ?? null;
                        $description = $organization[$columns['Описание']] ?? null;
                        if(isset($columns['SEO Описание'])){
                            $description = $organization[$columns['SEO Описание']] ?? null;
                        }                        
                        $rating = $organization[$columns['Рейтинг']] ?? null;
                        $nameType = $organization[$columns['Вид деятельности']] ?? null;
    
                       $objects=linkRegionDistrictCity($region,$district,$cityName);

                        $area = $objects['district'];
                        $city =  $objects['city'];
                    
                        if($city == null || $phones == null) continue;
        
                        $time_difference = $city->utc_offset ?? null;
                        if($time_difference==null && env('API_WORK')=='true'){
                            $time_difference=differencetHoursTimezone(getTimeByCoordinates($latitude,$longitude)['timezone']);
                            $city->update(['utc_offset'=> $time_difference]);
                            
                        }
                        if($time_difference==null){
                        $time_difference=0;
                    }   
                        $updateData = [
                            'title' => $orgTitle,
                            'slug'=> slugOrganization($orgTitle),
                            'adres' => $address,
                            'two_gis_link'=>$two_gis_link,
                            'width' => $latitude,
                            'longitude' => $longitude,
                            'link_website' => $urlSite ?: $existingOrg->link_website,
                            'phone' => normalizePhone(phoneImport($phones)),
                            'img_url' => $logoUrl ?: $existingOrg->img_url,
                            'img_main_url' => $mainPhotoUrl ?: $existingOrg->img_main_url,
                            'href_img' => $logoUrl ? 1 : $existingOrg->href_img,
                            'href_main_img' => $mainPhotoUrl ? 1 : $existingOrg->href_main_img,
                            'email' => $email,
                            'nearby' => $nearby,
                            'content' => $description,
                            'city_id' => $city->id,
                            'rating' => $rating,
                            'time_difference' => $time_difference,
                            'whatsapp' => $whatsapp,
                            'name_type'=> $nameType,
                            'telegram' => $telegram,
                        ];
    
                        $existingOrg->update($updateData);
        
                        $area = $existingOrg->city->area;
                        if($area != null) {
                            $cemeteries = implode(',', $area->cities->flatMap(function ($city) {
                                return $city->cemeteries->pluck('id');
                            })->unique()->toArray()) . ',';
                            $existingOrg->update(['cemetery_ids' => $cemeteries]);
                        }
    
                        // Обновляем рабочие часы
                        if($workHours) {
                            WorkingHoursOrganization::where('organization_id', $existingOrg->id)->delete();
                            $days = parseWorkingHours($workHours);
                            foreach($days as $day) {
                                $holiday = ($day['time_start_work'] == 'Выходной') ? 1 : 0;
                                WorkingHoursOrganization::create([
                                    'day' => $day['day'],
                                    'time_start_work' => $day['time_start_work'],
                                    'time_end_work' => $day['time_end_work'],
                                    'holiday' => $holiday,
                                    'organization_id' => $existingOrg->id,
                                ]);
                            }
                        }
        
                        // Обновляем галерею
                        if($photos) {
                            ImageOrganization::where('organization_id', $existingOrg->id)->delete();
                            $urls_array = explode(', ', $photos);
                            foreach($urls_array as $img) {
                                if(!isBrokenLink($img)){
                                    ImageOrganization::create([
                                        'img_url' => $img,
                                        'href_img' => 1,
                                        'organization_id' => $existingOrg->id,
                                    ]);
                                }
                            }
                        }
                        
                        if($services) {
                            addActiveCategory($services, [], $existingOrg,0);
                        }
                    }
                    // Если организации нет - создаем новую
                    else {
                        $district = $organization[$columns['Район']] ?? null;
                        $services = $organization[$columns['Подраздел']] ?? null;
                        $nearby = $organization[$columns['Рядом']] ?? null;
                        $email = trim(trim($organization[$columns['E-mail']] ?? '', '('), ')');
                        $whatsapp = $organization[$columns['WhatsApp']] ?? null;
                        $telegram = $organization[$columns['Telegram']] ?? null;
                        $description = $organization[$columns['Описание']] ?? null;
                        if(isset($columns['SEO Описание'])){
                            $description = $organization[$columns['SEO Описание']] ?? null;
                        }
                        $rating = $organization[$columns['Рейтинг']] ?? null;
                        $nameType = $organization[$columns['Вид деятельности']] ?? null;
    
                        $objects=linkRegionDistrictCity($region,$district,$cityName);
                        $area = $objects['district'];
                        $city =  $objects['city'];
                            
                        if($city == null || $phones == null) continue;
        
                        $time_difference = $city->utc_offset ?? null;
                        if($time_difference==null && env('API_WORK')=='true'){
                            $time_difference=differencetHoursTimezone(getTimeByCoordinates($latitude,$longitude)['timezone']);
                            $city->update(['utc_offset'=> $time_difference]);

                        }
                        if($time_difference==null){
                        $time_difference=0;
                    }   
                        
                        if($logoUrl!=null && !isBrokenLink($logoUrl)){
                            $logoUrl = $logoUrl ;
                        }else{
                            $logoUrl = 'default';
                        }


                        if($mainPhotoUrl!=null && !isBrokenLink($mainPhotoUrl)){
                            $mainPhotoUrl = $mainPhotoUrl ;
                        }else{
                            $mainPhotoUrl = 'default';
                        }
        
                        $organization_create = Organization::create([
                            'id' => $orgId,
                            'title' => $orgTitle,
                            'two_gis_link'=>$two_gis_link,
                            'adres' => $address,
                            'nearby' => $nearby,
                            'width' => $latitude,
                            'longitude' => $longitude,
                            'phone' => normalizePhone(phoneImport($phones)),
                            'email' => $email,
                            'img_url' => $logoUrl,
                            'content' => $description,
                            'city_id' => $city->id,
                            'rating' => $rating,
                            'link_website' => $urlSite,
                            'href_img' => 1,
                            'href_main_img' => 1,
                            'img_main_url' => $mainPhotoUrl,
                            'slug' => slugOrganization($orgTitle),
                            'cemetery_ids' => '',
                            'time_difference' => $time_difference,
                            'whatsapp' => $whatsapp,
                            'telegram' => $telegram,
                            'name_type'=> $nameType,
                        ]);
        
                        // Обновляем cemetery_ids
                        $area = $organization_create->city->area;
                        if($area != null) {
                            $cemeteries = implode(',', $area->cities->flatMap(function ($city) {
                                return $city->cemeteries->pluck('id');
                            })->unique()->toArray()) . ',';
                            $organization_create->update(['cemetery_ids' => $cemeteries]);
                        }
        
                        // Добавляем фотографии
                        if($photos) {
                            $urls_array = explode(', ', $photos);
                            foreach($urls_array as $img) {
                                if(!isBrokenLink($img)){
                                    ImageOrganization::create([
                                        'img_url' => $img,
                                        'href_img' => 1,
                                        'organization_id' => $organization_create->id,
                                    ]);
                                }
                                
                            }
                        }
        
                        // Добавляем рабочие часы
                        if($workHours) {
                            $days = parseWorkingHours($workHours);
                            foreach($days as $day) {
                                $holiday = ($day['time_start_work'] == 'Выходной') ? 1 : 0;
                                WorkingHoursOrganization::create([
                                    'day' => $day['day'],
                                    'time_start_work' => $day['time_start_work'],
                                    'time_end_work' => $day['time_end_work'],
                                    'holiday' => $holiday,
                                    'organization_id' => $organization_create->id,
                                ]);
                            }
                        }
        
                        // Добавляем категории
                        if($services) {
                            addActiveCategory($services, [], $organization_create,0);
                        }
                    }
                }
            }
        }
        
        $message = $importType == 'update' 
            ? 'Данные организаций успешно обновлены' 
            : 'Организации успешно импортированы';
         // Добавляем информацию о файлах в сообщение
   
        if (!empty($importedFiles)) {
            $filesList = implode(', ', $importedFiles);
            $message .= " (файлы: " . $filesList . ")";
        }
        return redirect()->back()->with("message_cart", $message);
    }





    public static function importPrices($request) {

        $files = $request->file('files_prices'); // Массив файлов
        $importWithUser = $request->input('import_with_user_prices', 0); // 0 или 1
        $updateEmptyToAsk = $request->input('update_empty_to_ask', 0); // 0 или 1
        
        $processedFiles = 0;
        $updatedPrices = 0;
        $removedCategories = 0;
    
        foreach ($files as $file) {
            if (!$file->isValid()) {
                continue;
            }
    
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $titles = $sheet->toArray()[0];
            $prices = array_slice($sheet->toArray(), 1);
            
            // Создаем массив для быстрого доступа к индексам колонок по их названиям
            $columns = array_flip($titles);
            
            foreach ($prices as $price) {
                $orgId = rtrim($price[$columns['ID']] ?? '', '!');
                $categoryTitle = $price[$columns['Категория']] ?? null;
                $priceValue = $price[$columns['Цена']] ?? null;
    
                // Пропускаем если нет обязательных данных
                if ( empty($categoryTitle)) {
                    continue;
                }
            
    
                $organization = Organization::find($orgId);
                if (!$organization) continue;
    
                // Проверяем условие import_with_user
                if (($importWithUser == 0 && $organization->user_id != null) || 
                    ($importWithUser == 1 && $organization->user_id == null)) {
                    continue;
                }
    
                $categoryProduct = CategoryProduct::where('title', $categoryTitle)->first();
                if (!$categoryProduct) continue;
    
                $activeCategory = ActivityCategoryOrganization::where('organization_id', $organization->id)
                    ->where('category_children_id', $categoryProduct->id)
                    ->where('category_main_id', $categoryProduct->parent_id)
                    ->first();
    
                // Обработка случая "Нет" - удаляем из категории
                if (strtolower($priceValue) == 'Нет') {
                    if ($activeCategory) {
                        $activeCategory->delete();
                        $removedCategories++;
                    }
                    continue;
                }
    
                // Обработка пустой цены
                if (empty($priceValue)) {
                    if ($updateEmptyToAsk) {
                        $priceValue = 0;
                    } else {
                        continue; // Пропускаем если не нужно обновлять
                    }
                }
    
                // Обновляем или создаем запись
                if ($activeCategory) {
                    $activeCategory->update([
                        'price' => is_numeric($priceValue) ? (float)$priceValue : $priceValue,
                    ]);
                } else {
                    ActivityCategoryOrganization::create([
                        'price' => is_numeric((int)$priceValue) ? (float)$priceValue : $priceValue,
                        'category_children_id' => $categoryProduct->id,
                        'category_main_id' => $categoryProduct->parent_id,
                        'organization_id' => $organization->id,
                    ]);
                }
                
                $updatedPrices++;
            }
            
            $processedFiles++;
        }
    
        $message = "Цены успешно обновлены. " .
                   "Файлов обработано: $processedFiles, " .
                   "Обновлено цен: $updatedPrices, " .
                   "Удалено категорий: $removedCategories";
    
        return redirect()->back()->with("message_cart", $message);
    }




    
    public static function importReviews($request)
{
    $file = $request->file('file_reviews');
    $spreadsheet = IOFactory::load($file);
    $sheet = $spreadsheet->getActiveSheet();
    
    $headers = $sheet->rangeToArray('A1:' . $sheet->getHighestColumn() . '1')[0];
    $headers = array_map('strtolower', $headers);
    
    $columnIndexes = [
        'organization_id' => array_search('id', $headers),
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
            
            $organizationId = rtrim($review[$columnIndexes['organization_id']] ?? '', '!') ?? null;
            $reviewerName = $review[$columnIndexes['name']] ?? null;
            $reviewDate = $review[$columnIndexes['date']] ?? null;
            $rating = $review[$columnIndexes['rating']] ?? null;
            $content = $review[$columnIndexes['content']] ?? null;
            
            $organizationId=transformId($organizationId);

            if (empty($organizationId)) {
                $errors[] = "Строка {$rowNumber}: Не указан ID организации";
                $skippedReviews++;
                continue;
            }

            $organization = Organization::find($organizationId);
            if (!$organization) {
                $errors[] = "Строка {$rowNumber}: Организация с ID {$organizationId} не найдена";
                $skippedReviews++;
                continue;
            }
            
            if (!$organization->city) {
                $errors[] = "Строка {$rowNumber}: У организации не указан город";
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
            
            $review=ReviewsOrganization::create([
                'name' => $reviewerName,
                'rating' => $rating,
                'content' => $content,
                'created_at' => !empty($reviewDate) ? $reviewDate : now(),
                'organization_id' => $organization->id,
                'status' => 1,
                'city_id' => $organization->city->id,
            ]);
            $addedReviews++;
            
        } catch (\Exception $e) {
            $errors[] = "Строка {$rowNumber}: Ошибка обработки - " . $e->getMessage();
            $skippedReviews++;
            continue;
        }
    }
    
    $message = "Импорт отзывов для организаций завершен. Добавлено: {$addedReviews}, Пропущено: {$skippedReviews}";
    
    if (!empty($errors)) {
        $message .= "<br><br>Ошибки:<br>" . implode("<br>", array_slice($errors, 0, 10));
        if (count($errors) > 10) {
            $message .= "<br>... и ещё " . (count($errors) - 10) . " ошибок";
        }
    }
    
    return redirect()->back()->with("message_cart", $message);
}
}





