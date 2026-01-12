<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\CategoryProduct;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Rap2hpoutre\FastExcel\FastExcel;

class ExportProductsCommand extends Command
{
    protected $signature = 'export:products 
                            {user : ID пользователя}
                            {--columns=* : Колонки для экспорта}
                            {--category= : ID категории}
                            {--city= : ID города}
                            {--filename=products_export.xlsx : Имя файла}';
    
    protected $description = 'Экспорт товаров в Excel файл';

    // Карта колонок (русские названия)
    protected $columnsMap = [
        'id' => 'ID',
        'title' => 'Название',
        'slug' => 'Слаг',
        'view' => 'Отображение',
        'organization.title' => 'Организация',
        'organization.city.title' => 'Город',
        'price' => 'Цена',
        'price_sale' => 'Цена со скидкой',
        'total_price' => 'Итоговая цена',
        'category.title' => 'Подкатегория',
        'category.parent.title' => 'Категория',
        'material' => 'Материал',
        'color' => 'Цвет',
        'layering' => 'Тип продукта',
        'size' => 'Размеры',
        'created_at' => 'Дата создания',
        'updated_at' => 'Дата обновления',
    ];

    public function handle()
    {
        ini_set('memory_limit', '2048M');
        set_time_limit(3600);
        
        $userId = $this->argument('user');
        $user = User::findOrFail($userId);
        
        $this->info("Начинаем экспорт для пользователя: {$user->email}");
        
        // Получаем параметры
        $columns = $this->option('columns') ?: array_keys($this->columnsMap);
        $filename = $this->option('filename');
        
        // Строим запрос
        $query = Product::query()
            ->select([
                'products.id',
                'products.title',
                'products.slug',
                'products.view',
                'products.organization_id',
                'products.price',
                'products.price_sale',
                'products.total_price',
                'products.category_id',
                'products.material',
                'products.color',
                'products.layering',
                'products.size',
                'products.created_at',
                'products.updated_at',
            ])
            ->with([
                'organization' => function($query) {
                    $query->select('id', 'title', 'city_id');
                },
                'organization.city' => function($query) {
                    $query->select('id', 'title');
                },
                'category' => function($query) {
                    $query->select('id', 'title', 'parent_id')
                        ->with('parent:id,title');
                }
            ]);
        
        // Применяем права доступа для deputy-admin
        if ($user->role === 'deputy-admin') {
            $cityIds = $this->getUserCityIds($user);
            if (!empty($cityIds)) {
                $query->whereHas('organization.city', function($q) use ($cityIds) {
                    $q->whereIn('id', $cityIds);
                });
            } else {
                $query->whereNull('organization_id');
            }
        }
        
        // Применяем фильтры
        if ($categoryId = $this->option('category')) {
            $query->where('category_product_id', $categoryId);
        }
        
        if ($cityId = $this->option('city')) {
            $query->whereHas('organization.city', function($q) use ($cityId) {
                $q->where('id', $cityId);
            });
        }
        
        // Считаем общее количество
        $total = $query->count();
        $this->info("Найдено товаров: {$total}");
        
        if ($total === 0) {
            $this->error('Нет товаров для экспорта');
            return;
        }
        
        // Создаем прогресс-бар
        $bar = $this->output->createProgressBar($total);
        $bar->start();
        
        // Подготавливаем данные
        $data = [];
        
        $headers = [];
        foreach ($columns as $column) {
            $headers[] = $this->columnsMap[$column] ?? $column;
        }
        $data[] = $headers;
        
        // Экспортируем чанками
        $chunkSize = 1000;
        $offset = 0;
        
        while ($offset < $total) {
            $products = $query->offset($offset)->limit($chunkSize)->get();
            
            foreach ($products as $product) {
                $row = [];
                foreach ($columns as $column) {
                    $row[] = $this->getColumnValue($product, $column);
                }
                $data[] = $row;
                $bar->advance();
            }
            
            $offset += $chunkSize;
            
            // Освобождаем память
            unset($products);
            gc_collect_cycles();
        }
        
        $bar->finish();
        
        $this->newLine(2);
        $this->info("Данные подготовлены для экспорта");
        
        // Путь для сохранения файла
        $excelPath = storage_path('app/public/exports/' . $filename);
        
        // Создаем директорию если нет
        if (!file_exists(dirname($excelPath))) {
            mkdir(dirname($excelPath), 0777, true);
        }
        
        // Экспортируем в Excel
        $collection = collect($data);
        (new FastExcel($collection))->export($excelPath);
        
        $this->info("✅ Экспорт завершен!");
        $this->info("Файл сохранен: " . $excelPath);
        
        // Отправляем уведомление пользователю
        $this->sendNotification($user, $filename);
    }
    
    private function getUserCityIds($user): array
    {
        $cityIds = [];
        
        if (!empty($user->city_ids)) {
            $decoded = json_decode($user->city_ids, true);
            
            if (is_array($decoded)) {
                $cityIds = $decoded;
            } else {
                $cityIds = array_filter(explode(',', trim($user->city_ids, '[],"')));
            }
            
            $cityIds = array_map('intval', array_filter($cityIds));
        }
        
        return $cityIds;
    }
    
    private function getColumnValue($product, $column)
    {
        switch ($column) {
            case 'id':
                return (string) $product->id;
                
            case 'view':
                return $product->view ? 'Показывать' : 'Не показывать';
                
            case 'organization.title':
                return $product->organization->title ?? '';
                
            case 'organization.city.title':
                return $product->organization->city->title ?? '';
                
            case 'category.title':
                return $product->category->title ?? '';
                
            case 'category.parent.title':
                return $product->category->parent->title ?? '';
                
            case 'created_at':
            case 'updated_at':
                return $product->{$column} ? $product->{$column}->format('d.m.Y H:i:s') : '';
                
            default:
                return $product->{$column} ?? '';
        }
    }
    
    private function sendNotification($user, $filename)
    {
        // Получаем базовый URL из конфигурации
        $baseUrl = config('app.url');
        
        // Формируем сообщение
        $message = "Экспорт начался. Файл будет доступен для скачивания по ссылке: {$baseUrl}/download-latest-export";
        
        // Отправляем email (используйте свою реализацию sendMail)
        sendMail($user->email, 'Экспорт товаров', $message);
        
        $this->info("Уведомление отправлено пользователю: {$user->email}");
    }
}