<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем данные из старой таблицы \areas\
        $oldData = DB::connection('zahoron_old')->table('users')->get();

        // Переносим данные в \users\
        foreach ($oldData as $item) {
            DB::table('users')->insert([
                'id'=> $item->id,

                'name'                => $item->name ?? 'Без имени',
                'surname'             => $item->surname ?? null,
                'patronymic'          => $item->patronymic ?? null,
                'email'               => $item->email, // Создаём email, если отсутствует
                'email_verified_at'   => now(),
                'password'            => $item->password, // Генерация пароля
                'role'                => $item->role ?? 'user',
                'theme'               => $item->theme ?? 'light',
                'phone'               => $item->phone ?? null,
                'adres'               => $item->adres ?? null,
                'whatsapp'            => $item->whatsapp ?? null,
                'telegram'            => $item->telegram ?? null,
                'language'            => $item->language ?? 'ru',
                'sms_notifications'   => $item->sms_notifications ?? 1,
                'email_notifications' => $item->email_notifications ?? 1,
                'inn'                 => $item->inn ?? null,
                'uploading_signature' => $item->uploading_signature ?? null,
                'number_cart'         => $item->number_cart ?? null,
                'bank'                => $item->bank ?? null,
                'cemetery_ids'        => $item->cemetery_ids ?? null,
                'ogrn'                => $item->ogrn ?? null,
                'icon'                => $item->icon ?? null,
                'organization_id'     => $item->organization_id ?? null,
                'organizational_form' => $item->organizational_form ?? 'ep',
                'name_organization'   => $item->name_organization ?? null,
                'edge_id'             => $item->edge_id ?? null,
                'city_id'             => $item->city_id ?? null,
                'in_face'             => $item->in_face ?? null,
                'regulation'          => $item->regulation ?? null,
                'status'              => $item->status ?? 1,
                'remember_token'      => $item->remember_token,
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);
        }
    }
}
