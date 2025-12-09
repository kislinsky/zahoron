<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CloseApplicationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $application;

    public function __construct($application)
    {
        $this->application = $application;
    }

   public function handle(): void
{
    try {
        Log::info('=== CLOSE APPLICATION JOB STARTED ===');
        Log::info('Application ID: ' . $this->application->id);
        Log::info('Application Type: ' . get_class($this->application));
        
        // Определяем тип заявки
        $type = $this->getApplicationType();
        
        if (!$type) {
            Log::error('Cannot determine application type for ID: ' . $this->application->id);
            return;
        }
        
        Log::info('Detected Type: ' . $type);

        // Проверяем, не была ли уже принята заявка
        if (!$this->application->organization_id) {
            Log::info('Application NOT accepted yet, proceeding...');
            
            $config = $this->getNotificationConfig($type);
            
            if ($config) {
                Log::info('Config found: ' . json_encode($config));
                
                // 1. Удаляем уведомления для ЭТОЙ КОНКРЕТНОЙ заявки
                // Используем id_field из конфигурации
                $idField = $config['id_field']; // beautification_id, funeral_service_id, dead_application_id
                $searchPattern = '%"' . $idField . '":' . $this->application->id . '%';
                
                Log::info('Searching with pattern: ' . $searchPattern);
                
                $deletedCount = \App\Models\Notification::where('type', $config['new'])
                    ->where('data', 'LIKE', $searchPattern)
                    ->delete();
                
                Log::info("Deleted {$deletedCount} notifications for application #{$this->application->id}");
                
                // 2. Уведомляем пользователя
                \App\Models\Notification::create([
                    'user_id' => $this->application->user_id,
                    'organization_id' => null,
                    'type' => $config['expired'],
                    'title' => "Время {$config['title']} истекло",
                    'message' => "Время для принятия вашей {$config['title']} #{$this->application->id} истекло",
                    'is_read' => false,
                    'data' => json_encode([
                        $config['id_field'] => $this->application->id,
                        'status' => 4
                    ])
                ]);
                
                Log::info('Expired notification created for user');
            } else {
                Log::error('No config found for type: ' . $type);
            }
            
            // 3. Меняем статус заявки
            $this->application->update(['status' => 4]);
            
            Log::info("Application #{$this->application->id} status changed to 4 (expired)");
            
        } else {
            Log::info("Application #{$this->application->id} already accepted, job cancelled");
        }
        
        Log::info('=== CLOSE APPLICATION JOB COMPLETED ===');
        
    } catch (\Exception $e) {
        Log::error('Job Error: ' . $e->getMessage());
        Log::error('Trace: ' . $e->getTraceAsString());
        throw $e;
    }
}

    /**
     * Определяем тип заявки по классу модели
     */
    private function getApplicationType()
    {
        $className = get_class($this->application);
        
        $typeMap = [
            'App\\Models\\Beautification' => 'beautification',
            'App\\Models\\FuneralService' => 'funeral_service',
            'App\\Models\\DeadApplication' => 'dead_application',
        ];
        
        return $typeMap[$className] ?? null;
    }

    /**
     * Получаем конфигурацию уведомлений для типа
     */
    private function getNotificationConfig($type)
    {
        $configs = [
            'beautification' => [
                'new' => 'beautification_new',
                'expired' => 'beautification_expired',
                'title' => 'Заявка на благоустройство',
                'id_field' => 'beautification_id'
            ],
            'funeral_service' => [
                'new' => 'funeral_service_new',
                'expired' => 'funeral_service_expired',
                'title' => 'Заявка на ритуальную услугу',
                'id_field' => 'funeral_service_id'
            ],
            'dead_application' => [
                'new' => 'dead_application_new',
                'expired' => 'dead_application_expired',
                'title' => 'Заявка по умершему',
                'id_field' => 'dead_application_id'
            ],
        ];
        
        return $configs[$type] ?? null;
    }
}