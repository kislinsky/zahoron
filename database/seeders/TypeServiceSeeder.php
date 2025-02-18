<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем данные из старой таблицы `old_type_services`
        $oldData = DB::connection('zahoron_old')->table('type_services')->get();

        // Переносим данные в таблицу `type_services`
        foreach ($oldData as $item) {
            DB::table('type_services')->insert([
                'id'=> $item->id,

                'title' => $item->title ?? 'Название услуги', // Значение по умолчанию
                'title_ru' => $item->title_ru ?? 'Название услуги на русском', // Значение по умолчанию
                'type_application_id' => $item->type_application_id ?? 1, // Значение по умолчанию
                'created_at' => $item->created_at ?? now(), // Текущее время, если поле отсутствует
                'updated_at' => $item->updated_at ?? now(), // Текущее время, если поле отсутствует
            ]);
        }
    }
}
