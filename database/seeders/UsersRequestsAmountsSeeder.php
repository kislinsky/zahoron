<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersRequestsAmountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем данные из старой таблицы `areas`
        $oldData = DB::connection('zahoron_old')->table('user_request_amounts')->get();

        // Переносим данные в `user_request_amounts`
        foreach ($oldData as $item) {
            DB::table('user_request_amounts')->insert([
                'id'=> $item->id,

                'organization_id'     => $item->organization_id ?? null,
                'type_service_id'     => $item->type_service_id ?? 1, // Значение по умолчанию
                'type_application_id' => $item->type_application_id ?? 1, // Значение по умолчанию
                'price'               => $item->price ?? 0, // По умолчанию 0
                'created_at'          => $item->created_at ?? now(),
                'updated_at'          => $item->updated_at ?? now(),
            ]);
        }
    }
}
