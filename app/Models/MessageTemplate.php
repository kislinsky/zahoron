<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageTemplate extends Model
{
    use HasFactory;

    protected $guarded =[];

    public function compile(array $data): string
    {
        $compiled = $this->template;
        
        foreach ($data as $key => $value) {
            $compiled = str_replace("{{{$key}}}", $value, $compiled);
        }
        
        return $compiled;
    }

    public static function getBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->first();
    }
}
