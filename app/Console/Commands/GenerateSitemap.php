<?php

namespace App\Console\Commands;

use App\Models\Burial; // Модель для захоронений
use App\Models\Cemetery; // Модель для кладбищ
use App\Models\City; // Модель для городов
use App\Models\Columbarium; // Модель для колумбариев
use App\Models\Crematorium; // Модель для крематориев
use App\Models\Mortuary; // Модель для моргов
use App\Models\News; // Модель для новостей
use App\Models\Organization; // Модель для организаций
use App\Models\PriceList; // Модель для прайс-листов
use App\Models\Product; // Модель для продуктов
use App\Models\ProductPriceList;
use App\Models\Service; // Модель для услуг
use Carbon\Carbon;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate the sitemap.';

    public function handle()
    {
        $sitemap = Sitemap::create();

        // Добавляем статические страницы
        $this->addStaticPages($sitemap);

        // Добавляем динамические страницы
        $this->addDynamicRoutes($sitemap);

        // Записываем карту сайта в файл
        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap успешно создан!');
    }

    protected function addStaticPages(Sitemap $sitemap)
    {
        $sitemap->add(Url::create('/')
            ->setLastModificationDate(now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY)
            ->setPriority(1.0));

        $sitemap->add(Url::create('/speczialist')
            ->setLastModificationDate(now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
            ->setPriority(0.8));

        $sitemap->add(Url::create('/kontakty')
            ->setLastModificationDate(now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
            ->setPriority(0.8));

        $sitemap->add(Url::create('/terms-of-use')
            ->setLastModificationDate(now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY)
            ->setPriority(0.7));

        $sitemap->add(Url::create('/our-works')
            ->setLastModificationDate(now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
            ->setPriority(0.8));
    }

    protected function addDynamicRoutes(Sitemap $sitemap)
    {
        // Получаем все города из базы данных
        $cities = City::all();

        foreach ($cities as $city) {
            $citySlug = $city->slug; // Предполагаем, что у модели City есть поле slug

            // Добавляем маршруты для каждого города
            $this->addOrganizationRoutes($sitemap, $citySlug);
            $this->addProductRoutes($sitemap, $citySlug);
            $this->addNewsRoutes($sitemap, $citySlug);
            $this->addCemeteryRoutes($sitemap, $citySlug);
            $this->addMortuaryRoutes($sitemap, $citySlug);
            $this->addCrematoriumRoutes($sitemap, $citySlug);
            $this->addColumbariumRoutes($sitemap, $citySlug);
            $this->addBurialRoutes($sitemap, $citySlug);
            $this->addServiceRoutes($sitemap, $citySlug);
            $this->addPriceListRoutes($sitemap, $citySlug);
        }
    }

    protected function addOrganizationRoutes(Sitemap $sitemap, $citySlug)
    {
        $sitemap->add(Url::create("/{$citySlug}/organizations")
            ->setLastModificationDate(now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
            ->setPriority(0.9));

        // Добавляем маршруты для отдельных организаций
        $organizations = Organization::where('city_id', City::where('slug', $citySlug)->first()->id)->get();
        foreach ($organizations as $organization) {
            $sitemap->add(Url::create("/{$citySlug}/organization/{$organization->slug}")
                ->setLastModificationDate($organization->updated_at)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                ->setPriority(0.8));
        }
    }

    protected function addProductRoutes(Sitemap $sitemap, $citySlug)
    {
        $sitemap->add(Url::create("/{$citySlug}/marketplace")
            ->setLastModificationDate(now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
            ->setPriority(0.9));

        // Добавляем маршруты для отдельных продуктов
        $products = Product::where('city_id', City::where('slug', $citySlug)->first()->id)->get();
        foreach ($products as $product) {
            $sitemap->add(Url::create("/{$citySlug}/product/{$product->slug}")
                ->setLastModificationDate($product->updated_at)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                ->setPriority(0.8));
        }
    }

    protected function addNewsRoutes(Sitemap $sitemap, $citySlug)
    {
        $sitemap->add(Url::create("/{$citySlug}/news")
            ->setLastModificationDate(now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
            ->setPriority(0.8));

        // Добавляем маршруты для отдельных новостей
        $news = News::all();
        foreach ($news as $newsItem) {
            $sitemap->add(Url::create("/{$citySlug}/news/{$newsItem->slug}")
                ->setLastModificationDate($newsItem->updated_at)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                ->setPriority(0.7));
        }
    }

    protected function addCemeteryRoutes(Sitemap $sitemap, $citySlug)
    {
        $sitemap->add(Url::create("/{$citySlug}/cemeteries")
            ->setLastModificationDate(now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
            ->setPriority(0.8));

        // Добавляем маршруты для отдельных кладбищ
        $cemeteries = Cemetery::where('city_id', City::where('slug', $citySlug)->first()->id)->get();
        foreach ($cemeteries as $cemetery) {
            $sitemap->add(Url::create("/{$citySlug}/cemetery/{$cemetery->id}")
                ->setLastModificationDate($cemetery->updated_at)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                ->setPriority(0.7));
        }
    }

    protected function addMortuaryRoutes(Sitemap $sitemap, $citySlug)
    {
        $sitemap->add(Url::create("/{$citySlug}/mortuaries")
            ->setLastModificationDate(now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
            ->setPriority(0.8));

        // Добавляем маршруты для отдельных моргов
        $mortuaries = Mortuary::where('city_id', City::where('slug', $citySlug)->first()->id)->get();
        foreach ($mortuaries as $mortuary) {
            $sitemap->add(Url::create("/{$citySlug}/mortuary/{$mortuary->id}")
                ->setLastModificationDate($mortuary->updated_at)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                ->setPriority(0.7));
        }
    }

    protected function addCrematoriumRoutes(Sitemap $sitemap, $citySlug)
    {
        $sitemap->add(Url::create("/{$citySlug}/crematoriums")
            ->setLastModificationDate(now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
            ->setPriority(0.8));

        // Добавляем маршруты для отдельных крематориев
        $crematoriums = Crematorium::where('city_id', City::where('slug', $citySlug)->first()->id)->get();
        foreach ($crematoriums as $crematorium) {
            $sitemap->add(Url::create("/{$citySlug}/crematorium/{$crematorium->id}")
                ->setLastModificationDate($crematorium->updated_at)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                ->setPriority(0.7));
        }
    }

    protected function addColumbariumRoutes(Sitemap $sitemap, $citySlug)
    {
        $sitemap->add(Url::create("/{$citySlug}/columbariums")
            ->setLastModificationDate(now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
            ->setPriority(0.8));

        // Добавляем маршруты для отдельных колумбариев
        $columbariums = Columbarium::where('city_id', City::where('slug', $citySlug)->first()->id)->get();
        foreach ($columbariums as $columbarium) {
            $sitemap->add(Url::create("/{$citySlug}/columbarium/{$columbarium->id}")
                ->setLastModificationDate($columbarium->updated_at)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                ->setPriority(0.7));
        }
    }

    protected function addBurialRoutes(Sitemap $sitemap, $citySlug)
    {
        $sitemap->add(Url::create("/{$citySlug}/burial")
            ->setLastModificationDate(now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
            ->setPriority(0.9));

        // Добавляем маршруты для отдельных захоронений
        $burials = Burial::whereIn('cemetery_id', City::where('slug', $citySlug)->first()->cemeteries->pluck('id'))->get();
        foreach ($burials as $burial) {
            $sitemap->add(Url::create("/{$citySlug}/burial/{$burial->slug}")
                ->setLastModificationDate($burial->updated_at)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                ->setPriority(0.8));
        }
    }

    protected function addServiceRoutes(Sitemap $sitemap, $citySlug)
    {
        $sitemap->add(Url::create("/{$citySlug}/service")
            ->setLastModificationDate(now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
            ->setPriority(0.9));

        // Добавляем маршруты для отдельных услуг
        $services = Service::all();
        foreach ($services as $service) {
            $sitemap->add(Url::create("/{$citySlug}/service/{$service->id}")
                ->setLastModificationDate($service->updated_at)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                ->setPriority(0.8));
        }
    }

    protected function addPriceListRoutes(Sitemap $sitemap, $citySlug)
    {
        $sitemap->add(Url::create("/{$citySlug}/price-list")
            ->setLastModificationDate(now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
            ->setPriority(0.8));

        // Добавляем маршруты для отдельных категорий услуг
        $priceLists = ProductPriceList::all();
        foreach ($priceLists as $priceList) {
            $sitemap->add(Url::create("/{$citySlug}/price-list/{$priceList->slug}")
                ->setLastModificationDate($priceList->updated_at)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                ->setPriority(0.7));
        }
    }

  

}