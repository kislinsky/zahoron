<?php

namespace App\Models;

use App\Traits\HasPhone;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpCodes extends Model
{
    use HasFactory,HasPhone;
    protected $guarded =[];
    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = $this->normalizePhone($value);
    }

}
