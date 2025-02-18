<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $oldData = DB::connection('zahoron_old')->table('organizations')->get();

        foreach ($oldData as $item) {
            DB::table('organizations')->insert([
                'id'                    => $item->id,
                'user_id'               => $item->user_id ?? null,
                'title'                 => $item->title,
                'img_url'               => $item->img_url ?? null,
                'img_file'              => $item->img_file ?? null,
                'city_id'               => $item->city_id,
                'all_price'             => $item->all_price ?? null,
                'district_id'           => $item->district_id ?? null,
                'phone'                 => $item->phone ?? null,
                'adres'                 => $item->adres ?? null,
                'time_start_work'       => $item->time_start_work ?? null,
                'time_end_work'         => $item->time_end_work ?? null,
                'mini_content'          => $item->mini_content ?? null,
                'content'               => $item->content ?? null,
                'name_type'             => $item->name_type ?? null,
                'width'                 => $item->width ?? null,
                'longitude'             => $item->longitude ?? null,
                'available_installments'=> $item->available_installments ?? null,
                'found_cheaper'         => $item->found_cheaper ?? null,
                'conclusion_contract'   => $item->conclusion_contract ?? null,
                'state_compensation'    => $item->state_compensation ?? null,
                'status'                => $item->status ?? 1,
                'next_to'               => $item->next_to ?? null,
                'underground'           => $item->underground ?? null,
                'rating'                => $item->rating ?? null,
                'cemetery_ids'          => $item->cemetery_ids ?? null,
                'role'                  => $item->role ?? 'organization',
                'awards'                => $item->awards ?? null,
                'price_list'            => $item->price_list ?? null,
                'remains'               => $item->remains ?? null,
                'slug'                  => $item->slug,
                'href_img'              => $item->href_img ?? 0,
                'whatsapp'              => $item->whatsapp ?? null,
                'telegram'              => $item->telegram ?? null,
                'email'                 => $item->email ?? null,
                'village'               => $item->village ?? null,
                'time_difference'       => $item->time_difference ?? 0,
                'created_at'            => $item->created_at,
                'updated_at'            => $item->updated_at,
            ]);
        }
    }
}
