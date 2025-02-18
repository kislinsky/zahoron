<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MortuarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем данные из старой таблицы
        $oldData = DB::connection('zahoron_old')->table('mortuaries')->get();

        // Переносим данные в новую таблицу mortuaries
        foreach ($oldData as $item) {
            DB::table('mortuaries')->insert([
                'id'=> $item->id,

                'title'           => $item->title,
                'city_id'         => $item->city_id ?? 1, // Если NULL, подставляем 1 или другой дефолтный город
                'width'           => $item->width ?? null,
                'longitude'       => $item->longitude ?? null,
                'content'         => $item->content ?? '',
                'mini_content'    => $item->mini_content ?? null,
                'img_url'         => $item->img_url ?? null,
                'img_file'        => $item->img_file ?? null,
                'adres'           => $item->adres ?? '',
                'rating'          => $item->rating ?? null,
                'next_to'         => $item->next_to ?? null,
                'underground'     => $item->underground ?? null,
                'characteristics' => $item->characteristics ?? null,
                'district_id'     => $item->district_id ?? null,
                'phone'           => $item->phone ?? null,
                'href_img'        => $item->href_img ?? 0,
                'village'         => $item->village ?? null,
                'email'           => $item->email ?? null,
                'time_difference' => $item->time_difference ?? 0,
                'created_at'      => $item->created_at ?? now(),
                'updated_at'      => $item->updated_at ?? now(),
            ]);
        }
    }
}
