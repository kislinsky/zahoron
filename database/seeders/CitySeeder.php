<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $oldData = DB::connection('zahoron_old')->table('cities')->get();

        foreach ($oldData as $item) {
            DB::table('cities')->insert([
                'id'=> $item->id,

                'title'      => $item->title,
                'slug'       => $item->slug ?? '', // Если в старой таблице нет slug
                'edge_id'    => $item->edge_id ?? 1, // Убедитесь, что edge_id существует
                'area_id'    => $item->area_id ?? 1, // Аналогично для area_id
                'selected_admin' => $item->selected_admin ?? null,
                'selected_form'  => $item->selected_form ?? 0,
                'width'      => $item->width ?? null,
                'longitude'  => $item->longitude ?? null,
                'text_about_project' => $item->text_about_project ?? '...',
                'text_how_properly_arrange_funeral_services' => $item->text_how_properly_arrange_funeral_services ?? '...',
                'content_mortuary' => $item->content_mortuary ?? null,
                'created_at' => $item->created_at ?? now(),
                'updated_at' => $item->updated_at ?? now(),
            ]);
        }
    }
}
