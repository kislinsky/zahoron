<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ColumbariumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем данные из старой таблицы columbaria
        $oldData = DB::connection('zahoron_old')->table('columbaria')->get();

        // Переносим данные в новую таблицу columbaria
        foreach ($oldData as $item) {
            DB::table('columbaria')->insert([
                'id'=> $item->id,

                'title'            => $item->title,
                'city_id'          => $item->city_id ?? 1, // Проверяем, есть ли ID города
                'width'            => $item->width ?? '',
                'longitude'        => $item->longitude ?? '',
                'content'          => $item->content ?? null,
                'mini_content'     => $item->mini_content ?? null,
                'img_url'          => $item->img_url ?? null,
                'img_file'         => $item->img_file ?? null,
                'adres'            => $item->adres ?? '',
                'rating'           => $item->rating ?? null,
                'time_start_work'  => $item->time_start_work ?? '00:00',
                'time_end_work'    => $item->time_end_work ?? '23:59',
                'next_to'          => $item->next_to ?? null,
                'underground'      => $item->underground ?? null,
                'characteristics'  => $item->characteristics ?? null,
                'phone'            => $item->phone ?? null,
                'href_img'         => $item->href_img ?? 0,
                'time_difference'  => $item->time_difference ?? 0,
                'created_at'       => $item->created_at ?? now(),
                'updated_at'       => $item->updated_at ?? now(),
            ]);
        }
    }
}
