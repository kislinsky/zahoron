<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем данные из старой таблицы \areas\
        $oldData = DB::connection('zahoron_old')->table('category_products')->get();

        // Перебираем данные и вставляем их в новую таблицу
        foreach ($oldData as $item) {
            DB::table('category_products')->insert([
                'id'             => $item->id, // Сохранение ID
                'title'          => $item->title, 
                'parent_id'      => $item->parent_id ?? null,
                'icon'           => $item->icon ?? null,
                'icon_white'     => $item->icon_white ?? null,
                'white_icon'     => $item->white_icon ?? null,
                'content'        => $item->content ?? null,
                'manual'         => $item->manual ?? null,
                'manual_video'   => $item->manual_video ?? null,
                'type'           => $item->type ?? 'beautification', // Значение по умолчанию
                'additional'     => $item->additional ?? null,
                'choose_admin'   => $item->choose_admin ?? 0, // Значение по умолчанию
                'slug'           => $item->slug ?? '', // Обязательно должно быть заполнено
                'icon_map'       => $item->icon_map ?? null,  
                'created_at'     => $item->created_at ?? now(), 
                'updated_at'     => $item->updated_at ?? now(),
            ]);
        }
    }
}
