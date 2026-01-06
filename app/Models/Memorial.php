<?php

namespace App\Models;

use App\Jobs\CloseApplicationJob;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Memorial extends Model
{
    use HasFactory;
    protected $guarded =[];

    function city(){
        return $this->belongsTo(City::class);
    }

    function district(){
        return $this->belongsTo(District::class);
    }

    function user(){
        return $this->belongsTo(User::class);
    }


    function organization(){
        return $this->belongsTo(Organization::class);
    }
    
    function changeStatus($status){
        if($this->status==0 && $this->organization==null){
            $this->update(['status'=>$status]);
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
        
        static::created(function ($memorial) {

            // Получаем все организации, которые могут принять заявку
            $organizations = selectCity()->organizations;
            
            // Создаем уведомление для каждой организации
            foreach ($organizations as $organization) {
                \App\Models\Notification::create([
                    'user_id' => null,
                    'organization_id' => $organization->id,
                    'type' => 'memorial_new',
                    'title' => 'Новая заявка на мемориал',
                    'message' => "Поступила новая заявка на мемориал #{$memorial->id}",
                    'is_read' => false,
                    'data' => json_encode([
                        'memorial_id' => $memorial->id,
                        'status' => 'pending'
                    ])
                ]);
            }
            
            // Уведомление для пользователя
            \App\Models\Notification::create([
                'user_id' => $memorial->user_id,
                'organization_id' => null,
                'type' => 'memorial_created',
                'title' => 'Заявка на мемориал создана',
                'message' => "Ваша заявка на мемориал #{$memorial->id} успешно создана",
                'is_read' => false,
                'data' => json_encode([
                    'memorial_id' => $memorial->id,
                    'status' => 'created'
                ])
            ]);

            // Уведомление для администратора
            \App\Models\Notification::create([
                'user_id' => admin()->id,
                'organization_id' => null,
                'type' => 'memorial_created_admin',
                'title' => 'Заявка на мемориал создана',
                'message' => "Новая заявка на мемориал #{$memorial->id}",
                'is_read' => false,
                'data' => json_encode([
                    'memorial_id' => $memorial->id,
                    'status' => 'created'
                ])
            ]);
            
            // РАСЧЕТ ВРЕМЕНИ УДАЛЕНИЯ ЗАЯВКИ
            $delayTime = now()->addMinutes(30); // По умолчанию 30 минут
            
            // Если есть call_time в будущем
            if ($memorial->call_time) {
                try {
                    $callTime = Carbon::parse($memorial->call_time);
                    
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
                    Log::error('Error parsing call_time for memorial: ' . $e->getMessage());
                }
            }
            
            // Запускаем задачу на закрытие
            CloseApplicationJob::dispatch($memorial)->delay($delayTime);

            sendMessage('soobshhenie-pri-zaiavke-pop-up-pominki-dlia-polzovatelia', [], $memorial->user);
            sendMessagesOrganizations(selectCity()->organizations, 'sms-soobshhenie-pri-zaiavke-pop-ap-pominok-dlia-organizacii', 'email-soobshhenie-pri-pop-ap-zaiavke-pominok-dlia-organizacii');
            sendSms(admin()->phone, "Новая заявка на поминки #{$memorial->id}");
            
        });
        
        static::updated(function ($memorial) {
            // Если заявку приняла организация
            if ($memorial->isDirty('organization_id') && $memorial->organization_id) {
                
                // Уведомляем пользователя
                \App\Models\Notification::create([
                    'user_id' => $memorial->user_id,
                    'organization_id' => null,
                    'type' => 'memorial_accepted',
                    'title' => 'Заявка на поминки принята',
                    'message' => "Вашу заявку на поминки #{$memorial->id} приняла организация",
                    'is_read' => false,
                    'data' => json_encode([
                        'memorial_id' => $memorial->id,
                        'organization_id' => $memorial->organization_id,
                        'status' => 'accepted'
                    ])
                ]);
                
                $searchPattern = '%"memorial_id":' . $memorial->id . '%';
                                
                $deletedCount = \App\Models\Notification::where('type', 'memorial_new')
                    ->where('data', 'LIKE', $searchPattern)
                    ->delete();
            }
            
            // При изменении статуса
            if ($memorial->isDirty('status')) {
                \App\Models\Notification::create([
                    'user_id' => $memorial->user_id,
                    'organization_id' => $memorial->organization_id,
                    'type' => 'memorial_status',
                    'title' => 'Статус заявки изменен',
                    'message' => "Статус вашей заявки на поминки #{$memorial->id} изменен на: {$memorial->status}",
                    'is_read' => false,
                    'data' => json_encode([
                        'memorial_id' => $memorial->id,
                        'old_status' => $memorial->getOriginal('status'),
                        'new_status' => $memorial->status
                    ])
                ]);
            }
        });
    }

}
