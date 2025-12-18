<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;
    protected $guarded =[];

    public function urlImgBefore(){
        return asset('storage/'.$this->img_before);
    }

    public function urlImgAfter(){
        return asset('storage/'.$this->img_after);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ReviewCategory::class, 'review_category_id');
    }
}
