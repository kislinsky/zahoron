<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PriceServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем данные из старой таблицы
        $oldData = DB::connection('zahoron_old')->table('price_services')->get();

        // Переносим данные в новую таблицу price_services
        foreach ($oldData as $item) {
            DB::table('price_services')->insert([
                'id'          => $item->id, // Если ID должен быть сохранен
                'cemetery_id' => $item->cemetery_id, // ID кладбища
                'service_id'  => $item->service_id, // ID услуги
                'price'       => $item->price ?? 0.0, // Цена (по умолчанию 0.0, если поле отсутствует)
                'created_at'  => $item->created_at ?? now(), // Текущее время, если поле отсутствует
                'updated_at'  => $item->updated_at ?? now(), // Текущее время, если поле отсутствует
            ]);
        }
    }
}
