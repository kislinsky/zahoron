<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use App\Models\User;
use Rap2hpoutre\FastExcel\FastExcel;

class ProcessProductExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600;
    public $tries = 3;
    
    protected $userId;
    protected $columns;
    protected $filters;
    protected $filename;
    
    public function __construct($userId, $columns = [], $filters = [], $filename = null)
    {
        $this->userId = $userId;
        $this->columns = $columns;
        $this->filters = $filters;
        $this->filename = $filename ?: 'products_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
    }
    
    public function handle()
    {
        try {
            Log::info('====== EXPORT JOB STARTED ======');
            Log::info('User ID:', ['user_id' => $this->userId]);
            Log::info('Columns:', ['columns' => $this->columns]);
            Log::info('Filename:', ['filename' => $this->filename]);
            
            $user = User::find($this->userId);
            if (!$user) {
                throw new \Exception('Пользователь не найден');
            }
            
            // Увеличиваем лимиты
            ini_set('memory_limit', '2048M');
            set_time_limit(3600);
            
            // Строим запрос
            $query = $this->buildQuery($user);
            
            // Получаем колонки для экспорта
            $exportColumns = $this->getExportColumns();
            Log::info('Export columns:', ['export_columns' => $exportColumns]);
            
            // Создаем коллекцию для данных
            $data = collect();
            
            // Добавляем заголовки ПЕРВОЙ строкой
            $headers = [];
            foreach ($exportColumns as $columnKey) {
                $headers[] = $this->getColumnName($columnKey);
            }
            $data->push($headers);
            Log::info('Headers added:', ['headers' => $headers]);
            
            // Получаем общее количество
            $total = $query->count();
            Log::info('Total products to export:', ['total' => $total]);
            
            if ($total === 0) {
                Log::warning('No products found for export');
                $this->createNotification($this->userId, 'Нет товаров для экспорта', null, 0);
                return;
            }
            
            // Обрабатываем чанками
            $chunkSize = 500; // Уменьшаем для тестирования
            $processed = 0;
            
            Log::info('Starting chunk processing...');
            
            $query->chunk($chunkSize, function($products) use (&$data, $exportColumns, &$processed, $total) {
                Log::info('Processing chunk', ['chunk_size' => $products->count()]);
                
                foreach ($products as $product) {
                    try {
                        $row = [];
                        foreach ($exportColumns as $columnKey) {
                            $row[] = $this->getColumnValue($product, $columnKey);
                        }
                        $data->push($row);
                        $processed++;
                        
                        if ($processed % 100 === 0) {
                            Log::info('Progress:', ['processed' => $processed, 'total' => $total]);
                        }
                    } catch (\Exception $e) {
                        Log::error('Error processing product:', [
                            'product_id' => $product->id,
                            'error' => $e->getMessage()
                        ]);
                        // Продолжаем обработку остальных товаров
                        continue;
                    }
                }
                
                gc_collect_cycles();
            });
            
            Log::info('Data collection completed:', ['total_rows' => $data->count()]);
            
            // Сохраняем файл
            $filePath = 'exports/' . $this->filename;
            $fullPath = storage_path('app/public/' . $filePath);
            
            // Создаем директорию
            $dir = dirname($fullPath);
            if (!file_exists($dir)) {
                Log::info('Creating directory:', ['dir' => $dir]);
                mkdir($dir, 0755, true);
            }
            
            // Проверяем права на запись
            if (!is_writable($dir)) {
                throw new \Exception("Директория не доступна для записи: {$dir}");
            }
            
            // Проверяем данные перед экспортом
            if ($data->count() <= 1) {
                Log::warning('No data to export', ['data_count' => $data->count()]);
                $sample = $data->take(3)->toArray();
                Log::info('Data sample:', ['sample' => $sample]);
                throw new \Exception('Нет данных для экспорта');
            }
            
            // Экспортируем в Excel
            Log::info('Starting Excel export...');
            (new FastExcel($data))->export($fullPath);
            
            // Проверяем создание файла
            if (!file_exists($fullPath)) {
                throw new \Exception("Файл не был создан: {$fullPath}");
            }
            
            $fileSize = filesize($fullPath);
            Log::info('File created successfully:', [
                'path' => $fullPath,
                'size' => $fileSize,
                'size_mb' => round($fileSize / 1024 / 1024, 2)
            ]);
            
            // URL для скачивания
            $downloadUrl = asset('storage/exports/' . $this->filename);
            
            // Создаем уведомление
            $this->createNotification($this->userId, $downloadUrl, $this->filename, $processed);
            
            Log::info('====== EXPORT JOB COMPLETED SUCCESSFULLY ======');
            
        } catch (\Exception $e) {
            Log::error('====== EXPORT JOB FAILED ======');
            Log::error('Error message:', ['error' => $e->getMessage()]);
            Log::error('Error trace:', ['trace' => $e->getTraceAsString()]);
            
            // Создаем уведомление об ошибке
            $this->createNotification(
                $this->userId, 
                null, 
                $this->filename, 
                0, 
                $e->getMessage()
            );
            
            // Пробрасываем исключение дальше для повторных попыток
            throw $e;
        }
    }
    
    protected function getExportColumns(): array
    {
        Log::info('Getting export columns', ['input_columns' => $this->columns]);
        
        // Если колонки пустые, используем все по умолчанию
        if (empty($this->columns)) {
            return [
                'id', 'title', 'organization.title', 
                'organization.city.title', 'price', 'price_sale'
            ];
        }
        
        // Проверяем формат
        if (is_string($this->columns)) {
            $columns = json_decode($this->columns, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $columns = explode(',', $this->columns);
            }
        } else {
            $columns = (array)$this->columns;
        }
        
        // Фильтруем только существующие колонки
        $availableColumns = [
            'id', 'title', 'slug', 'view', 'organization.title', 
            'organization.city.title', 'price', 'price_sale', 'total_price',
            'category.title', 'category.parent.title', 'material', 'color',
            'layering', 'size', 'created_at', 'updated_at'
        ];
        
        $validColumns = array_filter($columns, function($column) use ($availableColumns) {
            return in_array($column, $availableColumns);
        });
        
        return !empty($validColumns) ? array_values($validColumns) : ['id', 'title'];
    }
    
   protected function buildQuery($user)
{
    $query = Product::query()
        ->with([
            'organization:id,title,city_id',
            'organization.city:id,title',
            'category:id,title,parent_id',
            'category.parent:id,title'
        ]);
    
    // Права доступа
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
    
    // Применяем фильтры из таблицы
    Log::info('Applying filters in Job', ['filters' => $this->filters]);
    
    if (!empty($this->filters['category_id'])) {
        $query->where('category_id', $this->filters['category_id']);
    }
    
    if (!empty($this->filters['category_parent_id'])) {
        $query->whereHas('category.parent', function($q) {
            $q->where('id', $this->filters['category_parent_id']);
        });
    }
    
    if (!empty($this->filters['city_id'])) {
        $query->whereHas('organization.city', function($q) {
            $q->where('id', $this->filters['city_id']);
        });
    }
    
    if (!empty($this->filters['cemetery_id'])) {
        $query->whereHas('organization', function($q) {
            $q->whereRaw("FIND_IN_SET(?, cemetery_ids)", [$this->filters['cemetery_id']]);
        });
    }
    
    return $query;
}
    
    protected function getUserCityIds($user)
    {
        $cityIds = [];
        
        if (!empty($user->city_ids)) {
            if (is_string($user->city_ids)) {
                $decoded = json_decode($user->city_ids, true);
                
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $cityIds = $decoded;
                } else {
                    $cityIds = array_filter(explode(',', trim($user->city_ids, '[],"')));
                }
                
                $cityIds = array_map('intval', array_filter($cityIds));
            } elseif (is_array($user->city_ids)) {
                $cityIds = $user->city_ids;
            }
        }
        
        return $cityIds;
    }
    
    protected function getColumnName($column)
    {
        $map = [
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
        
        return $map[$column] ?? $column;
    }
    
    protected function getColumnValue($product, $column)
    {
        try {
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
                    return $product->created_at ? $product->created_at->format('d.m.Y H:i:s') : '';
                    
                case 'updated_at':
                    return $product->updated_at ? $product->updated_at->format('d.m.Y H:i:s') : '';
                    
                default:
                    return $product->{$column} ?? '';
            }
        } catch (\Exception $e) {
            Log::warning('Error getting column value:', [
                'column' => $column,
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
            return '';
        }
    }
    
    protected function createNotification($userId, $downloadUrl, $filename, $count, $error = null)
    {
        try {
            if ($error) {
                $title = 'Ошибка экспорта товаров';
                $message = "Произошла ошибка: {$error}";
                $type = 'export_failed';
            } else {
                $title = 'Экспорт товаров завершен';
                $message = "Экспортировано {$count} товаров";
                if ($downloadUrl) {
                    $message .= ". Файл {$filename} готов к скачиванию";
                }
                $type = 'export_completed';
            }
            
            DB::table('notifications')->insert([
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'link' => $downloadUrl,
                'data' => json_encode([
                    'filename' => $filename,
                    'file_url' => $downloadUrl,
                    'count' => $count,
                    'exported_at' => now()->toDateTimeString(),
                    'error' => $error
                ]),
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            Log::info('Notification created:', [
                'user_id' => $userId,
                'type' => $type,
                'count' => $count
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to create notification:', [
                'error' => $e->getMessage()
            ]);
        }
    }
    
    public function failed(\Throwable $exception)
    {
        Log::error('Job marked as failed:', [
            'job' => get_class($this),
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
        
        // Уведомление уже создано в основном методе handle()
    }
}