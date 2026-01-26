<?php

namespace App\Services\Parser;

use App\Models\CategoryProduct;
use App\Models\ImageProduct;
use App\Models\Organization;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use OpenSpout\Reader\Common\Creator\ReaderFactory;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Rap2hpoutre\FastExcel\FastExcel;


class ParserProduct
{
    private array $orgCache = [];
    private array $categoryCache = [];

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
                'url'      => array_search('url', $headers),
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
                    $productsList = self::parseProductsString($productsData);

                    if (empty($productsList)) {
                        $errors[] = "Строка {$rowNumber}: Не удалось распарсить товары";
                        $skippedProducts++;
                        continue;
                    }

                    Product::where('organization_id', $organization->id)->delete();

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
                                'title'             => $productTitle,
                                'category_id'       => $category->id,
                                'price'             => $priceValue,
                                'total_price'       => $priceValue,
                                'status'            => 1,
                                'view'              => 1,
                                'type'              => 'product',
                                'organization_id'   => $organization->id,
                                'city_id'           => $organization->city->id,
                                'title_institution' => $organization->name,
                                'slug'              => Str::slug($productTitle . '-' . uniqid()),
                                'created_at'        => now(),
                                'updated_at'        => now(),
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
                                            'title'      => $imagePath,
                                        ]);
                                    }
                                } catch (\Exception $e) {
                                    ImageProduct::create([
                                        'product_id' => $product->id,
                                        'title'      => 'uploads_product/default.png',
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

    public function importFromFilament(string $file, array $columnMapping, string $jobId)
    {
        try {
            $createdProducts = 0;
            $skippedRows = 0;
            $errors = [];

            $realPath = Storage::disk('public')->path($file);
            $fileName = basename($file);

            if (!file_exists($realPath)) {
                $error = "Файл {$fileName} не найден по пути {$realPath}";
                Log::error($error);
                return ['created' => 0, 'skipped' => 0, 'errors' => [$error]];
            }

            $totalRows = $this->getCountRowsInCsv($realPath);

            Redis::set("import_progress:{$jobId}:total", $totalRows);
            Redis::set("import_progress:{$jobId}:current", 0);
            Redis::set("import_progress:{$jobId}:status", 'В процессе');


            (new FastExcel)->import($realPath, function ($row) use (
                &$createdProducts,
                &$skippedRows,
                &$errors,
                $columnMapping,
                $jobId,
            ) {
                try {
                    if (empty(array_filter($row))) {
                        Redis::incr("import_progress:{$jobId}:current");
                        $skippedRows++;
                        return;
                    }

                    // Маппинг и доступ к полям
                    $getFieldValue = function ($sysKey) use ($columnMapping, $row) {
                        $fileColumn = $columnMapping[$sysKey] ?? null;
                        return $fileColumn && isset($row[$fileColumn])
                            ? trim((string)$row[$fileColumn])
                            : null;
                    };

                    $organization_id = rtrim($getFieldValue('organization_id') ?: '', '!');

                    // Ищем организацию по id
                    $organization = $this->getOrganizationWithCache($organization_id);

                    if (!$organization) {
                        $skippedRows++;
                        $error_message = "Пропущена строка. Организация с ID {$organization_id} не найдена";
                        $errors[] = $error_message;
                        Log::warning($error_message);
                        Redis::incr("import_progress:{$jobId}:current");
                        return;
                    }

                    if (!$organization->city) {
                        $skippedRows++;
                        $error_message = "Пропущена строка. У организации с ID {$organization_id} не указан город";
                        $errors[] = $error_message;
                        Log::warning($error_message);
                        Redis::incr("import_progress:{$jobId}:current");
                        return;
                    }

                    $category = $this->getCategoryWithCache(
                        $getFieldValue('category_title'),
                        $getFieldValue('category_parent_title')
                    );

                    if (!empty($category['error'])) {
                        $skippedRows++;
                        $error_message = $category['error'];
                        $errors[] = $error_message;
                        Log::warning($error_message);
                        Redis::incr("import_progress:{$jobId}:current");
                        return;
                    }

                    $productTitle = $getFieldValue('title') ?: $category->title . ' - ' . $organization->title;
                    $price = $getFieldValue('price');
                    $imageUrl = $getFieldValue('img');

                    // Извлекаем числовое значение цены
                    $priceValue = self::extractPrice($price);

                    // Создаем товар
                    $product = Product::create([
                        'title'              => rtrim($productTitle),
                        'category_id'        => $category->id,
                        'category_parent_id' => $category->parent_id,
                        'content'            => $getFieldValue('content'),
                        'price'              => $priceValue,
                        'total_price'        => $priceValue,
                        'status'             => 1,
                        'view'               => 1,
                        'type'               => 'product',
                        'organization_id'    => $organization->id,
                        'city_id'            => $organization->city->id,
                        'title_institution'  => $organization->title,
                        'slug'               => Str::slug($productTitle . '-' . uniqid()),
                        'created_at'         => now(),
                        'updated_at'         => now(),
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
                                    'title'      => $imagePath,
                                ]);
                            }
                        } catch (\Exception $e) {
                            ImageProduct::create([
                                'product_id' => $product->id,
                                'title'      => 'uploads_product/default.png',
                            ]);
                        }
                    }

                    $createdProducts++;
                    Redis::incr("import_progress:{$jobId}:current");
                } catch (\Exception $e) {
                    $skippedRows++;
                    $error_message = "Ошибка при импорте строки: {$e->getMessage()} in {$e->getFile()}:{$e->getLine()}";
                    $errors[] = $error_message;
                    Log::warning($error_message);
                    Redis::incr("import_progress:{$jobId}:current");
                    return;
                }
            });

            Redis::set("import_progress:{$jobId}:status", 'Выполнен');
            Redis::set("import_progress:{$jobId}:created", $createdProducts);
            Redis::set("import_progress:{$jobId}:skipped", $skippedRows);

            return [
                'created' => $createdProducts,
                'skipped' => $skippedRows,
                'errors'  => $errors,
            ];
        } catch (\Exception $e) {
            $error = "Критическая ошибка импорта: {$e->getMessage()} in {$e->getFile()}:{$e->getLine()}";
            Log::error($error);

            Redis::set("import_progress:{$jobId}:status", 'Ошибка');
            Redis::set("import_progress:{$jobId}:errors", json_encode([$error]));

            return [
                'created' => 0,
                'skipped' => 0,
                'errors'  => [$error],
            ];
        }
    }

    /**
     * Кешированный поиск организации
     */
    private function getOrganizationWithCache(?string $orgId): ?Organization
    {
        if (!$orgId) return null;

        if (!isset($this->orgCache[$orgId])) {
            $this->orgCache[$orgId] = Organization::with('city')->find($orgId);
        }

        return $this->orgCache[$orgId];
    }

    /**
     * Кешированный поиск категории с автоматическим созданием "Прочее"
     */
    private function getCategoryWithCache(?string $title, ?string $parentTitle)
    {
        if (empty($parentTitle)) {
            return [
                'error' => "Пропущена строка. Отсутствует родительская категория: {$parentTitle}"
            ];
        }

        $targetTitle = !empty($title) ? $title : 'Прочее';

        $cacheKey = "{$parentTitle}_{$targetTitle}";

        if (!isset($this->categoryCache[$cacheKey])) {
            $parent = CategoryProduct::where('title', $parentTitle)
                ->whereNull('parent_id')
                ->first();

            if (!$parent) {
                $this->categoryCache[$cacheKey] = null;
                return [
                    'error' => "Пропущена строка. Отсутствует родительская категория: {$parentTitle}"
                ];
            }

            $category = CategoryProduct::where('title', $targetTitle)
                ->where('parent_id', $parent->id)
                ->first();

            if (!$category) {
                $category = CategoryProduct::create([
                    'title'     => 'Прочее',
                    'parent_id' => $parent->id,
                    'type'      => $parent->type,
                    'slug'      => Str::slug('Прочее-' . $parent->title)
                ]);
            }

            $this->categoryCache[$cacheKey] = $category;
        }

        return $this->categoryCache[$cacheKey];
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
                    'title'    => trim($parts[1]),
                    'price'    => trim($parts[2]),
                    'image'    => isset($parts[3]) ? trim($parts[3]) : null,
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

    /**
     * Извлекает заголовки из загруженного файла.
     *
     * @param TemporaryUploadedFile $file
     * @return array
     * @throws Exception
     */
    public function getFileHeaders(TemporaryUploadedFile $file): array
    {
        $reader = ReaderFactory::createFromFile($file->getRealPath());
        $reader->open($file->getRealPath());

        $headers = [];
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $headers = $row->toArray();
                break 2;
            }
        }
        $reader->close();

        return array_filter($headers, fn($h) => !empty(trim((string)$h)));
    }

    /**
     * Максимально быстрый подсчет строк в CSV
     */
    private function getCountRowsInCsv(string $path): int
    {
        if (!file_exists($path)) return 0;

        $count = 0;

        // Если сервер на Linux/Unix
        if (function_exists('shell_exec') && strpos(strtolower(PHP_OS), 'win') === false) {
            $output = shell_exec("wc -l < " . escapeshellarg($path));
            $count = (int)$output;
        } else {
            // Фоллбек: потоковое чтение файла, чтобы не "съесть" оперативку
            $handle = fopen($path, "r");
            while (!feof($handle)) {
                fgets($handle);
                $count++;
            }
            fclose($handle);
        }

        return $count > 0 ? $count - 1 : 0;
    }
}
