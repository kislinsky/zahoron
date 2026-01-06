<?php

namespace App\Services\Parser;

use App\Models\CategoryProduct;
use App\Models\ImageProduct;
use App\Models\Organization;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;


class ParserProduct
{
    public static function index($request)
    {
        $files = $request->file('files');
        foreach ($files as $file) {
            if (!$file->isValid()) {
                continue;
            }
       
            $spreadsheet = IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            
            $headers = $sheet->rangeToArray('A1:' . $sheet->getHighestColumn() . '1')[0];
            $headers = array_map('strtolower', $headers);
            
            // Определяем индексы колонок
            $columnIndexes = [
                'url' => array_search('url', $headers),
                'products' => array_search('Товары', $headers),
            ];
            
            // Проверяем обязательные колонки
            foreach ($columnIndexes as $key => $index) {
                if ($index === false) {
                    return redirect()->back()->with("error_cart", "Отсутствует обязательная колонка: " . $key);
                }
            }

            // Получаем данные (пропускаем заголовок)
            $rows = array_slice($sheet->toArray(), 1);
            $addedProducts = 0;
            $skippedProducts = 0;
            $errors = [];

            foreach ($rows as $rowIndex => $row) {
                $rowNumber = $rowIndex + 2;
                
                try {
                    if (empty(array_filter($row))) {
                        $skippedProducts++;
                        continue;
                    }
                    
                    $url = $row[$columnIndexes['url']] ?? null;
                    $productsData = $row[$columnIndexes['products']] ?? null;
                    
                    if (empty($url)) {
                        $errors[] = "Строка {$rowNumber}: Не указан URL организации";
                        $skippedProducts++;
                        continue;
                    }
                    
                    if (empty($productsData)) {
                        $errors[] = "Строка {$rowNumber}: Не указаны товары";
                        $skippedProducts++;
                        continue;
                    }
                    
                    // Ищем организацию по two_gis_link
                    $organization = Organization::where('two_gis_link', 'like', '%' . $url . '%')->first();

                    
                    
                    if (!$organization) {
                        $errors[] = "Строка {$rowNumber}: Организация с URL '{$url}' не найдена";
                        $skippedProducts++;
                        continue;
                    }
                    
                    if (!$organization->city) {
                        $errors[] = "Строка {$rowNumber}: У организации не указан город";
                        $skippedProducts++;
                        continue;
                    }
                    
                    // Парсим строку с товарами
                    $productsList =self::parseProductsString($productsData);
                    
                    if (empty($productsList)) {
                        $errors[] = "Строка {$rowNumber}: Не удалось распарсить товары";
                        $skippedProducts++;
                        continue;
                    }
                    
                    Product::where('organization_id',$organization->id)->delete();
                    
                    // Обрабатываем каждый товар
                    foreach ($productsList as $productItem) {
                        try {
                            $categoryName = $productItem['category'] ?? null;
                            $productTitle = $productItem['title'] ?? null;
                            $price = $productItem['price'] ?? null;
                            $imageUrl = $productItem['image'] ?? null;
                            
                            if (empty($categoryName) || empty($productTitle)) {
                                $errors[] = "Строка {$rowNumber}: Пропущен товар - отсутствует категория или название";
                                $skippedProducts++;
                                continue;
                            }
                            
                            // Ищем категорию по названию
                            $category = CategoryProduct::where('title', $categoryName)->first();
                            
                    
                            
                            // Извлекаем числовое значение цены
                            $priceValue = self::extractPrice($price);
                            
                            // Создаем товар
                            $product = Product::create([
                                'title' => $productTitle,
                                'category_id' => $category->id,
                                'price' => $priceValue,
                                'total_price' => $priceValue,
                                'status' => 1,
                                'view' => 1,
                                'type' => 'product',
                                'organization_id' => $organization->id,
                                'city_id' => $organization->city->id,
                                'title_institution' => $organization->name,
                                'slug' => Str::slug($productTitle . '-' . uniqid()),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                            
                            // Загружаем изображение, если есть
                            if (!empty($imageUrl) && filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                                try {
                                    $imageContent = file_get_contents($imageUrl);
                                    if ($imageContent !== false) {
                                        $imageName = 'product_' . $product->id . '_' . time() . '.jpg';
                                        $imagePath = 'uploads_product/' . $imageName;
                                        
                                        // Сохраняем изображение
                                        Storage::disk('public')->put($imagePath, $imageContent);
                                        
                                        // Создаем запись в таблице изображений (предполагается, что есть модель ProductImage)
                                        ImageProduct::create([
                                            'product_id' => $product->id,
                                            'title' => $imagePath,
                                        ]);
                                    }
                                } catch (\Exception $e) {
                                     ImageProduct::create([
                                            'product_id' => $product->id,
                                            'title' => 'uploads_product/default.png',
                                        ]);
                                    // Если не удалось загрузить изображение, используем дефолтное
                                    $errors[] = "Строка {$rowNumber}: Не удалось загрузить изображение для товара '{$productTitle}': " . $e->getMessage();
                                }
                            }
                            
                            $addedProducts++;
                            
                        } catch (\Exception $e) {
                            $errors[] = "Строка {$rowNumber}: Ошибка создания товара '{$productTitle}' - " . $e->getMessage();
                            $skippedProducts++;
                        }
                    }
                    
                } catch (\Exception $e) {
                    $errors[] = "Строка {$rowNumber}: Ошибка обработки - " . $e->getMessage();
                    $skippedProducts++;
                    continue;
                }
            }
        }
        
        $message = "Импорт товаров организаций завершен. Добавлено: {$addedProducts}, Пропущено: {$skippedProducts}";
        
        if (!empty($errors)) {
            $message .= "<br><br>Ошибки:<br>" . implode("<br>", array_slice($errors, 0, 10));
            if (count($errors) > 10) {
                $message .= "<br>... и ещё " . (count($errors) - 10) . " ошибок";
            }
        }
        
        return redirect()->back()->with("message_cart", $message);
    }

    /**
     * Парсит строку с товарами
     */
    private static function parseProductsString($productsString)
    {
        $products = [];
        
        // Разделяем строку по точкам с запятой
        $items = explode(';', $productsString);
        
        foreach ($items as $item) {
            $item = trim($item);
            if (empty($item)) {
                continue;
            }
            
            // Разделяем на части по запятым
            $parts = explode(',', $item);
            
            if (count($parts) >= 3) {
                $product = [
                    'category' => trim($parts[0]),
                    'title' => trim($parts[1]),
                    'price' => trim($parts[2]),
                    'image' => isset($parts[3]) ? trim($parts[3]) : null,
                ];
                
                $products[] = $product;
            }
        }
        
        return $products;
    }

    /**
     * Извлекает числовое значение цены
     */
    private static function extractPrice($priceString)
    {
        if (empty($priceString)) {
            return 0;
        }
        
        // Удаляем все символы, кроме цифр
        $price = preg_replace('/[^0-9]/', '', $priceString);
        
        return (int)$price ?: 0;
    }
}