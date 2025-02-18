<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CemeterySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $oldData = DB::connection('zahoron_old')->table('cemeteries')->get();

        foreach ($oldData as $item) {
            DB::table('cemeteries')->insert([
                'id'=> $item->id,

                'title'                 => $item->title,
                'content'               => $item->content ,
                'img_url'               => $item->img_url ,
                'img_file'              => $item->img_file ,
                'adres'                 => $item->adres ,
                'city_id'               => $item->city_id , // Убедитесь, что город существует
                'area_id'               => $item->area_id , // Может отсутствовать
                'width'                 => $item->width ,
                'longitude'             => $item->longitude ,
                'rating'                => $item->rating ,
                'mini_content'          => $item->mini_content ,
                'characteristics'       => $item->characteristics ,
                'district_id'           => $item->district_id ,
                'underground'           => $item->underground ,
                'next_to'               => $item->next_to ,
                'price_decode'          => $item->price_decode ?? 0,
                'href_img'              => $item->href_img ?? 0,
                'village'               => $item->village ,
                'email'                 => $item->email ,
                'phone'                 => $item->phone ,
                'time_difference'       => $item->time_difference ?? 0,
                'square'                => $item->square ,
                'responsible'           => $item->responsible ,
                'cadastral_number'      => $item->cadastral_number ,
                'cost_sponsorship_call' => $item->cost_sponsorship_call ?? 1000,
                'price_burial_location' => $item->price_burial_location ?? 5900,
                'date_foundation'       => $item->date_foundation ,
                'created_at'            => $item->created_at ?? now(),
                'updated_at'            => $item->updated_at ?? now(),
            ]);
        }
    }
}
