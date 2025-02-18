<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivityCategoryOrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем данные из старой таблицы
        $oldData = DB::connection('zahoron_old')->table('activity_category_organizations')->get();

        // Переносим данные в новую таблицу activity_category_organizations
        foreach ($oldData as $item) {
            DB::table('activity_category_organizations')->insert([
                'id'                  => $item->id, // Если ID должен быть сохранен
                'organization_id'     => $item->organization_id, // ID организации
                'category_main_id'    => $item->category_main_id, // Основная категория
                'category_children_id'=> $item->category_children_id, // Подкатегория
                'price'               => $item->price ?? null, // Цена (если есть)
                'rating'              => $item->rating ?? null, // Рейтинг (если есть)

                // Преобразуем JSON-данные (если в старом формате они были в виде CSV-строки или JSON)
                'sales'               => json_encode($item->sales ?? []), 
                'cemetery_ids'        => json_encode($item->cemetery_ids ?? []), 
                'district_ids'        => json_encode($item->district_ids ?? []), 

                'created_at'          => $item->created_at ?? now(),
                'updated_at'          => $item->updated_at ?? now(),
            ]);
        }
    }
}
