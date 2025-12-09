<?php

namespace App\Models;

use App\Jobs\CloseApplicationJob;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Beautification extends Model
{
    use HasFactory;
    protected $guarded =[];

    function city(){
        return $this->belongsTo(City::class);
    }

    function cemetery(){
        return $this->belongsTo(Cemetery::class);
    }

    function categoryPriceList(){
        return CategoryProductPriceList::whereIn('id',json_decode($this->products_id))->get();
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
    
    function products(){
        return CategoryProductPriceList::whereIn('id',json_decode($this->products_id))->get();
    }




    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($beautification) {

 
            // Получаем все организации, которые могут принять заявку
            $organizations = selectCity()->organizations;
            
            // Создаем уведомление для каждой организации
            foreach ($organizations as $organization) {
                \App\Models\Notification::create([
                    'user_id' => null,
                    'organization_id' => $organization->id,
                    'type' => 'beautification_new',
                    'title' => 'Новая заявка на благоустройство',
                    'message' => "Поступила новая заявка на благоустройство #{$beautification->id}",
                    'is_read' => false,
                    'data' => json_encode([
                        'beautification_id' => $beautification->id,
                        'status' => 'pending'
                    ])
                    
                ]);
            }
            
            // Уведомление для пользователя
            \App\Models\Notification::create([
                'user_id' => $beautification->user_id,
                'organization_id' => null,
                'type' => 'beautification_created',
                'title' => 'Заявка на благоустройство создана',
                'message' => "Ваша заявка на благоустройство #{$beautification->id} успешно создана",
                'is_read' => false,
                'data' => json_encode([
                    'beautification_id' => $beautification->id,
                    'status' => 'created'
                ])
               
            ]);
            
            // РАСЧЕТ ВРЕМЕНИ УДАЛЕНИЯ ЗАЯВКИ
            $delayTime = now()->addMinutes(30); // По умолчанию 30 минут
            
            // Если есть call_time в будущем
            if ($beautification->call_time) {
                try {
                    $callTime = Carbon::parse($beautification->call_time);
                    
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
                    Log::error('Error parsing call_time: ' . $e->getMessage());
                }
            }
            
           
            
            // Запускаем задачу на закрытие
            CloseApplicationJob::dispatch($beautification)->delay($delayTime);

            sendMessage('soobshhenie-pri-zaiavke-pop-up-oblogorazivanie',[],$beautification->user);
            sendMessagesOrganizations(selectCity()->organizations,'sms-soobshhenie-dlia-organizacii-pri-zaiavke-oblagorazivaniia','email-soobshhenie-dlia-organizacii-pri-zaiavke-oblagorazivaniia');
        
            
        });
        
        static::updated(function ($beautification) {
            // Если заявку приняла организация
            if ($beautification->isDirty('organization_id') && $beautification->organization_id) {
              
                
                // Уведомляем пользователя
                \App\Models\Notification::create([
                    'user_id' => $beautification->user_id,
                    'organization_id' => null,
                    'type' => 'beautification_accepted',
                    'title' => 'Заявка на благоустройство принята',
                    'message' => "Вашу заявку на благоустройство #{$beautification->id} приняла организация",
                    'is_read' => false,
                    'data' => json_encode([
                        'beautification_id' => $beautification->id,
                        'organization_id' => $beautification->organization_id,
                        'status' => 'accepted'
                    ])
                ]);
                
                $searchPattern = '%"beautification_id":' . $beautification->id . '%';
                                
                $deletedCount = \App\Models\Notification::where('type', 'beautification_new')
                    ->where('data', 'LIKE', $searchPattern)
                    ->delete();

            }
            
            // При изменении статуса
            if ($beautification->isDirty('status')) {
                \App\Models\Notification::create([
                    'user_id' => $beautification->user_id,
                    'organization_id' => $beautification->organization_id,
                    'type' => 'beautification_status',
                    'title' => 'Статус заявки изменен',
                    'message' => "Статус вашей заявки на благоустройство #{$beautification->id} изменен на: {$beautification->status}",
                    'is_read' => false,
                    'data' => json_encode([
                        'beautification_id' => $beautification->id,
                        'old_status' => $beautification->getOriginal('status'),
                        'new_status' => $beautification->status
                    ])
                ]);
            }
        });
    }


}
