<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BurialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем старые данные (замените \old_burials\ на реальное название таблицы в старой БД)
        $oldData = DB::connection('zahoron_old')->table('burials')->get();

        // Переносим данные в новую таблицу \burials\
        foreach ($oldData as $item) {
            DB::table('burials')->insert([
                'id'              => $item->id, // Если нужно сохранить ID
                'name'            => $item->name ?? null, 
                'surname'         => $item->surname ?? null,
                'patronymic'      => $item->patronymic ?? null,
                'who'             => $item->who ?? 'Гражданский',
                'date_death'      => $item->date_death ?? null,
                'date_birth'      => $item->date_birth ?? null,
                'location_death'  => $item->location_death ?? null,
                'img'             => $item->img ?? null,
                'img_original'    => $item->img_original ?? null,
                'information'     => $item->information ?? null,
                'width'           => $item->width ?? null,
                'longitude'       => $item->longitude ?? null,
                
                'cemetery_id'     => $item->cemetery_id ?? null, // Проверяем существование кладбища
                'slug'            => $item->slug , // Генерируем \slug\
                'status'          => $item->status ?? 1,
                'photographer'    => $item->photographer ?? null,
                'comment'         => $item->comment ?? null,
                'decoder_id'      => $item->decoder_id ?? null, // Проверяем существование пользователя
                'href_img'        => $item->href_img ?? 0,
                'agent_id'        => $item->agent_id ?? null, // Проверяем существование агента

                'created_at'      => $item->created_at ?? now(), 
                'updated_at'      => $item->updated_at ?? now(),
            ]);
        }
    }
}
