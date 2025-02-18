<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\City;
use App\Models\Columbarium;
use App\Models\District;
use App\Models\Mortuary;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            EdgesSeeder::class,
            AreaSeeder::class,
            CitySeeder::class,
            DistrictSeeder::class,
            CemeterySeeder::class,
            ColumbariumSeeder::class,
            CrematoriumSeeder::class,
            MortuarySeeder::class,
            CategoryProductProviderSeeder::class,
            CategoryProductSeeder::class,
            CategoryServiceSeeder::class,
            WorkingHoursCemeterySeeder::class,
            WorkingHoursColumbariumSeeder::class,
            WorkingHoursCrematoriumSeeder::class,
            WorkingHoursMortuarySeeder::class,
            UsersSeeder::class,
            UsersRequestsCountsSeeder::class,
            UsersRequestsAmountsSeeder::class,
            TypeApplicationSeeder::class,
            TypeServiceSeeder::class,
            OrganizationSeeder::class,
            PagesSeeder::class,
            AcfsSeeder::class,
            ActivityCategoryOrganizationSeeder::class,
            ProductSeeder::class,
            ServiceSeeder::class,
            SeoObjectsSeeder::class,
            SeoSeeder::class,
            ImageAgenciesSeeder::class,
            ImageCatPriceListSeeder::class,
            ImageCemeteriesSeeder::class,
            ImageColumbariumSeeder::class,
            ImageCrematoriumSeeder::class,
            ImageMortuarySeeder::class,
            WorkingHoursCrematoriumSeeder::class,
            WorkingHoursMortuarySeeder::class,
            ImageOrganizationSeeder::class,
            ImageProductsSeeder::class,
            ImagesAgentSeeder::class,
            ImageServicesSeeder::class,
            ImagesSeeder::class,
            BurialSeeder::class,
            PriceServicesSeeder::class,
        ]);
    //     City::factory(500)->create();


    }
}
