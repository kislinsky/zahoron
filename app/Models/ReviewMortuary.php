<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewMortuary extends Model
{
    use HasFactory;
    protected $guarded =[];


    function mortuary(){
        return $this->belongsTo(Mortuary::class);
    }

    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($review) {

            
            // Для админа
            Notification::create([
                'user_id' => admin()->id,
                'organization_id' => null,
                'type' => 'review_mortuary_admin',
                'title' => 'Новый отзыв о морге',
                'message' => "Создан новый отзыв о морге",
                'is_read' => false
            ]);
        });
        
    }
}
