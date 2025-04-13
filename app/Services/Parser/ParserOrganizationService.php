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
        
        // Параметры фильтрации
        $filterRegion = $request->input('filter_region');
        $filterDistrict = $request->input('filter_district');
        $filterCity = $request->input('filter_city');
    
        foreach ($files as $file) {
            if (!$file->isValid()) {
                continue;
            }
    
            $spreadsheet = IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
        
            $titles = $sheet->toArray()[0];
            $organizations = array_slice($sheet->toArray(), 1);
        
            $columns = array_flip($titles);
        
            foreach($organizations as $organization) {
                $orgId = rtrim($organization[$columns['ID']] ?? '', '!');
                $orgTitle = $organization[$columns['Название организации']] ?? null;
                $address = $organization[$columns['Адрес']] ?? null;
                $latitude = $organization[$columns['Latitude']] ?? null;
                $longitude = $organization[$columns['Longitude']] ?? null;
                $phones = $organization[$columns['Телефоны']] ?? null;
                $workHours = $organization[$columns['Режим работы']] ?? null;
                $logoUrl = $organization[$columns['Логотип']] ?? null;
                $mainPhotoUrl = $organization[$columns['Главное фото']] ?? null;
                $photos = $organization[$columns['Фотографии']] ?? null;
                $services = $organization[$columns['Подраздел']] ?? null;
                $region = $organization[$columns['Регион']] ?? null;
                $cityName = $organization[$columns['city']] ?? null;
                $district = $organization[$columns['Район']] ?? null;
                $nameType = $organization[$columns['Вид деятельности']] ?? null;
                $urlSite = $organization[$columns['Сайт']] ?? null;
    
                // Пропускаем если нет ID
                if(empty($orgId)) continue;
        
                // Ищем организацию в базе с загрузкой связанных данных
                $existingOrg = Organization::with(['city', 'city.area'])->find($orgId);
                
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
                            $updateData['img_url'] = $logoUrl;
                            $updateData['href_img'] = 1;
                        }
                    }
                    
                    if(in_array('main_photo', $columnsToUpdate) && isset($columns['Главное фото'])) {
                        if($mainPhotoUrl) {
                            $updateData['img_main_url'] = $mainPhotoUrl;
                            $updateData['href_main_img'] = 1;
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
                                ImageOrganization::create([
                                    'img_url' => $img,
                                    'href_img' => 1,
                                    'organization_id' => $existingOrg->id,
                                ]);
                            }
                        }
                    }
        
                    // Обновляем категории
                    if(in_array('services', $columnsToUpdate) && isset($columns['Подраздел'])) {
                        if($services) {
                            addActiveCategory($services, [], $existingOrg);
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
    
                        $area = createArea($district, $region);
                        $city = createCity($cityName, $region);
                    
                        if($city == null || $phones == null) continue;
        
                        $time_difference = 12;
                        if(env('API_WORK')=='true'){
                            $time_difference=differencetHoursTimezone(getTimeByCoordinates($latitude,$longitude)['timezone']);
                        }
                        
                        $updateData = [
                            'title' => $orgTitle,
                            'slug'=> slugOrganization($orgTitle),
                            'adres' => $address,
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
                                ImageOrganization::create([
                                    'img_url' => $img,
                                    'href_img' => 1,
                                    'organization_id' => $existingOrg->id,
                                ]);
                            }
                        }
                        
                        if($services) {
                            addActiveCategory($services, [], $existingOrg);
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
    
                        $area = createArea($district, $region);
                        $city = createCity($cityName, $region);
                        if($city == null || $phones == null) continue;
        
                        $time_difference = 12;
                        if(env('API_WORK')=='true'){
                            $time_difference=differencetHoursTimezone(getTimeByCoordinates($latitude,$longitude)['timezone']);
                        }
                        
                        $logoUrl = $logoUrl ?: 'https://default-logo-url.com';
                        $mainPhotoUrl = $mainPhotoUrl ?: 'https://default-main-photo-url.com';
        
                        $organization_create = Organization::create([
                            'id' => $orgId,
                            'title' => $orgTitle,
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
                                ImageOrganization::create([
                                    'img_url' => $img,
                                    'href_img' => 1,
                                    'organization_id' => $organization_create->id,
                                ]);
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
                            addActiveCategory($services, [], $organization_create);
                        }
                    }
                }
            }
        }
        
        $message = $importType == 'update' 
            ? 'Данные организаций успешно обновлены' 
            : 'Организации успешно импортированы';
        
        return redirect()->back()->with("message_cart", $message);
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
                $organization=Organization::find($review[0]);
                if($cities!=null && $organization!=null){
                    ReviewsOrganization::create([
                        'name'=>$review[4],
                        'rating'=>$review[6],
                        'content'=>$review[7],
                        'created_at'=>$review[5],
                        'organization_id'=>$organization->id,
                        'status'=>1,
                        'city_id'=>$organization->city->id,
                        
                    ]);
                    $organization->updateRating();
                }
            }
        }
        return redirect()->back()->with("message_cart", 'Отзывы успешно добавлены');

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
                        'price' => is_numeric($priceValue) ? (float)$priceValue : $priceValue,
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

}