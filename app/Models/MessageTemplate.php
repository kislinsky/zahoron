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
            // Обрабатываем все возможные форматы
            $patterns = [
                "/\{\{\s*$key\s*\}\}/",    // {{ key }}
                "/\{\{\{$key\}\}\}/",       // {{{key}}}
            ];
            
            foreach ($patterns as $pattern) {
                $compiled = preg_replace($pattern, $value, $compiled);
            }
        }
        
        // Дополнительно: удаляем оставшиеся необработанные переменные
        $compiled = preg_replace('/\{\{[^}]+\}\}/', '', $compiled);
        $compiled = preg_replace('/\{\{\{[^}]+\}\}\}/', '', $compiled);
        
        return $compiled;
    }
    public static function getBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->first();
    }
}
