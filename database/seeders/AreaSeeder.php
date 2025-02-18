<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $oldData = DB::connection('zahoron_old')->table('areas')->get();

        // Переносим данные в новую таблицу
        foreach ($oldData as $item) {
            DB::table('areas')->insert([
                'id'=> $item->id,
                'title'      => $item->title,
                'edge_id'    => $item->edge_id ?? 1, // Убедитесь, что edge_id не NULL
                'created_at' => $item->created_at ?? now(),
                'updated_at' => $item->updated_at ?? now(),
            ]);
        }
    }
}
