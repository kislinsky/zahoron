<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Organization;
use App\Models\Product;
use App\Models\ActivityCategoryOrganization;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CreateProductsForOrganizations extends Command
{
    protected $signature = 'products:create-for-organizations';
    protected $description = 'Создать товары для всех организаций по их категориям деятельности';

    public function handle()
    {
        $this->info('Начинаем создание товаров для организаций...');

        $createdCount = 0;

        // Обрабатываем по частям, чтобы избежать переполнения памяти
        ActivityCategoryOrganization::with(['organization' => function($query) {
            $query->select('id', 'title', 'city_id', 'district_id', 'width', 'longitude');
        }, 'categoryProduct' => function($query) {
            $query->select('id', 'title', 'parent_id');
        }])->chunkById(100, function ($organizationCategories) use (&$createdCount) {
            
            foreach ($organizationCategories as $orgCategory) {
                $organization = $orgCategory->organization;
                $category = $orgCategory->categoryProduct;

                if (!$organization || !$category) {
                    $this->info("Пропуск: организация или категория не найдена");
                    continue;
                }

                // Пропускаем категории без parent_id (основные категории)
                if (!$category->parent_id) {
                    $this->info("Пропуск: категория {$category->title} без parent_id");
                    continue;
                }

                // Проверяем, не существует ли уже такой товар
                $existingProduct = Product::where('organization_id', $organization->id)
                    ->where('category_id', $category->id)
                    ->first();

                if ($existingProduct) {
                    $this->info("Товар уже существует для организации: {$organization->title} и категории: {$category->title}");
                    continue;
                }

                // Определяем данные товара в зависимости от категории
                $productData = $this->getProductDataByCategory($category->title, $organization);

                // Если для категории нет данных, пропускаем
                if (!$productData) {
                    $this->info("Пропуск: нет данных для категории {$category->title}");
                    continue;
                }

                // Создаем товар
                $product = Product::create([
                    'title' => $productData['title'],
                    'category_id' => $category->id,
                    'category_parent_id' => $category->parent_id,
                    'size' => $productData['size'],
                    'content' => $productData['content'],
                    'material' => $productData['material'],
                    'color' => $productData['color'],
                    'status' => 'active',
                    'view' => 0,
                    'price' => $productData['price'],
                    'price_sale' => $productData['price_sale'],
                    'total_price' => $productData['total_price'],
                    'city_id' => $organization->city_id,
                    'title_institution' => $organization->title,
                    'organization_id' => $organization->id,
                    'capacity' => $productData['capacity'],
                    'location_width' => $organization->width,
                    'location_longitude' => $organization->longitude,
                    'district_id' => $organization->district_id,
                    'type' => 'product',
                    'provider_id' => null,
                    'layering' => $productData['layering'],
                    'cafe' => $productData['cafe'],
                    'count_people' => $productData['count_people'],
                    'slug' => Str::slug($productData['title'] . '-' . $organization->id . '-' . time()),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Добавляем изображение для товара
                $this->addProductImage($product->id, $category->title);

                $createdCount++;
                $this->info("Создан товар: {$productData['title']} для организации: {$organization->title}");
            }
        });

        $this->info("Готово! Создано {$createdCount} товаров.");
        
        return Command::SUCCESS;
    }

  private function getProductDataByCategory($categoryName, $organization)
{
    // Базовые данные
    $baseData = [
        'size' => null,
        'content' => 'Описание товара будет добавлено позже',
        'material' => null,
        'color' => null,
        'price' => 0,
        'price_sale' => null,
        'total_price' => 0,
        'capacity' => null,
        'layering' => null,
        'cafe' => null,
        'count_people' => null,
    ];

    $categoryName = trim($categoryName);

    // Простые материалы
    $simpleMaterials = [
        'Камень',
        'Металл', 
        'Дерево',
        'Пластик'
    ];

    // Простые размеры в формате 100*100*100
    $simpleSizes = [
        '100*100*100',
        '150*150*150', 
        '200*200*200',
        '250*250*250'
    ];

    switch ($categoryName) {
        case 'Организация похорон':
            return array_merge($baseData, [
                'title' => 'Организация похорон - ' . $organization->title,
                'content' => 'Полная организация похоронных услуг',
                'price' => 0,
                'total_price' => 0,
                'count_people' => 50,
            ]);

        case 'Организация кремации':
            return array_merge($baseData, [
                'title' => 'Услуги кремации - ' . $organization->title,
                'content' => 'Организация кремации',
                'price' => 0,
                'total_price' => 0,
            ]);

        case 'Подготовка отправки груза 200':
            return array_merge($baseData, [
                'title' => 'Транспортировка гроба 200 - ' . $organization->title,
                'content' => 'Подготовка и транспортировка груза',
                'price' => 0,
                'total_price' => 0,
            ]);

        case 'Памятники':
            return array_merge($baseData, [
                'title' => 'Изготовление памятников - ' . $organization->title,
                'content' => 'Изготовление и установка памятников',
                'material' => $simpleMaterials[0], // Камень
                'color' => 'Черный',
                'size' => $simpleSizes[array_rand($simpleSizes)],
                'price' => 0,
                'total_price' => 0,
            ]);

        case 'Оградки':
            return array_merge($baseData, [
                'title' => 'Изготовление оградок - ' . $organization->title,
                'content' => 'Изготовление и установка оградок',
                'material' => $simpleMaterials[1], // Металл
                'color' => 'Черный',
                'size' => $simpleSizes[array_rand($simpleSizes)],
                'price' => 0,
                'total_price' => 0,
            ]);

        case 'Плитка на могилу':
            return array_merge($baseData, [
                'title' => 'Укладка плитки на могилу - ' . $organization->title,
                'content' => 'Профессиональная укладка плитки',
                'material' => $simpleMaterials[0], // Камень
                'size' => $simpleSizes[array_rand($simpleSizes)],
                'price' => 0,
                'total_price' => 0,
            ]);

        case 'Столики лавочки':
            return array_merge($baseData, [
                'title' => 'Изготовление столиков и лавочек - ' . $organization->title,
                'content' => 'Изготовление столиков и лавочек',
                'material' => $simpleMaterials[2], // Дерево
                'size' => $simpleSizes[array_rand($simpleSizes)],
                'price' => 0,
                'total_price' => 0,
            ]);

        case 'Кресты на могилу':
            return array_merge($baseData, [
                'title' => 'Изготовление крестов на могилу - ' . $organization->title,
                'content' => 'Изготовление крестов на могилу',
                'material' => $simpleMaterials[2], // Дерево
                'size' => $simpleSizes[array_rand($simpleSizes)],
                'price' => 0,
                'total_price' => 0,
            ]);

        case 'Вазы на могилу':
            return array_merge($baseData, [
                'title' => 'Изготовление ваз на могилу - ' . $organization->title,
                'content' => 'Изготовление ваз для цветов',
                'material' => $simpleMaterials[3], // Пластик
                'size' => $simpleSizes[array_rand($simpleSizes)],
                'price' => 0,
                'total_price' => 0,
            ]);

        case 'Траурные венки':
            return array_merge($baseData, [
                'title' => 'Изготовление траурных венков - ' . $organization->title,
                'content' => 'Изготовление траурных венков',
                'material' => $simpleMaterials[3], // Пластик
                'size' => $simpleSizes[array_rand($simpleSizes)],
                'price' => 0,
                'total_price' => 0,
            ]);

        case 'Фото на памятник':
            return array_merge($baseData, [
                'title' => 'Нанесение фото на памятник - ' . $organization->title,
                'content' => 'Нанесение фотографий на памятники',
                'material' => $simpleMaterials[3], // Пластик
                'size' => $simpleSizes[array_rand($simpleSizes)],
                'price' => 0,
                'total_price' => 0,
            ]);

        default:
            return null;
    }
}

    private function addProductImage($productId, $categoryName)
    {
        $imagePaths = [
            'Памятники' => 'uploads_product/Group 37.png',
            'Оградки' => 'uploads_product/default.png',
            'Плитка на могилу' => 'uploads_product/default.png',
            'Столики лавочки' => 'uploads_product/default.png',
            'Кресты на могилу' => 'uploads_product/default.png',
            'Вазы на могилу' => 'uploads_product/default.png',
            'Траурные венки' => 'uploads_product/default.png',
            'Фото на памятник' => 'uploads_product/default.png',
            'Организация похорон' => 'uploads_product/image 173 (1).png',
            'Организация кремации' => 'uploads_product/diploma (2).png',
            'Подготовка отправки груза 200' => 'uploads_product/595_original (1).png',
            'Поминальных обеды' => 'uploads_product/image 6 (1).png',
            'Поминальные залы' => 'uploads_product/image 6 (1).png',
            'Кремация животных' => 'uploads_product/image 6 (1).png',
        ];

        $imagePath = $imagePaths[$categoryName] ?? 'uploads_product/default.png';

        // Вставляем изображение в таблицу images_product
        DB::table('image_products')->insert([
            'title' => $imagePath,
            'selected' => 1,
            'product_id' => $productId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}