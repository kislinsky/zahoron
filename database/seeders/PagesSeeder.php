<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем данные из старой таблицы \areas\
        $oldData = DB::connection('zahoron_old')->table('pages')->get();

        // Переносим данные в \pages\
        foreach ($oldData as $item) {
            DB::table('pages')->insert([
                'id'         => $item->id, // Сохранение ID
                'title'      => $item->title ?? 'Неизвестная страница', // Значение по умолчанию
                'title_ru'   => $item->title_ru ?? $item->title ?? 'Неизвестная страница', // Если \title_ru\ отсутствует, используем \title\
                'created_at' => $item->created_at ?? now(),
                'updated_at' => $item->updated_at ?? now(),
            ]);
        }
    }
}
