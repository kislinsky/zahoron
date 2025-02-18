<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем данные из старой таблицы \areas\
        $oldData = DB::connection('zahoron_old')->table('category_services')->get();

        // Переносим данные в новую таблицу \category_services\
        foreach ($oldData as $item) {
            DB::table('category_services')->insert([
                'id'         => $item->id, // Сохранение ID (если используется автоинкремент, можно убрать)
                'title'      => $item->title,
                'method'     => '', // Значение по умолчанию (так как в старых данных этого поля нет),
                'created_at' => $item->created_at ?? now(),
                'updated_at' => $item->updated_at ?? now(),
            ]);
        }
    }
}
