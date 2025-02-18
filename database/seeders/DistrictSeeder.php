<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем данные из старой таблицы areas
        $oldData = DB::connection('zahoron_old')->table('districts')->get();

        // Переносим данные в новую таблицу districts
        foreach ($oldData as $item) {
            DB::table('districts')->insert([
                
                'id'         => $item->id, // Если id должен быть сохранен
                'title'      => $item->title,
                'city_id'    => $item->city_id ?? 1, // Если NULL, подставляем 1 или другой дефолтный город
                'created_at' => $item->created_at ?? now(),
                'updated_at' => $item->updated_at ?? now(),
            ]);
        }
    }
}
