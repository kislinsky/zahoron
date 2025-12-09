<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentProduct extends Model
{
    use HasFactory;
    protected $guarded =[];

    function product(){
        return $this->belongsTo(Product::class);
    }

    function organization(){
        return $this->belongsTo(Organization::class);
    }

    function btnReviewAccept(){
        if($this->status==0){
            $route=route('account.agency.review.product.accept',$this->id);
            return "<a href='$route' class='blue_btn'>Одобрить</a>";
        }

        elseif($this->status==1){
            return "<div content='$this->content' id_review='$this->id' class='blue_btn open_review_update_content_form'>Редактировать</div>";
        }
    }


    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($comment) {
            // Для организации о новом комментарии
            Notification::create([
                'user_id' => null,
                'organization_id' => $comment->organization_id,
                'type' => 'comment',
                'title' => 'Новый комментарий',
                'message' => "Поступил новый комментарий к товару",
                'is_read' => false
            ]);
        
        });
    }
}
