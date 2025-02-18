<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AcfsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем данные из старой таблицы `acfs`
        $oldData = DB::connection('zahoron_old')->table('acfs')->get();

        // Переносим данные в новую таблицу `acfs`
        foreach ($oldData as $item) {
            DB::table('acfs')->insert([
                'id'         => $item->id, // Сохранение старого ID
                'name'       => $item->name,
                'content'    => $item->content,
                'type'       => $item->type ?? null, // Если поле type отсутствует, используем null
                'page_id'    => $item->page_id ?? 1, // Если page_id отсутствует, используем значение по умолчанию
                'created_at' => $item->created_at ?? now(), // Текущее время, если поле отсутствует
                'updated_at' => $item->updated_at ?? now(), // Текущее время, если поле отсутствует
            ]);
        }
    }
}
