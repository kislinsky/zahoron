<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImageCatPriceListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем данные из старой таблицы `areas`
        $oldData = DB::connection('zahoron_old')->table('image_cat_price_lists')->get();

        // Переносим данные в новую таблицу `image_cat_price_lists`
        foreach ($oldData as $item) {
            DB::table('image_cat_price_lists')->insert([
                'id'          => $item->id, // Если ID должен сохраняться (уберите, если автоинкремент)
                'img_before'  => '', // Укажите путь к изображению (если нет данных, оставьте пустым)
                'img_after'   => '', // Укажите путь к изображению (если нет данных, оставьте пустым)
                'category_id' => $item->category_id, // Здесь укажите актуальный `category_id` (замените на реальное значение)
                'created_at'  => $item->created_at ?? now(),
                'updated_at'  => $item->updated_at ?? now(),
            ]);
        }
    }
}
