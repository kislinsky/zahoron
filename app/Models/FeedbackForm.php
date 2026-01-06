<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedbackForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic',
        'question',
        'name',
        'phone'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($feedback) {

            Notification::create([
                'user_id' => admin()->id,
                'organization_id' => null,
                'type' => 'feedback_admin',
                'title' => 'Новый заявка обратной связи',
                'message' => "Поступила новая заявка обратной связи",
                'is_read' => false
            ]);
        
        });
        
    }
}