<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewCemetery extends Model
{
    use HasFactory;
    protected $guarded =[];


    function cemetery(){
        return $this->belongsTo(Cemetery::class);
    }
    
    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($review) {

            
            // Для админа
            Notification::create([
                'user_id' => admin()->id,
                'organization_id' => null,
                'type' => 'review_cemetery_admin',
                'title' => 'Новый отзыв о кладбище',
                'message' => "Создан новый отзыв о кладбище",
                'is_read' => false
            ]);
        });
        
    }
}
