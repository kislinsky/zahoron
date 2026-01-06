<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderBurial extends Model
{
    use HasFactory;
    protected $guarded =[];

    function burial(){
        return $this->belongsTo(Burial::class);
    }

    function user(){
        return $this->belongsTo(User::class);
    }
    
    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($order) {

            sendMessage('pokupka-geolokacii-zaxoroneniia',[],$order->user);
            // Для пользователя
            Notification::create([
                'user_id' => $order->user_id,
                'organization_id' => null,
                'type' => 'order_burial',
                'title' => 'Заказ геолокации создан',
                'message' => "Ваш заказ геолокации успешно создан",
                'is_read' => false
            ]);

            
            // Для админа
            Notification::create([
                'user_id' => admin()->id,
                'organization_id' => null,
                'type' => 'order_burial_admin',
                'title' => 'Заказ геолокации создан',
                'message' => "заказ геолокации успешно создан",
                'is_read' => false
            ]);
        });
        
    }
}
