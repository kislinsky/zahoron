<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryProductProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем старые данные из таблицы \areas\ в старой БД
        $oldData = DB::connection('zahoron_old')->table('category_product_providers')->get();

        // Переносим данные в новую таблицу
        foreach ($oldData as $item) {
            DB::table('category_product_providers')->insert([
                'id'           => $item->id, // Если нужно сохранить ID
                'title'        => $item->title, 
                'icon'         => $item->icon ?? null, // Проверяем, есть ли данные
                'icon_white'   => $item->icon_white ?? null,
                'parent_id'    => $item->parent_id ?? null,
                'choose_admin' => $item->choose_admin ?? 0, // Если отсутствует, ставим 0
                'created_at'   => $item->created_at ?? now(), 
                'updated_at'   => $item->updated_at ?? now(),
            ]);
        }
    }
}
