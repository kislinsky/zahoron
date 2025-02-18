<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем данные из старой таблицы \areas\
        $oldData = DB::connection('zahoron_old')->table('images')->get();

        // Переносим данные в \images\
        foreach ($oldData as $item) {
            DB::table('images')->insert([
                'id'         => $item->id, // Если ID должен сохраняться (уберите, если автоинкремент)
                'title'      => $item->title ?? 'Без названия', // Установлено значение по умолчанию
                'created_at' => $item->created_at ?? now(),
                'updated_at' => $item->updated_at ?? now(),
            ]);
        }
    }
}
