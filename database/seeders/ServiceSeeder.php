<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем данные из старой таблицы `old_services`
        $oldData = DB::connection('zahoron_old')->table('services')->get();

        // Переносим данные в таблицу `services`
        foreach ($oldData as $item) {
            DB::table('services')->insert([
                'id'=> $item->id,

                'title'              => $item->title ?? 'Название услуги', // Значение по умолчанию
                'content'            => $item->content ?? 'Описание услуги', // Значение по умолчанию
                'category_id'        => $item->category_id ?? 1, // Значение по умолчанию
                'cemetery_id'       => $item->cemetery_id ?? 1, // Значение по умолчанию
                'text_under_title'   => $item->text_under_title ?? null,
                'video_1'            => $item->video_1 ?? null,
                'text_under_video_1' => $item->text_under_video_1 ?? null,
                'text_under_img'     => $item->text_under_img ?? null,
                'text_sale'          => $item->text_sale ?? null,
                'text_stages'        => $item->text_stages ?? null,
                'video_2'            => $item->video_2 ?? null,
                'img_structure'      => $item->img_structure ?? null,
                'created_at'         => $item->created_at ?? now(), // Текущее время, если поле отсутствует
                'updated_at'         => $item->updated_at ?? now(), // Текущее время, если поле отсутствует
            ]);
        }
    }
}
