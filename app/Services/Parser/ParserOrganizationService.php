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
        $file = $request->file('file');
        $importType = $request->input('import_type'); // 'new' или 'update'
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
    
        $titles = $sheet->toArray()[0];
        $organizations = array_slice($sheet->toArray(), 1);
    
        // Создаем массив для быстрого доступа к индексам колонок по их названиям
        $columns = array_flip($titles);
    
        foreach($organizations as $organization) {
            // Получаем значения по названиям колонок
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
            $categories = $organization[$columns['Виды услуг']] ?? null;
            $region = $organization[$columns['Регион']] ?? null;
            $cityName = $organization[$columns['city']] ?? null;
    
            // Пропускаем если нет ID
            if(empty($orgId)) continue;
    
            // Ищем организацию в базе
            $existingOrg = Organization::find($orgId);
    
            // Режим "Обновление"
            if($importType == 'update') {
                // Если организация не существует или у нее status == 0 - пропускаем
                if(!$existingOrg || $existingOrg->status == 0) continue;
    
                // Подготовка данных для обновления
                $updateData = [];
                if($orgTitle) $updateData['title'] = $orgTitle;
                if($orgTitle) $updateData['slug'] = slugOrganization($orgTitle);
                if($address) $updateData['adres'] = $address;
                if($latitude) $updateData['width'] = $latitude;
                if($longitude) $updateData['longitude'] = $longitude;
                if($phones) $updateData['phone'] = phoneImport($phones);
                
                // Обновляем изображения если они есть
                if($logoUrl) {
                    $updateData['img_url'] = $logoUrl;
                    $updateData['href_img'] = 1;
                }
                if($mainPhotoUrl) {
                    $updateData['img_main_url'] = $mainPhotoUrl;
                    $updateData['href_main_img'] = 1;
                }
    
                // Применяем обновления если есть что обновлять
                if(!empty($updateData)) {
                    $existingOrg->update($updateData);
                }
    
                // Обновляем рабочие часы если они есть
                if($workHours) {
                    // Удаляем старые часы работы
                    WorkingHoursOrganization::where('organization_id', $existingOrg->id)->delete();
                    
                    // Добавляем новые
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
    
                // Обновляем галерею если есть новые фото
                if($photos) {
                    // Удаляем старые фото
                    ImageOrganization::where('organization_id', $existingOrg->id)->delete();
                    
                    // Добавляем новые
                    $urls_array = explode(', ', $photos);
                    foreach($urls_array as $img) {
                        ImageOrganization::create([
                            'img_url' => $img,
                            'href_img' => 1,
                            'organization_id' => $existingOrg->id,
                        ]);
                    }
                }
    
                // // Обновляем категории и подкатегории если они есть
                // if($categories) {
                //     $categoriesArray = array_map('trim', explode(',', $categories));
                    
                //     // Здесь должен быть код для обновления категорий
                //     // Например: updateCategories($existingOrg, $categoriesArray, $subcategoriesArray);
                //     addActiveCategory($categoriesArray, $subcategoriesArray, $existingOrg);
                // }
            }
            // Режим "Новый импорт"
            else {
                // Если организация уже существует - обновляем ее (если status != 0)
                if($existingOrg) {
                    if($existingOrg->status == 0) continue;
                    $district = $organization[$columns['Район']] ?? null;
                    $services = $organization[$columns['Виды услуг']] ?? null;
                    $nearby = $organization[$columns['Рядом']] ?? null;
                    $email = trim(trim($organization[$columns['E-mail']] ?? '', '('), ')');
                    $whatsapp = $organization[$columns['WhatsApp']] ?? null;
                    $telegram = $organization[$columns['Telegram']] ?? null;
                    $description = $organization[$columns['SEO Описание']] ?? null;
                    $rating = $organization[$columns['Рейтинг']] ?? null;

                    $area = createArea($district, $region);
                    $city = createCity($cityName, $region);
                 
                    if($city == null || $phones == null) continue;
    
                    $time_difference = 12;
                    // Подготовка данных для обновления
                    $updateData = [
                        'title' => $orgTitle,
                        'slug'=>slugOrganization($orgTitle),
                        'adres' => $address,
                        'width' => $latitude,
                        'longitude' => $longitude,
                        'phone' => phoneImport($phones),
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
                        'telegram' => $telegram,

                    ];

                    // Обновляем организацию
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
                    
                    // // Обновляем категории
                    // if($categories) {
                    //     $categoriesArray = array_map('trim', explode(',', $categories));
                    //     addActiveCategory($categoriesArray, [], $existingOrg);
                    // }
                }
                // Если организации нет - создаем новую
                else {
                    // Получаем дополнительные данные для создания
                    $district = $organization[$columns['Район']] ?? null;
                    $services = $organization[$columns['Виды услуг']] ?? null;
                    $nearby = $organization[$columns['Рядом']] ?? null;
                    $email = trim(trim($organization[$columns['E-mail']] ?? '', '('), ')');
                    $whatsapp = $organization[$columns['WhatsApp']] ?? null;
                    $telegram = $organization[$columns['Telegram']] ?? null;
                    $description = $organization[$columns['SEO Описание']] ?? null;
                    $rating = $organization[$columns['Рейтинг']] ?? null;
    
                    // Создаем город и район
                    $area = createArea($district, $region);
                    $city = createCity($cityName, $region);
                    if($city == null || $phones == null) continue;
    
                    $time_difference = 12;
                    
                    // Устанавливаем дефолтные изображения если не указаны
                    $logoUrl = $logoUrl ?: 'https://default-logo-url.com';
                    $mainPhotoUrl = $mainPhotoUrl ?: 'https://default-main-photo-url.com';
    
                    $organization_create = Organization::create([
                        'id' => $orgId,
                        'title' => $orgTitle,
                        'adres' => $address,
                        'nearby' => $nearby,
                        'width' => $latitude,
                        'longitude' => $longitude,
                        'phone' => phoneImport($phones),
                        'email' => $email,
                        'img_url' => $logoUrl,
                        'content' => $description,
                        'city_id' => $city->id,
                        'rating' => $rating,
                        'href_img' => 1,
                        'href_main_img' => 1,
                        'img_main_url' => $mainPhotoUrl,
                        'slug' => slugOrganization($orgTitle),
                        'cemetery_ids' => '',
                        'time_difference' => $time_difference,
                        'whatsapp' => $whatsapp,
                        'telegram' => $telegram,
                        'status' => 1 // Новые организации по умолчанию включены
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
    
                    // Добавляем категории и подкатегории
                    if($services) {
                        addActiveCategory($services, [], $organization_create);
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


    public static function importPrices($request){
        $spreadsheet = new Spreadsheet();
        $file = $request->file('file_prices');
        $spreadsheet = IOFactory::load($file);
        // Получение данных из первого листа
        $sheet = $spreadsheet->getActiveSheet();
        $prices = array_slice($sheet->toArray(),1);
        foreach($prices as $price){
                $city=City::where('title',$price[8])->first();
                if($city!=null){
                    $organization=Organization::where('title',$price[3])->where('city_id',$city->id)->first();
                    $category_product=CategoryProduct::where('title',$price[5])->first();
                    if($organization!=null && $category_product!=null){
                        $active_category=ActivityCategoryOrganization::where('organization_id',$organization->id)->where('category_children_id',$category_product->id)->where('category_main_id',$category_product->parent_id)->first();
                        if( $organization!=null && $category_product!=null){
                            if($active_category==null){
                                ActivityCategoryOrganization::create([
                                    'rating'=>$price[6],
                                    'category_children_id'=>$category_product->id,
                                    'category_main_id'=>$category_product->parent_id,
                                    'organization_id'=>$organization->id,
                                    'city_id'=>$organization->city->id,
                                    'role'=>'organization',
                                    'cemetery_ids'=>$organization->cemetery_ids,
                                    'price'=>0,
                                    'rating'=>$organization->rating,
                                ]);
                            }
                        }
                    }
                   
                }            
        }
        return redirect()->back()->with("message_cart", 'Цены успешно добавлены');

    }

}