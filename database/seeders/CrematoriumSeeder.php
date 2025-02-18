<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CrematoriumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $oldData = DB::connection('zahoron_old')->table('crematoria')->get(); // Предположим, что таблица из которой мы берем данные называется crematoria_old
    
        // Переносим данные в новую таблицу crematoria
        foreach ($oldData as $item) {
            DB::table('crematoria')->insert([
                'id'=> $item->id,

                'id'                  => $item->id, // Если id должен быть сохранен
                'title'               => $item->title,
                'city_id'            => $item->city_id, // Убедитесь, что это значение корректно
                'width'               => $item->width ?? '',
                'longitude'           => $item->longitude ?? '',
                'content'             => $item->content ?? '',
                'mini_content'        => $item->mini_content ?? '',
                'img_url'            => $item->img_url ?? null,
                'img_file'           => $item->img_file ?? null,
                'adres'               => $item->adres ?? '',
                'rating'              => $item->rating ?? null,
                'time_start_work'     => $item->time_start_work ?? null,
                'time_end_work'       => $item->time_end_work ?? null,
                'next_to'            => $item->next_to ?? null,
                'underground'         => $item->underground ?? null,
                'characteristics'     => $item->characteristics ?? null,
                'phone'               => $item->phone ?? null,
                'href_img'           => $item->href_img ?? 0,
                'village'             => $item->village ?? null,
                'email'               => $item->email ?? null,
                'time_difference'     => $item->time_difference ?? 0,
                'created_at'          => $item->created_at,
                'updated_at'          => $item->updated_at,
            ]);
        }
    }
}
