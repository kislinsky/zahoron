<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImageProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем данные из старой таблицы \areas\
        $oldData = DB::connection('zahoron_old')->table('image_products')->get();

        // Переносим данные в \image_products\
        foreach ($oldData as $item) {
            DB::table('image_products')->insert([
                'id'         => $item->id, // Если ID должен сохраняться (уберите, если автоинкремент)
                'title'       => $item->title, // Если title отсутствует, добавляем заглушку
                'selected'   => 0, // Значение по умолчанию, т.к. в \areas\ его нет
                'product_id' => $item->product_id , // Укажите корректный ID товара (см. "ВАЖНО" ниже)
                'created_at' => $item->created_at ?? now(),
                'updated_at' => $item->updated_at ?? now(),
            ]);
        }
    }
}
