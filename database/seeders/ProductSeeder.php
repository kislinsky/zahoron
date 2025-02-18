<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        $oldData = DB::connection('zahoron_old')->table('products')->get();

        foreach ($oldData as $item) {
            DB::table('products')->insert([
                'id'                 => $item->id, // Сохранение старого ID
                'title'              => $item->title,
                'category_id'        => $item->category_id ?? 1, // Если есть соответствующее поле
                'category_parent_id' => $item->category_parent_id ?? 0,
                'size'               => $item->size ?? '',
                'content'            => $item->content ?? '',
                'material'           => $item->material ?? '',
                'color'              => $item->color ?? 'Не указано',
                'status'             => $item->status ?? 'active',
                'price'              => $item->price ?? 0,
                'price_sale'         => $item->price_sale ?? 0,
                'total_price'        => $item->total_price ?? 0,
                'city_id'            => $item->city_id ?? 1,
                'title_institution'  => $item->title_institution ?? '',
                'organization_id'    => $item->organization_id ?? 1,
                'capacity'           => $item->capacity ?? 0,
                'location_width'     => $item->location_width ?? '0.000000',
                'location_longitude' => $item->location_longitude ?? '0.000000',
                'district_id'        => $item->district_id ?? 1,
                'type'               => $item->type ?? 'product',
                'provider_id'        => $item->provider_id ?? 1,
                'layering'           => $item->layering ?? '',
                'cafe'               => $item->cafe ?? '',
                'count_people'       => $item->count_people ?? 0,
                'slug'               => $item->slug ?? '',
                'created_at'         => $item->created_at,
                'updated_at'         => $item->updated_at,
            ]);
        }
    }
}
