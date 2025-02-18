<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем данные из старой таблицы `old_type_applications`
        $oldData = DB::connection('zahoron_old')->table('type_applications')->get();

        // Переносим данные в таблицу `type_applications`
        foreach ($oldData as $item) {
            DB::table('type_applications')->insert([
                'id'=> $item->id,

                'title' => $item->title ?? 'Название заявки', // Значение по умолчанию
                'title_ru' => $item->title_ru ?? 'Название заявки', // Значение по умолчанию
                'created_at' => $item->created_at ?? now(), // Текущее время, если поле отсутствует
                'updated_at' => $item->updated_at ?? now(), // Текущее время, если поле отсутствует
            ]);
        }
    }
}
