<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImageServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем данные из старой таблицы \areas\
        $oldData = DB::connection('zahoron_old')->table('image_services')->get();

        // Переносим данные в \image_services\
        foreach ($oldData as $item) {
            DB::table('image_services')->insert([
                'id'         => $item->id, // Если ID должен сохраняться (уберите, если автоинкремент)
                'service_id' => 1, // Укажите корректный ID услуги (важно, см. "ВАЖНО!" ниже)
                'img_before' => $item->img_before, // Укажите путь к изображению "до" (заглушка)
                'img_after'  => $item->img_after, // Укажите путь к изображению "после" (заглушка)
                'created_at' => $item->created_at ?? now(),
                'updated_at' => $item->updated_at ?? now(),
            ]);
        }
    }
}
