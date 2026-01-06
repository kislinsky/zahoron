<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewsOrganization extends Model
{
    use HasFactory;
    protected $guarded =[];

    function organization(){
        return $this->belongsTo(Organization::class);
    }

    function city(){
        return $this->belongsTo(City::class);
    }

    function btnReviewAccept(){
        if($this->status==0){
            $route=route('account.agency.review.organization.accept',$this->id);
            return "<a href='$route' class='blue_btn'>Одобрить</a>";
        }
        elseif($this->status==1){
            return "<div content='$this->content' id_review='$this->id' class='blue_btn open_review_update_content_form'>Редактировать</div>";
        }
    }


    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($review) {


        $message="ID организации:".$review->organization_id."n Рейтинг:".$review->rating." \n Сообщение:".$review->content;

            // Для организации о новом отзыве
            Notification::create([
                'user_id' => null,
                'organization_id' => $review->organization_id,
                'type' => 'review',
                'title' => 'Новый отзыв об организации',
                'message' => "Поступил новый отзыв об организации",
                'is_read' => false
            ]);

            // Для админа
            Notification::create([
                'user_id' => admin()->id,
                'organization_id' =>  $review->organization_id,
                'type' => 'review_cemetery_admin',
                'title' => 'Новый отзыв об организации',
                'message' => "Создан новый отзыв об организации",
                'is_read' => false
            ]);
            
        });
        
        static::updated(function ($review) {
            // При изменении статуса отзыва (модерация)
            if ($review->isDirty('status')) {
                // Уведомление для автора отзыва
                Notification::create([
                    'user_id' => $review->user_id,
                    'organization_id' => $review->organization_id,
                    'type' => 'review_status',
                    'title' => 'Статус отзыва изменен',
                    'message' => "Статус вашего отзыва об организации изменен на: {$review->status}",
                    'is_read' => false
                ]);
            }
        });
    }
}
