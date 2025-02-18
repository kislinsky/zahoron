<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkingHoursOrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем данные из старой таблицы `areas`
        $oldData = DB::connection('zahoron_old')->table('working_hours_organizations')->get();

        // Переносим данные в `working_hours_cemeteries`
        foreach ($oldData as $item) {
            // Проверяем существование cemetery_id
            $cemeteryExists = DB::connection('zahoron_old')->table('cemeteries')->where('id', $item->organization_id)->exists();

            // Если кладбище существует, добавляем запись
            if ($cemeteryExists) {
                DB::table('working_hours_organizations')->insert([
                    'time_start_work' => $item->time_start_work ?? '00:00',
                    'time_end_work'   => $item->time_end_work ?? '00:00',
                    'holiday'         => $item->holiday ?? 0,
                    'organization_id'     => $item->organization_id,
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
