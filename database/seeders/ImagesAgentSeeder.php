<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImagesAgentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем данные из старой таблицы \areas\
        $oldData = DB::connection('zahoron_old')->table('image_agents')->get();

        // Переносим данные в \image_agents\
        foreach ($oldData as $item) {
            DB::table('image_agents')->insert([
                'id'         => $item->id, // Если ID должен сохраняться (уберите, если автоинкремент)
                'title'       => $item->title, // Если title отсутствует, добавляем заглушку
                'user_id'    => $item->user_id ?? 1, // Укажите корректный ID пользователя (см. "ВАЖНО" ниже)
                'created_at' => $item->created_at ?? now(),
                'updated_at' => $item->updated_at ?? now(),
            ]);
        }
    }
}
