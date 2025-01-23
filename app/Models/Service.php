<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $guarded =[];

    function priceService(){
        return $this->hasMany(PriceService::class);
    }

    // В модели Service
    public function getPriceForCemetery($cemeteryId)
    {
        return $this->priceService->where('cemetery_id', $cemeteryId)->first();
    }


}
