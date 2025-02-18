<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeoObjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $oldData = DB::connection('zahoron_old')->table('seo_objects')->get();

        foreach ($oldData as $item) {
            DB::table('seo_objects')->insert([
                'id'=> $item->id,
                'title'    => $item->title ?? 'Без названия',
                'ru_title' => $item->ru_title ?? $item->title ?? 'Без названия',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
