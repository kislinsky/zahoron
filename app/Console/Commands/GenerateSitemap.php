<?php

namespace App\Console\Commands;

use App\Models\Cemetery;
use App\Models\City;
use App\Models\Columbarium;
use App\Models\Crematorium;
use App\Models\Mortuary;
use App\Models\Organization;
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

        // Добавляем динамические маршруты
        $this->addDynamicRoutes($sitemap);

        // Записываем карту сайта в файл
        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap успешно создан!');
    }

    protected function addStaticPages(Sitemap $sitemap)
    {
        // Главная страница
        $sitemap->add(Url::create('/')
            ->setLastModificationDate(now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY)
            ->setPriority(1.0));

        // Специалисты
        $sitemap->add(Url::create('/speczialist')
            ->setLastModificationDate(now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
            ->setPriority(0.8));

        // Контакты
        $sitemap->add(Url::create('/kontakty')
            ->setLastModificationDate(now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
            ->setPriority(0.8));

        // Условия использования
        $sitemap->add(Url::create('/terms-of-use')
            ->setLastModificationDate(now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY)
            ->setPriority(0.7));

        // Наши работы
        $sitemap->add(Url::create('/our-works')
            ->setLastModificationDate(now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
            ->setPriority(0.8));
    }

    protected function addDynamicRoutes(Sitemap $sitemap)
    {
        // Добавляем все организации с их городами
        $this->addOrganizations($sitemap);
        
        // Добавляем все кладбища с их городами
        $this->addCemeteries($sitemap);
        
        // Добавляем все морги с их городами
        $this->addMortuaries($sitemap);
        
        // Добавляем все крематории с их городами
        $this->addCrematoriums($sitemap);
        
        // Добавляем все колумбарии с их городами
        $this->addColumbariums($sitemap);
    }

    protected function addOrganizations(Sitemap $sitemap)
    {
        // Главная страница организаций
        $sitemap->add(Url::create('/organizations')
            ->setLastModificationDate(now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
            ->setPriority(0.8));

        // Все организации с их городами
        $organizations = Organization::with('city')->get();
        foreach ($organizations as $organization) {
            if ($organization->city) {
                $sitemap->add(Url::create("/{$organization->city->slug}/organization/{$organization->slug}")
                    ->setLastModificationDate($organization->updated_at)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    ->setPriority(0.7));
            }
        }
    }

    protected function addCemeteries(Sitemap $sitemap)
    {
        // Главная страница кладбищ
        $sitemap->add(Url::create('/cemeteries')
            ->setLastModificationDate(now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
            ->setPriority(0.8));

        // Все кладбища с их городами
        $cemeteries = Cemetery::with('city')->get();
        foreach ($cemeteries as $cemetery) {
            if ($cemetery->city) {
                $sitemap->add(Url::create("/{$cemetery->city->slug}/cemetery/{$cemetery->id}")
                    ->setLastModificationDate($cemetery->updated_at)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    ->setPriority(0.7));
            }
        }
    }

    protected function addMortuaries(Sitemap $sitemap)
    {
        // Главная страница моргов
        $sitemap->add(Url::create('/mortuaries')
            ->setLastModificationDate(now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
            ->setPriority(0.8));

        // Все морги с их городами
        $mortuaries = Mortuary::with('city')->get();
        foreach ($mortuaries as $mortuary) {
            if ($mortuary->city) {
                $sitemap->add(Url::create("/{$mortuary->city->slug}/mortuary/{$mortuary->id}")
                    ->setLastModificationDate($mortuary->updated_at)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    ->setPriority(0.7));
            }
        }
    }

    protected function addCrematoriums(Sitemap $sitemap)
    {
        // Главная страница крематориев
        $sitemap->add(Url::create('/crematoriums')
            ->setLastModificationDate(now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
            ->setPriority(0.8));

        // Все крематории с их городами
        $crematoriums = Crematorium::with('city')->get();
        foreach ($crematoriums as $crematorium) {
            if ($crematorium->city) {
                $sitemap->add(Url::create("/{$crematorium->city->slug}/crematorium/{$crematorium->id}")
                    ->setLastModificationDate($crematorium->updated_at)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    ->setPriority(0.7));
            }
        }
    }

    protected function addColumbariums(Sitemap $sitemap)
    {
        // Главная страница колумбариев
        $sitemap->add(Url::create('/columbariums')
            ->setLastModificationDate(now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
            ->setPriority(0.8));

        // Все колумбарии с их городами
        $columbariums = Columbarium::with('city')->get();
        foreach ($columbariums as $columbarium) {
            if ($columbarium->city) {
                $sitemap->add(Url::create("/{$columbarium->city->slug}/columbarium/{$columbarium->id}")
                    ->setLastModificationDate($columbarium->updated_at)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    ->setPriority(0.7));
            }
        }
    }
}