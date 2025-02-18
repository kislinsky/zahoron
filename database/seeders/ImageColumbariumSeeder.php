<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImageColumbariumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем данные из старой таблицы \areas\
        $oldData = DB::connection('zahoron_old')->table('image_columbaria')->get();

        // Переносим данные в \image_cemeteries\
        foreach ($oldData as $item) {
            DB::table('image_columbaria')->insert([
                'id'          => $item->id, // Если ID должен сохраняться (уберите, если автоинкремент)
                'title'       => $item->title, // Если title отсутствует, добавляем заглушку
                'columbarium_id' => $item->columbarium_id , // Укажите корректный ID кладбища (см. ниже "ВАЖНО")
                'href_img'    =>  $item->href_img, // По умолчанию, так как в \areas\ нет изображений
                'created_at'  => $item->created_at ?? now(),
                'updated_at'  => $item->updated_at ?? now(),
            ]);
        }
    }
}
