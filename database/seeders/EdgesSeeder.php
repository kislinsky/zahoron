<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EdgesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $oldData = DB::connection('zahoron_old')->table('edges')->get();

        // Переносим данные в новую таблицу
        foreach ($oldData as $item) {
            DB::table('edges')->insert([
                'id'         => $item->id, // Если id должен быть сохранен
                'title'      => $item->title,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
            ]);
        }
    }
}
