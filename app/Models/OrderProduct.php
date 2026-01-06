<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory;
    protected $guarded =[];


    function product(){
        return $this->belongsTo(Product::class);
    }

    function additionals(){
        if($this->additional!=null){
            return AdditionProduct::whereIn('id',json_decode($this->additional))->get();
        }
        return null;
    }

    function user(){
        return $this->belongsTo(User::class);
    }


    function cemetery(){
        return $this->belongsTo(Cemetery::class);
    }

    function mortuary(){
        return $this->belongsTo(Mortuary::class);
    }

    function organization(){
        return $this->belongsTo(Organization::class);
    }
   

    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($order) {


            sendMessage('sms-soobshhenie-user-pri-zaiavke-produkta',['name'=>$order->user->name],$order->user);
            sendMessage('sms-soobshhenie-pri-zaiavke-produkta',[],$order->organization);
            sendMessage('email-soobshhenie-pri-zaiavke-produkta',[],$order->organization);

            // Для организации о новом заказе товара
            Notification::create([
                'user_id' => null,
                'organization_id' => $order->organization_id,
                'type' => 'order_product',
                'title' => 'Новый заказ товара',
                'message' => "Поступил новый заказ товара",
                'is_read' => false
            ]);
            
            // Для пользователя
            Notification::create([
                'user_id' => $order->user_id,
                'organization_id' => $order->organization_id,
                'type' => 'order_product',
                'title' => 'Заказ товара создан',
                'message' => "Ваш заказ товара успешно создан",
                'is_read' => false
            ]);

              
            // Для админа
            Notification::create([
                'user_id' => admin()->id,
                'organization_id' => null,
                'type' => 'order_product_admin',
                'title' => 'Заказ товара создан',
                'message' => "заказ товара успешно создан",
                'is_read' => false
            ]);
        });
        
        static::updated(function ($order) {
            // При изменении статуса заказа
            if ($order->isDirty('status')) {
                Notification::create([
                    'user_id' => $order->user_id,
                    'organization_id' => $order->organization_id,
                    'type' => 'order_product_status',
                    'title' => 'Статус заказа изменен',
                    'message' => "Статус вашего заказа товара изменен на: {$order->status}",
                    'is_read' => false
                ]);
            }
        });
    }
}
