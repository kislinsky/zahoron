<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImageAgenciesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем данные из старой таблицы `areas`
        $oldData = DB::connection('zahoron_old')->table('image_agencies')->get();

        // Переносим данные в новую таблицу `image_agencies`
        foreach ($oldData as $item) {
            DB::table('image_agencies')->insert([
                'id'         => $item->id, // Если ID должен сохраняться (уберите, если автоинкремент)
                'title'      => $item->title,
                'user_id'    => $item->user_id, // Устанавливаем значение user_id (ЗАМЕНИТЕ на реальное значение или логику)
                'created_at' => $item->created_at ?? now(),
                'updated_at' => $item->updated_at ?? now(),
            ]);
        }
    }
}
