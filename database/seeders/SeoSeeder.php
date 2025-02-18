<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем данные из таблицы 'pages' (или другой таблицы-источника, если надо)
        $oldData = DB::connection('zahoron_old')->table('s_e_o_s')->get();

        // Заполнение таблицы s_e_o_s
        foreach ($oldData as $item) {
            DB::table('s_e_o_s')->insert([
                'id'=>$item->id,
                'page'          => $item->page ?? 'default-page', // Если null, то 'default-page'
                'name'          => $item->name  ?? 'Без имени', // Если title_ru нет, используем title
                'title'         => $item->title ?? 'Неизвестный заголовок',
                'content'       =>  $item->content, // Дефолтный контент, если ничего нет
                'seo_object_id' => $item->seo_object_id, // Связь с seo_objects (из какой таблицы берем? Уточните!)
                'created_at'    => $item->created_at ?? now(),
                'updated_at'    => $item->updated_at ?? now(),
            ]);
        }
    }
}
