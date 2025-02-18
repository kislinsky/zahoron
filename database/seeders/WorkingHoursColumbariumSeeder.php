<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkingHoursColumbariumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем данные из старой таблицы `areas`
        $oldData = DB::connection('zahoron_old')->table('working_hours_columbaria')->get();

        // Переносим данные в `working_hours_cemeteries`
        foreach ($oldData as $item) {
            // Проверяем существование cemetery_id
            $cemeteryExists = DB::connection('zahoron_old')->table('columbariums')->where('id', $item->columbarium_id)->exists();

            // Если кладбище существует, добавляем запись
            if ($cemeteryExists) {
                DB::table('working_hours_columbaria')->insert([
                    'time_start_work' => $item->time_start_work ?? '00:00',
                    'time_end_work'   => $item->time_end_work ?? '00:00',
                    'holiday'         => $item->holiday ?? 0,
                    'columbarium_id'     => $item->columbarium_id,
                    'day'             => $item->day ?? 'Понедельник', // Указываем день по умолчанию
                    'created_at'      => $item->created_at ?? now(),
                    'updated_at'      => $item->updated_at ?? now(),
                ]);
            } else {
                // Логируем или выводим предупреждение, если cemetery_id отсутствует
            }
        }
    }
}
