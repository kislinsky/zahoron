<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuneralService extends Model
{
    use HasFactory;
    protected $guarded =[];

    function city(){
        return $this->belongsTo(City::class);
    }

    function user(){
        return $this->belongsTo(User::class);
    }

    function mortuary(){
        return $this->belongsTo(Mortuary::class);
    }

    function cemetery(){
        return $this->belongsTo(Cemetery::class);
    }

    function cityTo(){
        return City::find($this->city_id_to);
    }

    function organization(){
        return $this->belongsTo(Organization::class);
    }

     function changeStatus($status){
        if($this->status==0 && $this->organization==null){
            $this->update(['status'=>$status]);
            if($status==4){
                sendSms($this->user->phone,"К сожалению ваша заявка не была принята не одной организацией в течении часа.");
            }
        }
    }

    function timeEnd(){
        if($this->call_time!=null){
            $timeFormatRegex = '/^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/';
            // Проверяем, соответствует ли строка регулярному выражению
            if (preg_match($timeFormatRegex, $this->call_time)) {
                $time = Carbon::createFromFormat('H:i', $this->call_time);
                // Добавляем 30 минут
                $time->addMinutes(30);
                // Возвращаем новое время в формате "H:i"
                return $time->format('H:i');
            } else {
                return $this->call_time;
            }  
        }
    }

    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($funeralService) {
            
    
            // Получаем все организации, которые могут принять заявку
            $organizations = selectCity()->organizations;
            
            // Создаем уведомление для каждой организации
            foreach ($organizations as $organization) {
                \App\Models\Notification::create([
                    'user_id' => null,
                    'organization_id' => $organization->id,
                    'type' => 'funeral_service_new',
                    'title' => 'Новая заявка на ритуальную услугу',
                    'message' => "Поступила новая заявка на ритуальную услугу #{$funeralService->id}",
                    'is_read' => false,
                    'data' => json_encode([
                        'funeral_service_id' => $funeralService->id,
                        'status' => 'pending'
                    ])
                ]);
            }
            
            // Уведомление для пользователя
            \App\Models\Notification::create([
                'user_id' => $funeralService->user_id,
                'organization_id' => null,
                'type' => 'funeral_service_created',
                'title' => 'Заявка на ритуальную услугу создана',
                'message' => "Ваша заявка на ритуальную услугу #{$funeralService->id} успешно создана",
                'is_read' => false,
                'data' => json_encode([
                    'funeral_service_id' => $funeralService->id,
                    'status' => 'created'
                ])
            ]);
            
            // РАСЧЕТ ВРЕМЕНИ УДАЛЕНИЯ ЗАЯВКИ
            $delayTime = now()->addMinutes(30); // По умолчанию 30 минут
            
            // Если есть call_time в будущем
            if ($funeralService->call_time) {
                try {
                    $callTime = \Carbon\Carbon::parse($funeralService->call_time);
                    
                    if ($callTime->isFuture()) {
                        // Если время звонка завтра или позже
                        if ($callTime->isTomorrow() || $callTime->gt(now()->addDay())) {
                            // Время до конца завтра + 30 минут
                            $endOfTomorrow = now()->addDay()->endOfDay();
                            $delayTime = $endOfTomorrow->copy()->addMinutes(30);
                        } else {
                            // Время до указанного времени звонка + 30 минут
                            $delayTime = $callTime->copy()->addMinutes(30);
                        }
                    }
                } catch (\Exception $e) {
                    // В случае ошибки парсинга оставляем 30 минут
                    \Illuminate\Support\Facades\Log::error('Error parsing call_time: ' . $e->getMessage());
                }
            }
            

            // Запускаем задачу на закрытие
            \App\Jobs\CloseApplicationJob::dispatch($funeralService)->delay($delayTime);

            //Отправляем SMS и Email
            sendMessage('soobshhenie-pri-zaiavke-pop-up-ritualnye-uslugi', [], $funeralService->user);
            sendMessagesOrganizations(
                selectCity()->organizations,
                'sms-soobshhenie-dlia-organizacii-pri-zaiavke-rit-uslug',
                'email-soobshhenie-dlia-organizacii-pri-zaiavke-rit-uslug'
            );
            
            \Illuminate\Support\Facades\Log::info("FuneralService #{$funeralService->id} created. Will expire at: " . $delayTime->format('Y-m-d H:i:s'));
        });
        
        static::updated(function ($funeralService) {
            // Если заявку приняла организация
            if ($funeralService->isDirty('organization_id') && $funeralService->organization_id) {
               
                
                // Уведомляем пользователя
                \App\Models\Notification::create([
                    'user_id' => $funeralService->user_id,
                    'organization_id' => null,
                    'type' => 'funeral_service_accepted',
                    'title' => 'Заявка на ритуальную услугу принята',
                    'message' => "Вашу заявку на ритуальную услугу #{$funeralService->id} приняла организация",
                    'is_read' => false,
                    'data' => json_encode([
                        'funeral_service_id' => $funeralService->id,
                        'organization_id' => $funeralService->organization_id,
                        'status' => 'accepted'
                    ])
                ]);
                

                $searchPattern = '%"funeral_service_id":' . $funeralService->id . '%';
                                
                $deletedCount = \App\Models\Notification::where('type', 'funeral_service_new')
                    ->where('data', 'LIKE', $searchPattern)
                    ->delete();


                \Illuminate\Support\Facades\Log::info("FuneralService #{$funeralService->id} accepted by organization #{$funeralService->organization_id}. Notifications removed.");
            }
            
            // При изменении статуса
            if ($funeralService->isDirty('status')) {
                \App\Models\Notification::create([
                    'user_id' => $funeralService->user_id,
                    'organization_id' => $funeralService->organization_id,
                    'type' => 'funeral_service_status',
                    'title' => 'Статус заявки изменен',
                    'message' => "Статус вашей заявки на ритуальную услугу #{$funeralService->id} изменен на: {$funeralService->status}",
                    'is_read' => false,
                    'data' => json_encode([
                        'funeral_service_id' => $funeralService->id,
                        'old_status' => $funeralService->getOriginal('status'),
                        'new_status' => $funeralService->status
                    ])
                ]);
            }
        });
    }
}
